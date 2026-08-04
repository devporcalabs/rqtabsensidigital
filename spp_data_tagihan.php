<?php
session_start();
include 'koneksi.php';
include_once 'spp_init_db.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// --- PROSES BAYAR TUNAI LANGSUNG DARI ADMIN / BENDAHARA ---
if (isset($_POST['bayar_tunai'])) {
    $tagihan_id = (int)$_POST['tagihan_id'];
    $nominal_bayar = (float)$_POST['nominal_bayar'];
    $catatan = trim($_POST['catatan'] ?? '');
    $petugas_id = $_SESSION['user_id'] ?? 1;

    // Ambil data tagihan saat ini
    $stmt_t = $conn->prepare("SELECT t.*, s.nama, s.no_hp_ortu, s.kelas, jt.nama as nama_tagihan FROM spp_tagihan t JOIN siswa s ON t.nis=s.nis JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id=jt.id WHERE t.id=?");
    $stmt_t->bind_param("i", $tagihan_id);
    $stmt_t->execute();
    $t = $stmt_t->get_result()->fetch_assoc();

    if ($t && $nominal_bayar > 0) {
        $nominal_bayar = min($nominal_bayar, $t['sisa']);
        $dibayar_baru = $t['dibayar'] + $nominal_bayar;
        $sisa_baru = max(0, $t['nominal'] - $dibayar_baru);
        $status_baru = ($sisa_baru <= 0) ? 'Lunas' : 'Sebagian';

        // Generate Nomor Transaksi
        $no_trx = 'SPP-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));

        // Insert ke spp_pembayaran
        $stmt_p = $conn->prepare("INSERT INTO spp_pembayaran (nomor_transaksi, tagihan_id, nis, metode, nominal, status_verifikasi, catatan, petugas_id) VALUES (?, ?, ?, 'Tunai', ?, 'Disetujui', ?, ?)");
        $stmt_p->bind_param("sisdsi", $no_trx, $tagihan_id, $t['nis'], $nominal_bayar, $catatan, $petugas_id);
        $stmt_p->execute();

        // Update spp_tagihan
        $stmt_u = $conn->prepare("UPDATE spp_tagihan SET dibayar=?, sisa=?, status=? WHERE id=?");
        $stmt_u->bind_param("ddsi", $dibayar_baru, $sisa_baru, $status_baru, $tagihan_id);
        $stmt_u->execute();

        echo "<script>alert('Pembayaran Tunai sebesar Rp " . number_format($nominal_bayar, 0, ',', '.') . " berhasil dicatat!'); window.location='spp_data_tagihan.php';</script>";
        exit;
    }
}

// --- FILTER DATA ---
$f_kelas  = trim($_GET['kelas'] ?? '');
$f_status = trim($_GET['status'] ?? '');
$f_q      = trim($_GET['q'] ?? '');

$sql = "SELECT t.*, s.nama as nama_siswa, s.kelas, s.no_hp_ortu, jt.nama as nama_tagihan, jt.jatuh_tempo, jt.tahun_ajaran
        FROM spp_tagihan t
        JOIN siswa s ON t.nis = s.nis
        JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id = jt.id
        WHERE 1=1";

if (!empty($f_kelas)) {
    $sql .= " AND s.kelas = '" . mysqli_real_escape_string($conn, $f_kelas) . "'";
}
if (!empty($f_status)) {
    $sql .= " AND t.status = '" . mysqli_real_escape_string($conn, $f_status) . "'";
}
if (!empty($f_q)) {
    $q_clean = mysqli_real_escape_string($conn, $f_q);
    $sql .= " AND (s.nama LIKE '%$q_clean%' OR s.nis LIKE '%$q_clean%' OR jt.nama LIKE '%$q_clean%')";
}

$sql .= " ORDER BY t.id DESC";
$q_tagihan = mysqli_query($conn, $sql);

// Opsi list kelas untuk filter
$q_kelas_list = mysqli_query($conn, "SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");

