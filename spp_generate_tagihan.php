<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'admin' && $role !== 'bendahara') {
    die("Akses ditolak. Halaman ini khusus Administrator dan Bendahara.");
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Fungsi bantu kirim WhatsApp (Fonente / KirimWA API)
function sendWaNotification($no_hp, $pesan) {
    if (empty($no_hp)) return false;
    $no_hp = preg_replace('/[^0-9]/', '', $no_hp);
    if (substr($no_hp, 0, 1) === '0') {
        $no_hp = '62' . substr($no_hp, 1);
    }
    // Jika ada sistem WhatsApp Gateway terkonfigurasi, panggil API pengirim
    // Contoh dummy log pengiriman:
    return true;
}

// --- PROSES GENERATE TAGIHAN ---
$result_info = null;

if (isset($_POST['generate'])) {
    $jenis_tagihan_id = (int)$_POST['jenis_tagihan_id'];
    $target_siswa     = $_POST['target_siswa']; // 'semua', 'kelas', 'nis'
    $kelas_pilih      = $_POST['kelas'] ?? '';
    $nis_pilih        = trim($_POST['nis'] ?? '');
    $kirim_wa         = isset($_POST['kirim_wa']) ? 1 : 0;

    // Ambil data jenis tagihan
    $stmt_jt = $conn->prepare("SELECT * FROM spp_jenis_tagihan WHERE id=?");
    $stmt_jt->bind_param("i", $jenis_tagihan_id);
    $stmt_jt->execute();
    $jt = $stmt_jt->get_result()->fetch_assoc();

    if (!$jt) {
        $result_info = ['status' => 'error', 'pesan' => 'Jenis tagihan tidak ditemukan.'];
    } else {
        // Query ambil daftar siswa target
        $sql_siswa = "SELECT nis, nama, no_hp_ortu AS no_hp, kelas FROM siswa WHERE 1=1";
        if ($target_siswa === 'kelas' && !empty($kelas_pilih)) {
            $sql_siswa .= " AND kelas = '" . mysqli_real_escape_string($conn, $kelas_pilih) . "'";
        } elseif ($target_siswa === 'nis' && !empty($nis_pilih)) {
            $sql_siswa .= " AND nis = '" . mysqli_real_escape_string($conn, $nis_pilih) . "'";
        }

        $q_siswa = mysqli_query($conn, $sql_siswa);

        // Ambil template WA & Pengaturan
        $q_setting = mysqli_query($conn, "SELECT * FROM spp_pengaturan LIMIT 1");
        $setting = mysqli_fetch_assoc($q_setting);
        $wa_template = $setting['wa_template_tagihan'] ?? "Tagihan SPP {nama_tagihan} Rp {nominal}. Link: {link_portal}";

        $created = 0;
        $skipped = 0;

        // Base URL untuk portal ortu
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        while ($s = mysqli_fetch_assoc($q_siswa)) {
            $nis = $s['nis'];

            // Cek apakah tagihan jenis ini sudah dibuat untuk siswa ini
            $stmt_cek = $conn->prepare("SELECT id FROM spp_tagihan WHERE nis=? AND jenis_tagihan_id=?");
            $stmt_cek->bind_param("si", $nis, $jenis_tagihan_id);
            $stmt_cek->execute();
            if ($stmt_cek->get_result()->num_rows > 0) {
                $skipped++;
                continue;
            }

            // Generate Token Unik Ortu (32 byte hex = 64 karakter)
            $token = bin2hex(random_bytes(32));

            // Simpan tagihan siswa
            $nominal = $jt['nominal'];
            $sisa = $nominal;
            $status = 'Belum Bayar';

            $stmt_ins = $conn->prepare("INSERT INTO spp_tagihan (nis, jenis_tagihan_id, nominal, dibayar, sisa, status, token) VALUES (?, ?, ?, 0, ?, ?, ?)");
            $stmt_ins->bind_param("siddss", $nis, $jenis_tagihan_id, $nominal, $sisa, $status, $token);
            if ($stmt_ins->execute()) {
                $created++;
                $tagihan_id = $conn->insert_id;

                // Jika Opsi Kirim WA dicentang
                if ($kirim_wa && !empty($s['no_hp'])) {
                    $link_portal = $base_url . '/spp_portal.php?token=' . $token;

                    $pesan = str_replace(
                        ['{nama_siswa}', '{kelas}', '{nama_tagihan}', '{nominal}', '{jatuh_tempo}', '{link_portal}'],
                        [$s['nama'], $s['kelas'], $jt['nama'], number_format($nominal, 0, ',', '.'), date('d M Y', strtotime($jt['jatuh_tempo'])), $link_portal],
                        $wa_template
                    );

                    sendWaNotification($s['no_hp'], $pesan);

                    // Catat ke log notifikasi
                    $stmt_log = $conn->prepare("INSERT INTO spp_log_notifikasi (tagihan_id, no_hp, pesan) VALUES (?, ?, ?)");
                    $stmt_log->bind_param("iss", $tagihan_id, $s['no_hp'], $pesan);
                    $stmt_log->execute();
                }
            }
        }

        $result_info = [
            'status' => 'success',
            'pesan' => "Proses Selesai! Berhasil membuat <strong>$created</strong> tagihan baru ($skipped siswa dilewati karena sudah memiliki tagihan ini)."
        ];
    }
}

// Option daftar jenis tagihan aktif
$q_jt_list = mysqli_query($conn, "SELECT * FROM spp_jenis_tagihan WHERE aktif=1 ORDER BY id DESC");

// Option daftar kelas
$q_kelas_list = mysqli_query($conn, "SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Tagihan SPP Massal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 2rem; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-magic me-2 text-primary"></i>Generate Tagihan SPP</h4>
            <p class="text-muted small mb-0">Terbitkan tagihan pembayaran secara otomatis untuk seluruh siswa atau per kelas</p>
        </div>
        <a href="spp_data_tagihan.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Data Tagihan
        </a>
    </div>

    <?php if ($result_info): ?>
        <div class="alert alert-<?= $result_info['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show rounded-4 p-3 mb-4">
            <?= $result_info['pesan'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-custom shadow-sm">
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold">1. Pilih Master Jenis Tagihan</label>
                        <select name="jenis_tagihan_id" class="form-select form-select-lg" required>
                            <option value="">-- Pilih Jenis Tagihan --</option>
                            <?php while ($jt_item = mysqli_fetch_assoc($q_jt_list)): ?>
                            <option value="<?= $jt_item['id'] ?>">
                                <?= xss($jt_item['nama']) ?> — Rp <?= number_format($jt_item['nominal'], 0, ',', '.') ?> (Jatuh Tempo: <?= date('d M Y', strtotime($jt_item['jatuh_tempo'])) ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">2. Sasaran Target Siswa</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_siswa" id="t1" value="semua" checked onclick="toggleTarget('semua')">
                                <label class="form-check-label fw-semibold" for="t1">Seluruh Siswa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_siswa" id="t2" value="kelas" onclick="toggleTarget('kelas')">
                                <label class="form-check-label fw-semibold" for="t2">Per Kelas / Lembaga</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_siswa" id="t3" value="nis" onclick="toggleTarget('nis')">
                                <label class="form-check-label fw-semibold" for="t3">Per Siswa Specific (NIS)</label>
                            </div>
                        </div>

                        <!-- Dropdown Kelas -->
                        <div id="target-kelas-box" style="display:none;" class="mb-3">
                            <label class="small text-muted mb-1 fw-bold">Pilih Lembaga / Kelas</label>
                            <select name="kelas" class="form-select">
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($kls = mysqli_fetch_assoc($q_kelas_list)): ?>
                                <option value="<?= xss($kls['nama_kelas']) ?>"><?= xss($kls['nama_kelas']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Input NIS Specific -->
                        <div id="target-nis-box" style="display:none;" class="mb-3">
                            <label class="small text-muted mb-1 fw-bold">Ketik NIS Siswa</label>
                            <input type="text" name="nis" class="form-control" placeholder="Misal: 40012">
                        </div>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-4 border">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="kirim_wa" id="kirim_wa" value="1" checked>
                            <label class="form-check-label fw-bold" for="kirim_wa">
                                <i class="bi bi-whatsapp text-success me-1"></i> Kirim Notifikasi WhatsApp Otomatis
                            </label>
                            <div class="small text-muted mt-1">Mengirim pesan WhatsApp pengirim tagihan beserta link token pembayaran unik ke orang tua murid.</div>
                        </div>
                    </div>

                    <button type="submit" name="generate" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold" onclick="return confirm('Apakah Anda yakin ingin memproses generate tagihan ini?')">
                        <i class="bi bi-play-circle-fill me-2"></i> Proses Generate Tagihan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTarget(val) {
    document.getElementById('target-kelas-box').style.display = (val === 'kelas') ? 'block' : 'none';
    document.getElementById('target-nis-box').style.display = (val === 'nis') ? 'block' : 'none';
}
</script>

</body>
</html>
