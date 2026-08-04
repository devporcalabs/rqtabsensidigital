<?php
include 'koneksi.php';

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

$token = trim($_GET['token'] ?? '');
if (empty($token)) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h2>Tautan Tidak Valid</h2><p>Silakan periksa kembali tautan pembayaran yang diberikan oleh sekolah.</p></div>");
}

// Ambil data tagihan berdasarkan token
$stmt = $conn->prepare("SELECT t.*, s.nama as nama_siswa, s.nis, s.kelas, jt.nama as nama_tagihan, jt.jatuh_tempo, jt.tahun_ajaran
                        FROM spp_tagihan t
                        JOIN siswa s ON t.nis = s.nis
                        JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id = jt.id
                        WHERE t.token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$tagihan = $stmt->get_result()->fetch_assoc();

if (!$tagihan) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h2>Tagihan Tidak Ditemukan</h2><p>Token tagihan tidak valid atau telah kadaluarsa.</p></div>");
}

// Ambil data sekolah & pengaturan pembayaran
$q_set = mysqli_query($conn, "SELECT * FROM setting LIMIT 1");
$sekolah = mysqli_fetch_assoc($q_set);
$nama_sekolah = $sekolah['nama_sekolah'] ?? 'Rumah Qur\'an Temi';
$logo_sekolah = $sekolah['logo_sekolah'] ?? 'porcalabs.ico';

$q_spp_set = mysqli_query($conn, "SELECT * FROM spp_pengaturan LIMIT 1");
$spp_set = mysqli_fetch_assoc($q_spp_set);

// --- PROSES UPLOAD BUKTI TRANSFER ---
$upload_info = null;

if (isset($_POST['upload_bukti'])) {
    $nominal_bayar = (float)$_POST['nominal_bayar'];
    $catatan       = trim($_POST['catatan'] ?? '');

    if ($nominal_bayar <= 0) {
        $upload_info = ['status' => 'danger', 'pesan' => 'Nominal konfirmasi bayar harus lebih dari 0.'];
    } elseif (!isset($_FILES['bukti_transfer']) || $_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK) {
        $upload_info = ['status' => 'danger', 'pesan' => 'Silakan pilih foto / file bukti transfer terlebih dahulu.'];
    } else {
        $file_tmp = $_FILES['bukti_transfer']['tmp_name'];
        $file_name = $_FILES['bukti_transfer']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($ext, $allowed)) {
            $upload_info = ['status' => 'danger', 'pesan' => 'Format file tidak didukung! Gunakan format JPG, PNG, atau PDF.'];
        } else {
            // Buat direktori folder img/bukti_spp jika belum ada
            $dir_target = __DIR__ . '/img/bukti_spp/';
            if (!is_dir($dir_target)) {
                mkdir($dir_target, 0777, true);
            }

            $nama_baru = 'bukti_' . date('Ymd_His') . '_' . rand(100, 999) . '.' . $ext;
            $dest = $dir_target . $nama_baru;

            if (move_uploaded_file($file_tmp, $dest)) {
                $no_trx = 'TRF-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));
                $metode = $_POST['metode'] ?? 'Transfer';

                $stmt_p = $conn->prepare("INSERT INTO spp_pembayaran (nomor_transaksi, tagihan_id, nis, metode, nominal, bukti_transfer, status_verifikasi, catatan) VALUES (?, ?, ?, ?, ?, ?, 'Pending', ?)");
                $stmt_p->bind_param("sissdss", $no_trx, $tagihan['id'], $tagihan['nis'], $metode, $nominal_bayar, $nama_baru, $catatan);
                $stmt_p->execute();

                $upload_info = [
                    'status' => 'success',
                    'pesan' => 'Alhamdulillah! Bukti konfirmasi pembayaran berhasil dikirim. Petugas / Bendahara akan memverifikasi pembayaran Anda.'
                ];
            } else {
                $upload_info = ['status' => 'danger', 'pesan' => 'Gagal mengunggah file ke server. Coba lagi.'];
            }
        }
    }
}

