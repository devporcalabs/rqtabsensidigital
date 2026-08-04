<?php
// Pastikan session sudah dimulai di file yang memanggil header ini
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memastikan koneksi tersedia
include_once 'koneksi.php';

// Ambil Role user saat ini
$role = $_SESSION['role'] ?? '';

// Mengambil data pengaturan jika belum ada (agar logo & nama sekolah muncul)
if (!isset($data)) {
    $query_set = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
    $data = mysqli_fetch_assoc($query_set);
}

// Menentukan halaman aktif untuk menandai menu
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data user & hitung inisial
$nama_tampil = 'Pengguna';
$initials = 'AD';
if (isset($_SESSION['id'])) {
    $id_user = (int)$_SESSION['id'];
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $id_user);
    $stmt_user->execute();
    $d_user = $stmt_user->get_result()->fetch_assoc();
    
    if ($d_user) {
        if (!empty(trim($d_user['nama'] ?? ''))) {
            $nama_asli = $d_user['nama'];
        } elseif (!empty(trim($d_user['username'] ?? ''))) {
            $nama_asli = $d_user['username'];
        } else {
            $nama_asli = $_SESSION['nama'] ?? 'Pengguna'; 
        }
        $nama_tampil = ucwords(strtolower($nama_asli));
    }
}
$words = explode(" ", $nama_tampil);
$initials_arr = [];
foreach ($words as $w) {
    if (!empty($w)) $initials_arr[] = strtoupper(substr($w, 0, 1));
}
$initials = implode("", array_slice($initials_arr, 0, 2));
if (empty($initials)) $initials = "AD";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="img/porcalabs.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Global font & background style */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif !important; 
            background-color: #f8fafc; 
        }

        /* Responsive spacing on desktop */
        @media (min-width: 992px) {
            .navbar-custom {
                display: none !important;
            }
            body {
                padding: 2.25rem 0 !important;
                margin-left: 280px !important;
                background-color: #f8fafc !important;
            }
            .container, .container-fluid, .main-content {
                max-width: 100% !important;
                padding-left: 2.5rem !important;
                padding-right: 2.5rem !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .sidebar-left {
                display: flex !important;
            }
        }

        /* Mobile specific style */
        @media (max-width: 991.98px) {
            .sidebar-left {
                display: none !important;
            }
            body {
                padding: 80px 1.5rem 1.5rem 1.5rem !important;
            }
            .navbar-custom {
                display: flex !important;
                background-color: #0d6efd;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }

        /* GLOBAL UI STANDARDIZATION */
        /* Glass card & standard card standardization */
        .glass-card, .card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 24px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -2px rgba(0, 0, 0, 0.01) !important;
            padding: 1.75rem !important;
            margin-bottom: 1.5rem !important;
        }

        /* Modern Table styling */
        .table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
            border: none !important;
        }
        .table thead th {
            background-color: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.725rem !important;
            letter-spacing: 0.5px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none !important;
            padding: 1rem 1.25rem !important;
            vertical-align: middle !important;
        }
        .table tbody td {
            padding: 1rem 1.25rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #334155 !important;
            vertical-align: middle !important;
            background-color: #ffffff !important;
        }
        .table-hover tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Input and form control styling */
        .form-control, .form-select {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 12px !important;
            padding: 0.6rem 1rem !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
            color: #334155 !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none !important;
        }

        /* Button styling */
        .btn {
            border-radius: 12px !important;
            font-weight: 700 !important;
            padding: 0.6rem 1.25rem !important;
            font-size: 0.85rem !important;
            transition: all 0.2s !important;
        }
        .btn-primary {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            transform: translateY(-1px);
        }
        .btn-success {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: #ffffff !important;
        }
        .btn-success:hover {
            background-color: #059669 !important;
            border-color: #059669 !important;
            transform: translateY(-1px);
        }
        .btn-action {
            border-radius: 30px !important;
        }

        /* Sidebar Left style */
        .sidebar-left {
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding: 2.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            text-decoration: none;
        }
        .sidebar-brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #eff6ff;
            padding: 4px;
            object-fit: contain;
        }
        .sidebar-brand-name {
            font-weight: 800;
            font-size: 1rem;
            color: #1e293b;
            line-height: 1.2;
        }
        .sidebar-brand-sub {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }
        .sidebar-menu-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            color: #64748b;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        .sidebar-nav-link i {
            font-size: 1.2rem;
        }
        .sidebar-nav-link:hover {
            background: #f8fafc;
            color: #1e293b;
        }
        .sidebar-nav-link.active {
            background: #3b82f6;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }
        .sidebar-footer {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }
        
        .sidebar-sub-nav {
            padding-left: 1.25rem;
            margin-top: 0.25rem;
        }
        .sidebar-sub-nav.show, .sidebar-sub-nav.collapsing {
            display: flex !important;
            flex-direction: column;
            gap: 0.2rem;
        }
        .sidebar-nav-link[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        .sidebar-nav-link .bi-chevron-down {
            transition: transform 0.2s ease;
        }

        .logout-modal .modal-content {
            border-radius: 24px;
            border: none;
        }
    </style>
</head>
<body>

<!-- Left Sidebar -->
<div class="sidebar-left">
    <a href="dashboard.php" class="sidebar-brand">
        <img src="img/<?= htmlspecialchars($data['logo_sekolah'] ?? 'porcalabs.ico'); ?>" class="sidebar-brand-logo">
        <div>
            <div class="sidebar-brand-name"><?= htmlspecialchars($data['nama_sekolah'] ?? 'Rumah Quran'); ?></div>
            <div class="sidebar-brand-sub">Sistem Administrasi</div>
        </div>
    </a>
    
    <div class="sidebar-menu-label">Menu Utama</div>
    
    <div class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-nav-link <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        
        <?php if ($role == 'admin'): ?>
        <a href="data_siswa.php" class="sidebar-nav-link <?= in_array($current_page, ['data_siswa.php', 'edit_siswa.php']) ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i> Siswa
        </a>
        <a href="data_guru.php" class="sidebar-nav-link <?= in_array($current_page, ['data_guru.php', 'edit_guru.php']) ? 'active' : ''; ?>">
            <i class="bi bi-person-badge-fill"></i> Guru
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'bendahara' || $role == 'operator' || $role == 'walikelas' || $role == 'kepalsekolah'): ?>
        <a class="sidebar-nav-link <?= in_array($current_page, ['spp_dashboard.php', 'spp_data_tagihan.php', 'spp_pembayaran.php', 'spp_jenis_tagihan.php', 'spp_generate_tagihan.php', 'spp_laporan.php', 'spp_pengaturan.php']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#collapseSPP" role="button" aria-expanded="<?= in_array($current_page, ['spp_dashboard.php', 'spp_data_tagihan.php', 'spp_pembayaran.php', 'spp_jenis_tagihan.php', 'spp_generate_tagihan.php', 'spp_laporan.php', 'spp_pengaturan.php']) ? 'true' : 'false'; ?>" aria-controls="collapseSPP">
            <i class="bi bi-wallet2"></i> Keuangan SPP <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse sidebar-sub-nav <?= in_array($current_page, ['spp_dashboard.php', 'spp_data_tagihan.php', 'spp_pembayaran.php', 'spp_jenis_tagihan.php', 'spp_generate_tagihan.php', 'spp_laporan.php', 'spp_pengaturan.php']) ? 'show' : ''; ?>" id="collapseSPP">
            <a href="spp_dashboard.php" class="sidebar-nav-link <?= ($current_page == 'spp_dashboard.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-speedometer2"></i> Dashboard SPP
            </a>
            <a href="spp_data_tagihan.php" class="sidebar-nav-link <?= ($current_page == 'spp_data_tagihan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-receipt-cutoff"></i> Tagihan Siswa
            </a>
            <a href="spp_pembayaran.php" class="sidebar-nav-link <?= ($current_page == 'spp_pembayaran.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-check-circle"></i> Verifikasi Bayar
            </a>
            <?php if ($role == 'admin' || $role == 'bendahara'): ?>
            <a href="spp_jenis_tagihan.php" class="sidebar-nav-link <?= ($current_page == 'spp_jenis_tagihan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-tags"></i> Master Tagihan
            </a>
            <a href="spp_generate_tagihan.php" class="sidebar-nav-link <?= ($current_page == 'spp_generate_tagihan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-magic"></i> Generate Tagihan
            </a>
            <?php endif; ?>
            <a href="spp_laporan.php" class="sidebar-nav-link <?= ($current_page == 'spp_laporan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan SPP
            </a>
            <?php if ($role == 'admin' || $role == 'bendahara'): ?>
            <a href="spp_pengaturan.php" class="sidebar-nav-link <?= ($current_page == 'spp_pengaturan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-gear"></i> Pengaturan SPP
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($role == 'admin' || $role == 'kantin'): ?>
        <a class="sidebar-nav-link <?= in_array($current_page, ['kantin_kasir.php', 'kantin_topup.php', 'kantin_laporan.php']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#collapseKantin" role="button" aria-expanded="<?= in_array($current_page, ['kantin_kasir.php', 'kantin_topup.php', 'kantin_laporan.php']) ? 'true' : 'false'; ?>" aria-controls="collapseKantin">
            <i class="bi bi-shop"></i> E-Kantin <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse sidebar-sub-nav <?= in_array($current_page, ['kantin_kasir.php', 'kantin_topup.php', 'kantin_laporan.php']) ? 'show' : ''; ?>" id="collapseKantin">
            <a href="kantin_kasir.php" class="sidebar-nav-link <?= ($current_page == 'kantin_kasir.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-calculator"></i> Kasir Kantin
            </a>
            <?php if ($role == 'admin'): ?>
            <a href="kantin_topup.php" class="sidebar-nav-link <?= ($current_page == 'kantin_topup.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-wallet2"></i> Top Up Saldo
            </a>
            <?php endif; ?>
            <a href="kantin_laporan.php" class="sidebar-nav-link <?= ($current_page == 'kantin_laporan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-file-earmark-spreadsheet"></i> Laporan Kantin
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($role !== 'kantin'): ?>
        <a class="sidebar-nav-link <?= in_array($current_page, ['laporan.php', 'laporan_guru.php', 'rekap_bulanan.php', 'input_manual.php']) ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#collapseLaporan" role="button" aria-expanded="<?= in_array($current_page, ['laporan.php', 'laporan_guru.php', 'rekap_bulanan.php', 'input_manual.php']) ? 'true' : 'false'; ?>" aria-controls="collapseLaporan">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
        </a>
        <div class="collapse sidebar-sub-nav <?= in_array($current_page, ['laporan.php', 'laporan_guru.php', 'rekap_bulanan.php', 'input_manual.php']) ? 'show' : ''; ?>" id="collapseLaporan">
            <a href="laporan.php" class="sidebar-nav-link <?= ($current_page == 'laporan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-file-earmark-text"></i> Laporan Siswa
            </a>
            <a href="laporan_guru.php" class="sidebar-nav-link <?= ($current_page == 'laporan_guru.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-person-badge"></i> Laporan Guru
            </a>
            <a href="rekap_bulanan.php" class="sidebar-nav-link <?= ($current_page == 'rekap_bulanan.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-calendar-check"></i> Rekap Bulanan
            </a>
            <a href="input_manual.php" class="sidebar-nav-link <?= ($current_page == 'input_manual.php') ? 'active' : ''; ?>" style="font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="bi bi-pencil-square"></i> Input Manual
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="sidebar-footer">
        <?php if ($role == 'admin'): ?>
        <a href="pengaturan.php" class="sidebar-nav-link <?= ($current_page == 'pengaturan.php') ? 'active' : ''; ?>">
            <i class="bi bi-gear-fill"></i> Pengaturan
        </a>
        <?php endif; ?>
        
        <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" class="sidebar-nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </div>
</div>

<!-- Mobile Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <img src="img/<?= htmlspecialchars($data['logo_sekolah'] ?? 'porcalabs.ico'); ?>" height="32" class="rounded bg-white p-1">
            <span class="fw-bold"><?= htmlspecialchars($data['nama_sekolah'] ?? 'Rumah Quran'); ?></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto py-2">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-grid me-1"></i> Dashboard
                    </a>
                </li>
                
                <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'data_siswa.php') ? 'active' : ''; ?>" href="data_siswa.php">
                        <i class="bi bi-people me-1"></i> Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'data_guru.php') ? 'active' : ''; ?>" href="data_guru.php">
                        <i class="bi bi-person-badge me-1"></i> Guru
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($role == 'admin' || $role == 'bendahara' || $role == 'operator' || $role == 'walikelas' || $role == 'kepalsekolah'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($current_page, ['spp_dashboard.php', 'spp_data_tagihan.php', 'spp_pembayaran.php', 'spp_jenis_tagihan.php', 'spp_generate_tagihan.php', 'spp_laporan.php', 'spp_pengaturan.php']) ? 'active' : ''; ?>" href="#" id="navbarDropdownSPP" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-wallet2 me-1"></i> Keuangan SPP
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdownSPP" style="border-radius: 12px; padding: 10px;">
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_dashboard.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard SPP
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_data_tagihan.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_data_tagihan.php">
                                <i class="bi bi-receipt-cutoff me-2"></i> Tagihan Siswa
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_pembayaran.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_pembayaran.php">
                                <i class="bi bi-check-circle me-2"></i> Verifikasi Bayar
                            </a>
                        </li>
                        <?php if ($role == 'admin' || $role == 'bendahara'): ?>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_jenis_tagihan.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_jenis_tagihan.php">
                                <i class="bi bi-tags me-2"></i> Master Tagihan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_generate_tagihan.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_generate_tagihan.php">
                                <i class="bi bi-magic me-2"></i> Generate Tagihan
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_laporan.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_laporan.php">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan SPP
                            </a>
                        </li>
                        <?php if ($role == 'admin' || $role == 'bendahara'): ?>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'spp_pengaturan.php') ? 'active bg-primary text-white' : ''; ?>" href="spp_pengaturan.php">
                                <i class="bi bi-gear me-2"></i> Pengaturan SPP
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($role == 'admin' || $role == 'kantin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($current_page, ['kantin_kasir.php', 'kantin_topup.php', 'kantin_laporan.php']) ? 'active' : ''; ?>" href="#" id="navbarDropdownKantin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shop me-1"></i> E-Kantin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdownKantin" style="border-radius: 12px; padding: 10px;">
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'kantin_kasir.php') ? 'active bg-primary text-white' : ''; ?>" href="kantin_kasir.php">
                                <i class="bi bi-calculator me-2"></i> Kasir Kantin
                            </a>
                        </li>
                        <?php if ($role == 'admin'): ?>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'kantin_topup.php') ? 'active bg-primary text-white' : ''; ?>" href="kantin_topup.php">
                                <i class="bi bi-wallet2 me-2"></i> Top Up Saldo
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'kantin_laporan.php') ? 'active bg-primary text-white' : ''; ?>" href="kantin_laporan.php">
                                <i class="bi bi-file-earmark-spreadsheet me-2"></i> Laporan Kantin
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($role !== 'kantin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($current_page, ['laporan.php', 'laporan_guru.php', 'rekap_bulanan.php', 'input_manual.php']) ? 'active' : ''; ?>" href="#" id="navbarDropdownLaporan" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdownLaporan" style="border-radius: 12px; padding: 10px;">
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'laporan.php') ? 'active bg-primary text-white' : ''; ?>" href="laporan.php">
                                <i class="bi bi-file-earmark-text me-2"></i> Laporan Siswa
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'laporan_guru.php') ? 'active bg-primary text-white' : ''; ?>" href="laporan_guru.php">
                                <i class="bi bi-person-badge me-2"></i> Laporan Guru
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'rekap_bulanan.php') ? 'active bg-primary text-white' : ''; ?>" href="rekap_bulanan.php">
                                <i class="bi bi-calendar-check me-2"></i> Rekap Bulanan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 px-3 rounded <?= ($current_page == 'input_manual.php') ? 'active bg-primary text-white' : ''; ?>" href="input_manual.php">
                                <i class="bi bi-pencil-square me-2"></i> Input Manual
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'pengaturan.php') ? 'active' : ''; ?>" href="pengaturan.php">
                        <i class="bi bi-gear me-1"></i> Pengaturan
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item ms-lg-3">
                    <a class="nav-link text-warning fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade logout-modal" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="text-warning mb-3">
                    <i class="bi bi-exclamation-circle" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Yakin ingin keluar?</h5>
                <p class="text-muted small">Anda harus login kembali untuk mengakses sistem.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light w-100 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <a href="logout.php" class="btn btn-danger w-100 rounded-pill fw-bold">Ya, Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>
