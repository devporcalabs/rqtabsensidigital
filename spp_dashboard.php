<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

$tgl_today = date('Y-m-d');

// --- 1. AMBIL WIDGET STATISTIK ---
// Total Tagihan & Pelunasan
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

// Pembayaran Hari Ini
$q_today = mysqli_query($conn, "SELECT SUM(nominal) as total_today FROM spp_pembayaran WHERE status_verifikasi='Disetujui' AND DATE(created_at) = '$tgl_today'");
$tot_today = mysqli_fetch_assoc($q_today)['total_today'] ?? 0;

// Menunggu Verifikasi
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as total_pending FROM spp_pembayaran WHERE status_verifikasi='Pending'");
$tot_pending = mysqli_fetch_assoc($q_pending)['total_pending'] ?? 0;

// Status Count
$q_status_count = mysqli_query($conn, "SELECT status, COUNT(*) as jml FROM spp_tagihan GROUP BY status");
$status_map = ['Lunas' => 0, 'Sebagian' => 0, 'Belum Bayar' => 0];
while ($sc = mysqli_fetch_assoc($q_status_count)) {
    $status_map[$sc['status']] = $sc['jml'];
}

// Data Grafik Pemasukan 6 Bulan Terakhir
$chart_months = [];
$chart_totals = [];
for ($i = 5; $i >= 0; $i--) {
    $m_year = date('Y-m', strtotime("-$i months"));
    $m_label = date('M Y', strtotime("-$i months"));
    
    $q_m = mysqli_query($conn, "SELECT SUM(nominal) as total FROM spp_pembayaran WHERE status_verifikasi='Disetujui' AND DATE_FORMAT(created_at, '%Y-%m') = '$m_year'");
    $tot_m = mysqli_fetch_assoc($q_m)['total'] ?? 0;

    $chart_months[] = $m_label;
    $chart_totals[] = (float)$tot_m;
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Bendahara SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 24px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .stat-card { border-radius: 20px; border: none; padding: 1.5rem; color: white; position: relative; overflow: hidden; }
        .stat-icon { position: absolute; right: 15px; bottom: 15px; font-size: 3.5rem; opacity: 0.2; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Keuangan & SPP</h4>
            <p class="text-muted small mb-0">Ringkasan statistik real-time pembayaran SPP, tunggakan, dan transaksi</p>
        </div>
        <?php if ($role == 'admin' || $role == 'bendahara'): ?>
        <div class="d-flex gap-2">
            <a href="spp_generate_tagihan.php" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-magic me-1"></i> Generate Tagihan
            </a>
            <a href="spp_pembayaran.php" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                <i class="bi bi-clock-history me-1"></i> Verifikasi (<?= $tot_pending ?>)
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Widgets Bar 1 -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="stat-card bg-primary shadow-sm">
                <small class="opacity-75 d-block mb-1">TOTAL TARGET SPP</small>
                <h5 class="fw-bold mb-0">Rp <?= number_format($tot_target, 0, ',', '.') ?></h5>
                <i class="bi bi-bullseye stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card bg-success shadow-sm">
                <small class="opacity-75 d-block mb-1">TOTAL TERBAYAR</small>
                <h5 class="fw-bold mb-0">Rp <?= number_format($tot_dibayar, 0, ',', '.') ?></h5>
                <i class="bi bi-check-circle stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card bg-danger shadow-sm">
                <small class="opacity-75 d-block mb-1">TOTAL TUNGGAKAN</small>
                <h5 class="fw-bold mb-0">Rp <?= number_format($tot_tunggakan, 0, ',', '.') ?></h5>
                <i class="bi bi-exclamation-circle stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card bg-info shadow-sm">
                <small class="opacity-75 d-block mb-1">PEMBAYARAN HARI INI</small>
                <h5 class="fw-bold mb-0">Rp <?= number_format($tot_today, 0, ',', '.') ?></h5>
                <i class="bi bi-cash-stack stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card bg-warning text-dark shadow-sm">
                <small class="opacity-75 d-block mb-1">PERLU VERIFIKASI</small>
                <h5 class="fw-bold mb-0"><?= number_format($tot_pending) ?> Transaksi</h5>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card bg-dark shadow-sm">
                <small class="opacity-75 d-block mb-1">PERSENTASE LUNAS</small>
                <h5 class="fw-bold mb-0"><?= $pct_pelunasan ?>%</h5>
                <i class="bi bi-pie-chart stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Grafik Pemasukan Bulanan -->
        <div class="col-md-8">
            <div class="card-custom shadow-sm h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Tren Pemasukan SPP (6 Bulan Terakhir)</h5>
                <div style="position: relative; height: 280px;">
                    <canvas id="chartPemasukan"></canvas>
                </div>
            </div>
        </div>

        <!-- Donut Chart Status Pelunasan -->
        <div class="col-md-4">
            <div class="card-custom shadow-sm h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Status Pelunasan Tagihan</h5>
                <div style="position: relative; height: 240px;" class="d-flex align-items-center justify-content-center">
                    <canvas id="chartStatus"></canvas>
                </div>
                <div class="d-flex justify-content-around mt-3 small fw-bold">
                    <span class="text-success"><i class="bi bi-circle-fill me-1"></i> Lunas (<?= $status_map['Lunas'] ?>)</span>
                    <span class="text-warning"><i class="bi bi-circle-fill me-1"></i> Sebagian (<?= $status_map['Sebagian'] ?>)</span>
                    <span class="text-danger"><i class="bi bi-circle-fill me-1"></i> Belum (<?= $status_map['Belum Bayar'] ?>)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart 1: Pemasukan Bulanan
const ctx1 = document.getElementById('chartPemasukan').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_months) ?>,
        datasets: [{
            label: 'Pemasukan SPP (Rp)',
            data: <?= json_encode($chart_totals) ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.3,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                }
            }
        }
    }
});

// Chart 2: Status Pelunasan Donut Chart
const ctx2 = document.getElementById('chartStatus').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Lunas', 'Sebagian', 'Belum Bayar'],
        datasets: [{
            data: [<?= $status_map['Lunas'] ?>, <?= $status_map['Sebagian'] ?>, <?= $status_map['Belum Bayar'] ?>],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});
</script>

</body>
</html>