// Ambil riwayat pembayaran tagihan ini
$stmt_history = $conn->prepare("SELECT * FROM spp_pembayaran WHERE tagihan_id=? ORDER BY id DESC");
$stmt_history->bind_param("i", $tagihan['id']);
$stmt_history->execute();
$q_history = $stmt_history->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Tagihan SPP — <?= xss($tagihan['nama_siswa']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f3f9; color: #1e293b; min-height: 100vh; }
        .portal-card { background: #ffffff; border-radius: 24px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .header-bg { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; border-radius: 24px 24px 0 0; padding: 2rem; }
        .status-badge-lunas { background: #dcfce7; color: #15803d; font-weight: 800; border-radius: 50px; padding: 8px 24px; }
        .status-badge-belum { background: #fee2e2; color: #b91c1c; font-weight: 800; border-radius: 50px; padding: 8px 24px; }
        .status-badge-sebagian { background: #fef3c7; color: #b45309; font-weight: 800; border-radius: 50px; padding: 8px 24px; }
    </style>
</head>
<body>

<div class="container py-4 col-md-8 col-lg-6">
    <div class="portal-card overflow-hidden">
        <!-- Header Portal -->
        <div class="header-bg text-center position-relative">
            <img src="img/<?= xss($logo_sekolah) ?>" height="60" class="bg-white p-2 rounded-circle mb-2 shadow">
            <h5 class="fw-bold mb-1"><?= xss($nama_sekolah) ?></h5>
            <p class="small text-white-50 mb-0">Portal Pembayaran SPP Digital</p>
        </div>

        <div class="p-4">
            <?php if ($upload_info): ?>
                <div class="alert alert-<?= $upload_info['status'] ?> rounded-4 mb-4">
                    <?= $upload_info['pesan'] ?>
                </div>
            <?php endif; ?>

            <!-- Informasi Siswa -->
            <div class="p-3 bg-light rounded-4 mb-4">
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted d-block">Nama Siswa</small>
                        <strong class="text-dark fs-6"><?= xss($tagihan['nama_siswa']) ?></strong>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted d-block">NIS / Kelas</small>
                        <strong class="text-dark fs-6"><?= xss($tagihan['nis']) ?> • <?= xss($tagihan['kelas']) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Detail Tagihan -->
            <div class="border rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted d-block">Jenis Tagihan</small>
                        <h5 class="fw-bold text-primary mb-0"><?= xss($tagihan['nama_tagihan']) ?></h5>
                    </div>
                    <div>
                        <?php if ($tagihan['status'] == 'Lunas'): ?>
                            <span class="status-badge-lunas"><i class="bi bi-check-circle-fill me-1"></i> LUNAS</span>
                        <?php elseif ($tagihan['status'] == 'Sebagian'): ?>
                            <span class="status-badge-sebagian"><i class="bi bi-pie-chart-fill me-1"></i> SEBAGIAN</span>
                        <?php else: ?>
                            <span class="status-badge-belum"><i class="bi bi-exclamation-triangle-fill me-1"></i> BELUM BAYAR</span>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-4">
                        <small class="text-muted d-block">Total Nominal</small>
                        <strong class="fw-bold">Rp <?= number_format($tagihan['nominal'], 0, ',', '.') ?></strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">Sudah Dibayar</small>
                        <strong class="fw-bold text-success">Rp <?= number_format($tagihan['dibayar'], 0, ',', '.') ?></strong>
                    </div>
                    <div class="col-4 text-end">
                        <small class="text-muted d-block">Sisa Tagihan</small>
                        <strong class="fw-bold text-danger fs-5">Rp <?= number_format($tagihan['sisa'], 0, ',', '.') ?></strong>
                    </div>
                </div>

                <div class="mt-3 small text-muted">
                    <i class="bi bi-calendar-event me-1"></i> Jatuh Tempo: <strong><?= date('d M Y', strtotime($tagihan['jatuh_tempo'])) ?></strong>
                </div>
            </div>

            <!-- Jika Belum Lunas: Pilihan Pembayaran & Form Upload -->
            <?php if ($tagihan['sisa'] > 0): ?>
            <div class="mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Instruksi Pembayaran Transfer / QRIS</h6>
                
                <!-- Nav Tab Pembayaran -->
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill mb-3" id="payTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill fw-bold" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button">
                            <i class="bi bi-bank me-1"></i> Transfer Bank
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill fw-bold" id="qris-tab" data-bs-toggle="tab" data-bs-target="#qris" type="button">
                            <i class="bi bi-qr-code me-1"></i> QRIS Statis
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3 border rounded-4 bg-white" id="payTabContent">
                    <div class="tab-pane fade show active" id="bank">
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                            <div>
                                <small class="text-muted d-block"><?= xss($spp_set['nama_bank'] ?? 'Bank BCA') ?></small>
                                <strong class="fs-4 text-primary d-block" id="norek"><?= xss($spp_set['no_rekening'] ?? '1234567890') ?></strong>
                                <small class="text-dark fw-semibold">a.n. <?= xss($spp_set['atas_nama'] ?? 'Rumah Quran Temi') ?></small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="copyNorek()">Salin No.Rek</button>
                        </div>
                    </div>
                    <div class="tab-pane fade text-center" id="qris">
                        <p class="small text-muted mb-2">Scan QRIS menggunakan Mobile Banking atau E-Wallet (GoPay, OVO, ShopeePay, DANA):</p>
                        <img src="img/<?= xss($spp_set['qris_image'] ?? 'qris_default.png') ?>" style="max-width: 220px;" class="img-fluid border rounded-3 p-2 shadow-sm">
                    </div>
                </div>

                <!-- Form Upload Bukti Transfer -->
                <div class="mt-4 p-4 border rounded-4 bg-light">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cloud-arrow-up me-2 text-success"></i>Konfirmasi / Upload Bukti Pembayaran</h6>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Metode Pembayaran</label>
                            <select name="metode" class="form-select">
                                <option value="Transfer">Transfer Bank</option>
                                <option value="QRIS">QRIS Statis</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Nominal yang Ditransfer (Rp)</label>
                            <input type="number" name="nominal_bayar" class="form-control fw-bold" value="<?= $tagihan['sisa'] ?>" max="<?= $tagihan['sisa'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Foto Bukti Transfer (JPG, PNG, PDF)</label>
                            <input type="file" name="bukti_transfer" class="form-control" accept="image/*,.pdf" required>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="catatan" class="form-control" placeholder="Misal: Transfer via BCA atas nama Fulan">
                        </div>

                        <button type="submit" name="upload_bukti" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Kirim Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Riwayat Konfirmasi & Pembayaran -->
            <div>
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Transaksi Tagihan Ini</h6>
                <?php if (mysqli_num_rows($q_history) > 0): ?>
                    <div class="list-group rounded-4 overflow-hidden border">
                        <?php while ($h = mysqli_fetch_assoc($q_history)): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block text-dark">Rp <?= number_format($h['nominal'], 0, ',', '.') ?> (<?= xss($h['metode']) ?>)</strong>
                                    <small class="text-muted"><?= date('d M Y H:i', strtotime($h['created_at'])) ?></small>
                                </div>
                                <div>
                                    <?php if ($h['status_verifikasi'] == 'Disetujui'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">DISETUJUI (LUNAS)</span>
                                    <?php elseif ($h['status_verifikasi'] == 'Pending'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-3 py-2 rounded-pill">MENUNGGU VERIFIKASI</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-2 rounded-pill">DITOLAK</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted small text-center py-3">Belum ada riwayat transaksi pembayaran.</p>
                <?php endif; ?>
            </div>

            <?php if ($tagihan['dibayar'] > 0): ?>
            <div class="mt-4 text-center">
                <a href="spp_kwitansi.php?tagihan_id=<?= $tagihan['id'] ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-printer me-1"></i> Download / Cetak Kwitansi Digital
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyNorek() {
    var norek = document.getElementById('norek').innerText;
    navigator.clipboard.writeText(norek);
    alert('Nomor Rekening (' + norek + ') berhasil disalin!');
}
</script>
</body>
</html>
