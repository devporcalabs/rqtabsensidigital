<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

$jenis_laporan = $_GET['jenis'] ?? 'rekap_tunggakan'; // 'rekap_tunggakan', 'pembayaran_harian', 'per_kelas'
$tgl_awal  = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
$kelas_pilih = $_GET['kelas'] ?? '';

// --- HITUNG SUMMARY STATISTIK ---
$q_stat = mysqli_query($conn, "SELECT 
    SUM(nominal) as total_target,
    SUM(dibayar) as total_dibayar,
    SUM(sisa) as total_tunggakan,
    COUNT(*) as total_tagihan
FROM spp_tagihan");
$stat = mysqli_fetch_assoc($q_stat);

$tot_target    = $stat['total_target'] ?? 0;
$tot_dibayar   = $stat['total_dibayar'] ?? 0;
$tot_tunggakan = $stat['total_tunggakan'] ?? 0;
$pct_pelunasan = ($tot_target > 0) ? round(($tot_dibayar / $tot_target) * 100, 1) : 0;

// Option List Kelas
$q_kelas_list = mysqli_query($conn, "SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .stat-card { border-radius: 20px; border: none; padding: 1.5rem; color: white; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Keuangan SPP</h4>
            <p class="text-muted small mb-0">Rekapitulasi transaksi pemasukan, daftar tunggakan, dan per kelas</p>
        </div>
        <div>
            <button onclick="exportToExcel('.table', 'Laporan_SPP_<?= $jenis_laporan ?>')" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Summary Widgets -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary shadow-sm">
                <small class="opacity-75 d-block mb-1">TOTAL TARGET SPP</small>
                <h3 class="fw-bold mb-0">Rp <?= number_format($tot_target, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success shadow-sm">
                <small class="opacity-75 d-block mb-1">TOTAL TERBAYAR</small>
                <h3 class="fw-bold mb-0">Rp <?= number_format($tot_dibayar, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-danger shadow-sm">
                <small class="opacity-75 d-block mb-1">TOTAL TUNGGAKAN (SISA)</small>
                <h3 class="fw-bold mb-0">Rp <?= number_format($tot_tunggakan, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-dark shadow-sm">
                <small class="opacity-75 d-block mb-1">PERSENTASE PELUNASAN</small>
                <h3 class="fw-bold mb-0"><?= $pct_pelunasan ?>%</h3>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card-custom shadow-sm mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Kategori Laporan</label>
                <select name="jenis" class="form-select border-0 bg-light" onchange="this.form.submit()">
                    <option value="rekap_tunggakan" <?= $jenis_laporan == 'rekap_tunggakan' ? 'selected' : '' ?>>Daftar Tunggakan Siswa</option>
                    <option value="pembayaran_harian" <?= $jenis_laporan == 'pembayaran_harian' ? 'selected' : '' ?>>Pemasukan Transaksi (Harian/Periode)</option>
                    <option value="per_kelas" <?= $jenis_laporan == 'per_kelas' ? 'selected' : '' ?>>Rekapitulasi Per Kelas</option>
                </select>
            </div>

            <?php if ($jenis_laporan == 'pembayaran_harian'): ?>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Tanggal Awal</label>
                <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control border-0 bg-light">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control border-0 bg-light">
            </div>
            <?php endif; ?>

            <?php if ($jenis_laporan == 'rekap_tunggakan' || $jenis_laporan == 'per_kelas'): ?>
            <div class="col-md-4">
                <label class="small fw-bold text-muted">Filter Kelas</label>
                <select name="kelas" class="form-select border-0 bg-light">
                    <option value="">-- Semua Kelas --</option>
                    <?php while ($k = mysqli_fetch_assoc($q_kelas_list)): ?>
                    <option value="<?= xss($k['nama_kelas']) ?>" <?= $kelas_pilih == $k['nama_kelas'] ? 'selected' : '' ?>><?= xss($k['nama_kelas']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-filter me-1"></i> Tampilkan</button>
            </div>
        </form>
    </div>

    <!-- Content Table -->
    <div class="card-custom shadow-sm">
        <?php if ($jenis_laporan == 'rekap_tunggakan'): ?>
            <!-- TABEL DAFTAR TUNGGAKAN -->
            <?php 
            $sql_tung = "SELECT t.*, s.nama as nama_siswa, s.no_hp_ortu, s.kelas, jt.nama as nama_tagihan, jt.jatuh_tempo
                         FROM spp_tagihan t
                         JOIN siswa s ON t.nis = s.nis
                         JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id = jt.id
                         WHERE t.sisa > 0";
            if (!empty($kelas_pilih)) {
                $sql_tung .= " AND s.kelas = '" . mysqli_real_escape_string($conn, $kelas_pilih) . "'";
            }
            $sql_tung .= " ORDER BY s.kelas ASC, s.nama ASC";
            $q_tung = mysqli_query($conn, $sql_tung);
            ?>
            <h5 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-octagon me-2"></i>Daftar Tunggakan Pembayaran Siswa</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS & Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Tagihan</th>
                            <th>Jatuh Tempo</th>
                            <th>Nominal Tagihan</th>
                            <th>Sudah Dibayar</th>
                            <th class="text-danger">Sisa Tunggakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_tung) > 0): $no=1; ?>
                            <?php while ($r = mysqli_fetch_assoc($q_tung)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong class="text-dark d-block"><?= xss($r['nama_siswa']) ?></strong>
                                    <small class="text-muted">NIS: <?= xss($r['nis']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= xss($r['kelas']) ?></span></td>
                                <td><?= xss($r['nama_tagihan']) ?></td>
                                <td><?= date('d/m/Y', strtotime($r['jatuh_tempo'])) ?></td>
                                <td>Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td>
                                <td class="text-success">Rp <?= number_format($r['dibayar'], 0, ',', '.') ?></td>
                                <td class="fw-bold text-danger">Rp <?= number_format($r['sisa'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada tunggakan pembayaran siswa saat ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($jenis_laporan == 'pembayaran_harian'): ?>
            <!-- TABEL PEMASUKAN TRANSAKSI PERIODE -->
            <?php 
            $sql_pem = "SELECT p.*, s.nama as nama_siswa, s.kelas, jt.nama as nama_tagihan
                        FROM spp_pembayaran p
                        JOIN siswa s ON p.nis = s.nis
                        JOIN spp_tagihan t ON p.tagihan_id = t.id
                        JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id = jt.id
                        WHERE p.status_verifikasi = 'Disetujui'
                        AND DATE(p.created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                        ORDER BY p.id DESC";
            $q_pem = mysqli_query($conn, $sql_pem);
            ?>
            <h5 class="fw-bold text-success mb-3"><i class="bi bi-cash-coin me-2"></i>Laporan Transaksi Pemasukan (<?= date('d M Y', strtotime($tgl_awal)) ?> s/d <?= date('d M Y', strtotime($tgl_akhir)) ?>)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal & Waktu</th>
                            <th>Nama Siswa & Kelas</th>
                            <th>Tagihan</th>
                            <th>Metode</th>
                            <th class="text-end">Nominal Masuk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_pem) > 0): $no=1; $subtotal=0; ?>
                            <?php while ($r = mysqli_fetch_assoc($q_pem)): $subtotal += $r['nominal']; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= xss($r['nomor_transaksi']) ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                <td>
                                    <strong><?= xss($r['nama_siswa']) ?></strong>
                                    <div class="small text-muted"><?= xss($r['kelas']) ?></div>
                                </td>
                                <td><?= xss($r['nama_tagihan']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= xss($r['metode']) ?></span></td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="6" class="text-end">TOTAL PEMASUKAN PERIODE INI:</td>
                                <td class="text-end text-success fs-5">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            </tr>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi pembayaran pada periode tanggal yang dipilih.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($jenis_laporan == 'per_kelas'): ?>
            <!-- TABEL REKAPITULASI PER KELAS -->
            <?php 
            $sql_kls = "SELECT s.kelas, 
                               COUNT(t.id) as total_tagihan,
                               SUM(t.nominal) as target_nominal,
                               SUM(t.dibayar) as total_dibayar,
                               SUM(t.sisa) as total_tunggakan
                        FROM siswa s
                        JOIN spp_tagihan t ON s.nis = t.nis
                        GROUP BY s.kelas
                        ORDER BY s.kelas ASC";
            $q_kls = mysqli_query($conn, $sql_kls);
            ?>
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-building me-2"></i>Rekapitulasi Pelunasan SPP Per Kelas</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas / Lembaga</th>
                            <th>Jumlah Tagihan</th>
                            <th>Target Nominal</th>
                            <th class="text-success">Total Terbayar</th>
                            <th class="text-danger">Total Tunggakan</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_kls) > 0): $no=1; ?>
                            <?php while ($r = mysqli_fetch_assoc($q_kls)): 
                                $pct = ($r['target_nominal'] > 0) ? round(($r['total_dibayar'] / $r['target_nominal']) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong class="text-dark"><?= xss($r['kelas']) ?></strong></td>
                                <td><?= number_format($r['total_tagihan']) ?> Tagihan</td>
                                <td class="fw-bold">Rp <?= number_format($r['target_nominal'], 0, ',', '.') ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($r['total_dibayar'], 0, ',', '.') ?></td>
                                <td class="fw-bold text-danger">Rp <?= number_format($r['total_tunggakan'], 0, ',', '.') ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
                                        </div>
                                        <span class="small fw-bold"><?= $pct ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data rekapitulasi kelas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function exportToExcel(tableSelector, filename) {
    let table = document.querySelector(tableSelector);
    if (!table) { alert('Data tabel tidak ditemukan!'); return; }
    let html = table.outerHTML;
    let blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = (filename || 'laporan_spp') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
</script>

</body>
</html>
