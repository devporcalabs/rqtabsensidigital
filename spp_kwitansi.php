<?php
session_start();
include 'koneksi.php';

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

$tagihan_id = (int)($_GET['tagihan_id'] ?? 0);
if ($tagihan_id <= 0) {
    die("Tagihan ID tidak valid.");
}

// Ambil detail tagihan & siswa
$stmt = $conn->prepare("SELECT t.*, s.nama as nama_siswa, s.nis, s.kelas, jt.nama as nama_tagihan, jt.tahun_ajaran
                        FROM spp_tagihan t
                        JOIN siswa s ON t.nis = s.nis
                        JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id = jt.id
                        WHERE t.id = ?");
$stmt->bind_param("i", $tagihan_id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) {
    die("Data kwitansi tidak ditemukan.");
}

// Ambil riwayat pembayaran disetujui
$stmt_p = $conn->prepare("SELECT * FROM spp_pembayaran WHERE tagihan_id=? AND status_verifikasi='Disetujui' ORDER BY id ASC");
$stmt_p->bind_param("i", $tagihan_id);
$stmt_p->execute();
$q_bayar = $stmt_p->get_result();

// Pengaturan sekolah
$q_set = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$sekolah = mysqli_fetch_assoc($q_set);
$nama_sekolah = $sekolah['nama_sekolah'] ?? 'Rumah Qur\'an Temi';
$logo_sekolah = $sekolah['logo_sekolah'] ?? 'porcalabs.ico';

// QR Code URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$verify_url = $base_url . '/spp_portal.php?token=' . $t['token'];
$qr_code_img = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($verify_url);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran SPP — <?= xss($t['nama_siswa']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; background: #f0f3f9; color: #000; }
        .kwitansi-box { background: #fff; border: 2px solid #000; padding: 2.5rem; border-radius: 12px; margin: 2rem auto; max-width: 800px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .border-bottom-thick { border-bottom: 3px double #000 !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .kwitansi-box { border: 2px solid #000; box-shadow: none; margin: 0; padding: 1.5rem; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Action Buttons -->
    <div class="d-flex justify-content-center gap-2 my-3 no-print">
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak / Save PDF
        </button>
        <button onclick="exportToExcel('.kwitansi-box', 'Kwitansi_SPP_<?= xss($t['nis']) ?>')" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </button>
    </div>

    <div class="kwitansi-box">
        <!-- Header Kwitansi -->
        <div class="d-flex align-items-center justify-content-between border-bottom-thick pb-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <img src="img/<?= xss($logo_sekolah) ?>" height="65" class="p-1">
                <div>
                    <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;"><?= xss($nama_sekolah) ?></h4>
                    <p class="mb-0 small">Kwitansi Bukti Pembayaran Keuangan Sekolah Official</p>
                </div>
            </div>
            <div class="text-end">
                <h5 class="fw-bold text-uppercase mb-0 text-decoration-underline">KWITANSI</h5>
                <small class="fw-bold">No: KW-<?= date('Ymd', strtotime($t['created_at'])) ?>-<?= sprintf('%04d', $t['id']) ?></small>
            </div>
        </div>

        <!-- Identitas Siswa -->
        <div class="row mb-4">
            <div class="col-7">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td width="120" class="fw-bold">Telah Terima Dari</td><td>: <?= xss($t['nama_siswa']) ?></td></tr>
                    <tr><td class="fw-bold">NIS / Kelas</td><td>: <?= xss($t['nis']) ?> (<?= xss($t['kelas']) ?>)</td></tr>
                    <tr><td class="fw-bold">Untuk Tagihan</td><td>: <?= xss($t['nama_tagihan']) ?> (T.A <?= xss($t['tahun_ajaran']) ?>)</td></tr>
                </table>
            </div>
            <div class="col-5 text-end">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="fw-bold">Total Nominal</td><td class="fw-bold">: Rp <?= number_format($t['nominal'], 0, ',', '.') ?></td></tr>
                    <tr><td class="fw-bold">Sudah Dibayar</td><td class="fw-bold text-success">: Rp <?= number_format($t['dibayar'], 0, ',', '.') ?></td></tr>
                    <tr><td class="fw-bold">Sisa Tagihan</td><td class="fw-bold text-danger">: Rp <?= number_format($t['sisa'], 0, ',', '.') ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Rincian Pembayaran -->
        <h6 class="fw-bold mb-2">Rincian Riwayat Pembayaran:</h6>
        <table class="table table-bordered align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th width="40">No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($q_bayar) > 0): $no=1; ?>
                    <?php while ($b = mysqli_fetch_assoc($q_bayar)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= xss($b['nomor_transaksi']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($b['created_at'])) ?></td>
                        <td><?= xss($b['metode']) ?></td>
                        <td class="text-end fw-bold">Rp <?= number_format($b['nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-2 text-muted">Belum ada riwayat pembayaran yang disetujui.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer Signature & QR -->
        <div class="d-flex justify-content-between align-items-end pt-3">
            <div class="text-center">
                <img src="<?= $qr_code_img ?>" height="90" class="border p-1 mb-1">
                <div style="font-size: 8pt;" class="text-muted">Scan untuk verifikasi keaslian kwitansi</div>
            </div>
            <div class="text-center">
                <p class="mb-5">Temi, <?= date('d F Y') ?><br>Bendahara Sekolah,</p>
                <p class="fw-bold mb-0 text-decoration-underline">( Panitia Keuangan )</p>
            </div>
        </div>
    </div>
</div>

<script>
function exportToExcel(containerSelector, filename) {
    let container = document.querySelector(containerSelector);
    if (!container) return;
    let html = container.outerHTML;
    let blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = (filename || 'kwitansi') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>

</body>
</html>
