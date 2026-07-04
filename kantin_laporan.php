<?php
session_start();
include 'koneksi.php';

// 1. Validasi Sesi Strict
if (!isset($_SESSION['login'])) {
    header("location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin' && $role !== 'kantin') {
    die("Akses Ditolak!");
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// 2. Ambil Parameter Filter
$tgl_awal     = $_GET['tgl_awal'] ?? date('Y-m-01'); // Awal bulan ini
$tgl_akhir    = $_GET['tgl_akhir'] ?? date('Y-m-d');
$tipe_filter  = $_GET['tipe'] ?? '';
$kelas_filter = $_GET['kelas'] ?? '';

// 3. Bangun Query SQL Dinamis
$where = "WHERE DATE(t.waktu) BETWEEN ? AND ?";
$params = [$tgl_awal, $tgl_akhir];
$types = "ss";

if (!empty($tipe_filter)) {
    $where .= " AND t.tipe = ?";
    $params[] = $tipe_filter;
    $types .= "s";
}

if (!empty($kelas_filter)) {
    $where .= " AND s.kelas = ?";
    $params[] = $kelas_filter;
    $types .= "s";
}

// Query untuk data transaksi
$sql = "SELECT t.*, s.nama, s.kelas 
        FROM kantin_transaksi t 
        LEFT JOIN siswa s ON t.nis = s.nis 
        $where 
        ORDER BY t.waktu DESC";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$transactions = $stmt->get_result();

// Hitung total belanja & top up untuk filter ini
$total_belanja = 0;
$total_topup = 0;

$data_transaksi_list = [];
while ($row = $transactions->fetch_assoc()) {
    if ($row['tipe'] == 'debet') {
        $total_belanja += $row['nominal'];
    } else {
        $total_topup += $row['nominal'];
    }
    $data_transaksi_list[] = $row;
}

// 4. LOGIKA EKSPOR EXCEL
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $filename = "Laporan_E-Kantin_" . str_replace('-', '', $tgl_awal) . "_" . str_replace('-', '', $tgl_akhir) . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
    <style>
        .str{ mso-number-format:\@; }
    </style>
    <table border="1">
        <tr>
            <th colspan="8" style="font-size: 1.25rem; font-weight: bold; text-align: center;">LAPORAN TRANSAKSI E-KANTIN</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">Periode: <?= $tgl_awal ?> s/d <?= $tgl_akhir ?></th>
        </tr>
        <tr></tr>
        <tr>
            <th style="background-color: #cbd5e1;">Waktu</th>
            <th style="background-color: #cbd5e1;">NIS</th>
            <th style="background-color: #cbd5e1;">Nama Siswa</th>
            <th style="background-color: #cbd5e1;">Kelas</th>
            <th style="background-color: #cbd5e1;">Tipe</th>
            <th style="background-color: #cbd5e1;">Nominal</th>
            <th style="background-color: #cbd5e1;">Operator</th>
            <th style="background-color: #cbd5e1;">Keterangan</th>
        </tr>
        <?php foreach ($data_transaksi_list as $row): ?>
            <tr>
                <td><?= $row['waktu'] ?></td>
                <td class="str"><?= $row['nis'] ?></td>
                <td><?= htmlspecialchars($row['nama'] ?? 'Siswa Terhapus') ?></td>
                <td><?= htmlspecialchars($row['kelas'] ?? '-') ?></td>
                <td><?= $row['tipe'] == 'debet' ? 'Belanja' : 'Top Up' ?></td>
                <td><?= $row['nominal'] ?></td>
                <td><?= htmlspecialchars($row['operator']) ?></td>
                <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        <tr></tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL BELANJA (Pemasukan Kantin)</td>
            <td colspan="4" style="font-weight: bold; text-align: left; color: red;"><?= $total_belanja ?></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL TOP UP SALDO MASUK</td>
            <td colspan="4" style="font-weight: bold; text-align: left; color: green;"><?= $total_topup ?></td>
        </tr>
    </table>
    <?php
    exit;
}

// 5. RENDER HALAMAN BIASA
include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi E-Kantin - <?= xss($data['nama_sekolah'] ?? 'Rumah Quran Temi') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f6fc;
            background-image: radial-gradient(at 0% 0%, rgba(13, 110, 253, 0.05) 0px, transparent 50%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.03);
        }

        .section-title {
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .summary-box {
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.4);
            padding: 20px;
            color: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        }

        .box-debet {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            box-shadow: 0 8px 20px rgba(225, 29, 72, 0.15);
        }

        .box-kredit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.15);
        }

        .filter-btn {
            border-radius: 12px;
            font-weight: 700;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

    <div class="container py-4" style="margin-top: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="section-title mb-1"><i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>LAPORAN E-KANTIN</h3>
                <p class="text-muted small mb-0">Analisis mutasi saldo, total belanja kantin, dan dana top-up siswa</p>
            </div>
            
            <div class="d-flex gap-2">
                <a href="?export=excel&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&tipe=<?= $tipe_filter ?>&kelas=<?= $kelas_filter ?>" class="btn btn-success filter-btn text-white" style="background-color:#16a34a; border-color:#16a34a;">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </a>
                <button class="btn btn-outline-secondary filter-btn" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </button>
            </div>
        </div>

        <!-- SUMMARY DARI FILTER INI -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="summary-box box-debet">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="opacity-75 fw-bold d-block mb-1">TOTAL BELANJA (Pemasukan Kantin)</small>
                            <h2 class="fw-bold mb-0">Rp <?= number_format($total_belanja, 0, ',', '.') ?></h2>
                        </div>
                        <i class="bi bi-cart-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="summary-box box-kredit">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="opacity-75 fw-bold d-block mb-1">TOTAL DANA TOP-UP MASUK</small>
                            <h2 class="fw-bold mb-0">Rp <?= number_format($total_topup, 0, ',', '.') ?></h2>
                        </div>
                        <i class="bi bi-cash-coin fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM FILTER -->
        <div class="glass-card p-4 mb-4">
            <form method="GET" action="kantin_laporan.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2">Tanggal Mulai</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>">
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-2">Tanggal Selesai</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-2">Tipe Transaksi</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua</option>
                        <option value="debet" <?= $tipe_filter == 'debet' ? 'selected' : '' ?>>Belanja</option>
                        <option value="kredit" <?= $tipe_filter == 'kredit' ? 'selected' : '' ?>>Top Up</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-muted mb-2">Kelas</label>
                    <select name="kelas" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php 
                        $q_k = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                        while($k = mysqli_fetch_assoc($q_k)): ?>
                            <option value="<?= xss($k['nama_kelas']) ?>" <?= $kelas_filter == $k['nama_kelas'] ? 'selected' : '' ?>><?= xss($k['nama_kelas']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 filter-btn"><i class="bi bi-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>

        <!-- TABLE TRANSAKSI -->
        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>WAKTU</th>
                            <th>NIS</th>
                            <th>NAMA SISWA</th>
                            <th class="text-center">KELAS</th>
                            <th class="text-center">TIPE</th>
                            <th class="text-end">NOMINAL</th>
                            <th>OPERATOR</th>
                            <th>KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_transaksi_list) > 0): ?>
                            <?php foreach ($data_transaksi_list as $row): ?>
                                <tr class="border-bottom border-white border-opacity-50">
                                    <td style="font-size: 0.85rem;"><?= date('d-m-Y H:i', strtotime($row['waktu'])) ?></td>
                                    <td><code><?= xss($row['nis']) ?></code></td>
                                    <td class="fw-bold text-dark"><?= xss($row['nama'] ?? 'Siswa Terhapus') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-primary border rounded-pill px-3"><?= xss($row['kelas'] ?? '-') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['tipe'] == 'debet'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2" style="font-size: 0.65rem;">BELANJA</span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2" style="font-size: 0.65rem;">TOP UP</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold <?= $row['tipe'] == 'debet' ? 'text-danger' : 'text-success' ?>">
                                        <?= $row['tipe'] == 'debet' ? '-' : '+' ?>Rp <?= number_format($row['nominal'], 0, ',', '.') ?>
                                    </td>
                                    <td class="small text-muted"><?= xss($row['operator']) ?></td>
                                    <td class="small text-muted"><?= xss($row['keterangan'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted small"><i class="bi bi-info-circle me-1"></i> Tidak ditemukan transaksi pada filter periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
<?php include 'footer.php'; ?>