// Base URL portal
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tagihan SPP Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.5rem; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Data Tagihan SPP Siswa</h4>
            <p class="text-muted small mb-0">Kelola status pembayaran, penerimaan tunai, dan link portal orang tua</p>
        </div>
        <?php if ($role == 'admin' || $role == 'bendahara'): ?>
        <div class="d-flex gap-2">
            <a href="spp_generate_tagihan.php" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-magic me-1"></i> Generate Tagihan
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Filter Section -->
    <div class="card-custom shadow-sm mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Filter Kelas / Lembaga</label>
                <select name="kelas" class="form-select border-0 bg-light">
                    <option value="">-- Semua Kelas --</option>
                    <?php while ($kls = mysqli_fetch_assoc($q_kelas_list)): ?>
                    <option value="<?= xss($kls['nama_kelas']) ?>" <?= $f_kelas == $kls['nama_kelas'] ? 'selected' : '' ?>><?= xss($kls['nama_kelas']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Status Pembayaran</label>
                <select name="status" class="form-select border-0 bg-light">
                    <option value="">-- Semua Status --</option>
                    <option value="Belum Bayar" <?= $f_status == 'Belum Bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                    <option value="Sebagian" <?= $f_status == 'Sebagian' ? 'selected' : '' ?>>Sebagian (Cicilan)</option>
                    <option value="Lunas" <?= $f_status == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                    <option value="Terlambat" <?= $f_status == 'Terlambat' ? 'selected' : '' ?>>Terlambat</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold text-muted">Pencarian (Nama / NIS / Tagihan)</label>
                <input type="text" name="q" value="<?= xss($f_q) ?>" class="form-control border-0 bg-light" placeholder="Cari nama atau NIS...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card-custom shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIS & Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tagihan</th>
                        <th>Nominal</th>
                        <th>Dibayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($q_tagihan) > 0): $no=1; ?>
                        <?php while ($row = mysqli_fetch_assoc($q_tagihan)): 
                            $badge_color = 'bg-secondary';
                            if ($row['status'] == 'Lunas') $badge_color = 'bg-success';
                            elseif ($row['status'] == 'Sebagian') $badge_color = 'bg-warning text-dark';
                            elseif ($row['status'] == 'Belum Bayar') $badge_color = 'bg-danger';
                            
                            $link_portal = $base_url . '/spp_portal.php?token=' . $row['token'];
                        ?>
                        <tr>
                            <td class="text-muted"><?= $no++ ?></td>
                            <td>
                                <strong class="text-dark d-block"><?= xss($row['nama_siswa']) ?></strong>
                                <small class="text-muted">NIS: <?= xss($row['nis']) ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= xss($row['kelas']) ?></span></td>
                            <td>
                                <div><strong><?= xss($row['nama_tagihan']) ?></strong></div>
                                <small class="text-muted">JT: <?= date('d/m/Y', strtotime($row['jatuh_tempo'])) ?></small>
                            </td>
                            <td class="fw-bold">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                            <td class="text-success fw-bold">Rp <?= number_format($row['dibayar'], 0, ',', '.') ?></td>
                            <td class="text-danger fw-bold">Rp <?= number_format($row['sisa'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $badge_color ?> bg-opacity-10 text-<?= $row['status'] == 'Lunas' ? 'success' : ($row['status'] == 'Sebagian' ? 'warning' : 'danger') ?> fw-bold px-3 py-2 rounded-pill">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyLink('<?= $link_portal ?>')" title="Salin Link Portal Ortu">
                                        <i class="bi bi-link-45deg"></i> Link Ortu
                                    </button>
                                    
                                    <?php if ($row['sisa'] > 0 && ($role == 'admin' || $role == 'bendahara' || $role == 'operator')): ?>
                                    <button class="btn btn-sm btn-success" onclick='bukaModalBayar(<?= json_encode($row) ?>)' title="Bayar Tunai">
                                        <i class="bi bi-cash-stack"></i> Bayar
                                    </button>
                                    <?php endif; ?>

                                    <?php if ($row['dibayar'] > 0): ?>
                                    <a href="spp_kwitansi.php?tagihan_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="Cetak Kwitansi">
                                        <i class="bi bi-printer"></i> Kwitansi
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">Data tagihan tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Input Pembayaran Tunai -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST">
                <input type="hidden" name="tagihan_id" id="m-tagihan_id">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Input Pembayaran Tunai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">Siswa / Tagihan:</div>
                        <strong id="m-nama-siswa" class="d-block text-primary"></strong>
                        <div id="m-nama-tagihan" class="small fw-semibold text-dark mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Sisa Tagihan</label>
                        <input type="text" id="m-sisa-text" class="form-control bg-light fw-bold text-danger" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nominal Bayar (Rp)</label>
                        <input type="number" name="nominal_bayar" id="m-nominal_bayar" class="form-control form-control-lg fw-bold" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="bayar_tunai" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyLink(url) {
    navigator.clipboard.writeText(url);
    alert('Link Portal Orang Tua berhasil disalin:\n' + url);
}

function bukaModalBayar(row) {
    document.getElementById('m-tagihan_id').value = row.id;
    document.getElementById('m-nama-siswa').innerText = row.nama_siswa + ' (' + row.kelas + ')';
    document.getElementById('m-nama-tagihan').innerText = row.nama_tagihan;
    document.getElementById('m-sisa-text').value = 'Rp ' + Number(row.sisa).toLocaleString('id-ID');
    document.getElementById('m-nominal_bayar').value = row.sisa;
    document.getElementById('m-nominal_bayar').max = row.sisa;
    var modal = new bootstrap.Modal(document.getElementById('modalBayar'));
    modal.show();
}
</script>

</body>
</html>
