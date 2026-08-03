<?php
session_start();
include 'koneksi.php';

// --- 1. SECURITY ENGINE---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// --- 2. VALIDASI SESI STRICT ---
if(!isset($_SESSION['login']) || empty($_SESSION['id'])){
    session_destroy();
    header("location: login.php");
    exit;
}

$id_user = (int)$_SESSION['id'];
$role = $_SESSION['role'] ?? 'user';
$kelas_diampu = $_SESSION['kelas_diampu'] ?? 'Semua Kelas';

if ($role === 'kantin') {
    header("location: kantin_kasir.php");
    exit;
}

// Ambil data user
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->bind_param("i", $id_user);
$stmt_user->execute();
$d_user = $stmt_user->get_result()->fetch_assoc();

// Nama Tampil
if (!empty(trim($d_user['nama'] ?? ''))) {
    $nama_asli = $d_user['nama'];
} elseif (!empty(trim($d_user['username'] ?? ''))) {
    $nama_asli = $d_user['username'];
} else {
    $nama_asli = $_SESSION['nama'] ?? 'Pengguna'; 
}
$nama_tampil = ucwords(strtolower($nama_asli));

// --- 3. STATISTIK UTAMA (Dihitung di awal untuk Pagination) ---
$tgl_hari_ini = date('Y-m-d');

if($role == 'admin' || $role == 'piket'){
    $stmt_ts = $conn->prepare("SELECT COUNT(*) as total FROM siswa");
    $stmt_ts->execute();
    $total_siswa = $stmt_ts->get_result()->fetch_assoc()['total'];

    $stmt_h = $conn->prepare("SELECT COUNT(*) as total FROM absensi WHERE DATE(waktu_masuk) = ?");
    $stmt_h->bind_param("s", $tgl_hari_ini);
    $stmt_h->execute();
    $total_hadir = $stmt_h->get_result()->fetch_assoc()['total'];

    // LOGIKA TAMBAHAN: HITUNG TELAT
    $stmt_telat = $conn->prepare("SELECT COUNT(*) as total FROM absensi WHERE DATE(waktu_masuk) = ? AND status_kehadiran = 'Terlambat'");
    $stmt_telat->bind_param("s", $tgl_hari_ini);
    $stmt_telat->execute();
    $total_telat = $stmt_telat->get_result()->fetch_assoc()['total'];

    $stmt_count_ba = $conn->prepare("SELECT COUNT(*) as total FROM siswa 
        LEFT JOIN absensi ON siswa.nis = absensi.nis AND DATE(absensi.waktu_masuk) = ? 
        WHERE absensi.nis IS NULL");
    $stmt_count_ba->bind_param("s", $tgl_hari_ini);
} else { 
    $stmt_ts = $conn->prepare("SELECT COUNT(*) as total FROM siswa WHERE kelas = ?");
    $stmt_ts->bind_param("s", $kelas_diampu);
    $stmt_ts->execute();
    $total_siswa = $stmt_ts->get_result()->fetch_assoc()['total'];

    $stmt_h = $conn->prepare("SELECT COUNT(*) as total FROM absensi 
        JOIN siswa ON absensi.nis = siswa.nis 
        WHERE DATE(absensi.waktu_masuk) = ? AND siswa.kelas = ?");
    $stmt_h->bind_param("ss", $tgl_hari_ini, $kelas_diampu);
    $stmt_h->execute();
    $total_hadir = $stmt_h->get_result()->fetch_assoc()['total'];

    // LOGIKA TAMBAHAN: HITUNG TELAT WALI KELAS
    $stmt_telat = $conn->prepare("SELECT COUNT(*) as total FROM absensi 
        JOIN siswa ON absensi.nis = siswa.nis 
        WHERE DATE(absensi.waktu_masuk) = ? AND siswa.kelas = ? AND absensi.status_kehadiran = 'Terlambat'");
    $stmt_telat->bind_param("ss", $tgl_hari_ini, $kelas_diampu);
    $stmt_telat->execute();
    $total_telat = $stmt_telat->get_result()->fetch_assoc()['total'];

    $stmt_count_ba = $conn->prepare("SELECT COUNT(*) as total FROM siswa 
        LEFT JOIN absensi ON siswa.nis = absensi.nis AND DATE(absensi.waktu_masuk) = ? 
        WHERE absensi.nis IS NULL AND siswa.kelas = ?");
    $stmt_count_ba->bind_param("ss", $tgl_hari_ini, $kelas_diampu);
}

$stmt_count_ba->execute();
$total_tidak_hadir = $stmt_count_ba->get_result()->fetch_assoc()['total'];
$persentase = $total_siswa > 0 ? round(($total_hadir / $total_siswa) * 100) : 0;

// --- 4. LOGIKA PAGINATION BELUM ABSEN ---
$limit_ba = 10; 
$halaman_ba = isset($_GET['p_ba']) ? (int)$_GET['p_ba'] : 1;
$offset_ba = ($halaman_ba - 1) * $limit_ba;
$total_hal_ba = ($total_tidak_hadir > 0) ? ceil($total_tidak_hadir / $limit_ba) : 1;

if($role == 'admin' || $role == 'piket'){
    $stmt_ba = $conn->prepare("SELECT siswa.nama, siswa.kelas, siswa.foto FROM siswa 
        LEFT JOIN absensi ON siswa.nis = absensi.nis AND DATE(absensi.waktu_masuk) = ? 
        WHERE absensi.nis IS NULL ORDER BY siswa.kelas ASC, siswa.nama ASC LIMIT ? OFFSET ?");
    $stmt_ba->bind_param("sii", $tgl_hari_ini, $limit_ba, $offset_ba);
} else { 
    $stmt_ba = $conn->prepare("SELECT siswa.nama, siswa.kelas, siswa.foto FROM siswa 
        LEFT JOIN absensi ON siswa.nis = absensi.nis AND DATE(absensi.waktu_masuk) = ? 
        WHERE absensi.nis IS NULL AND siswa.kelas = ? ORDER BY siswa.nama ASC LIMIT ? OFFSET ?");
    $stmt_ba->bind_param("ssii", $tgl_hari_ini, $kelas_diampu, $limit_ba, $offset_ba);
}
$stmt_ba->execute();
$q_ba_paginated = $stmt_ba->get_result();

// --- 5. LOGIKA PAGINATION WA QUEUE ---
$limit_wa = 10; 
$halaman_wa = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset_wa = ($halaman_wa - 1) * $limit_wa;

$total_wa_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM wa_queue");
$total_wa_data = mysqli_fetch_assoc($total_wa_query)['total'];
$total_wa_hal = ($total_wa_data > 0) ? ceil($total_wa_data / $limit_wa) : 1;

$stmt_wa = $conn->prepare("SELECT q.*, s.nama as nama_siswa 
            FROM wa_queue q 
            LEFT JOIN siswa s ON q.target = s.no_hp_ortu 
            ORDER BY q.created_at DESC LIMIT ? OFFSET ?");
$stmt_wa->bind_param("ii", $limit_wa, $offset_wa);
$stmt_wa->execute();
$query_wa = $stmt_wa->get_result();

// --- 6. PENGATURAN TAMBAHAN ---
if(isset($_POST['bersihkan_wa'])){
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Akses Ilegal!");
    }
    $stmt_del = $conn->prepare("DELETE FROM wa_queue WHERE status = 'sent'");
    $stmt_del->execute();
    echo "<script>alert('Riwayat dibersihkan!'); window.location='dashboard.php';</script>";
    exit;
}

if(isset($_POST['kirim_pengingat'])){
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Akses Ilegal!");
    }
    
    // Ambil siswa belum absen
    if($role == 'admin' || $role == 'piket'){
        $stmt_unabs = $conn->prepare("SELECT siswa.nis, siswa.nama, siswa.no_hp_ortu FROM siswa 
            LEFT JOIN absensi ON siswa.nis = absensi.nis AND DATE(absensi.waktu_masuk) = ? 
            WHERE absensi.nis IS NULL");
        $stmt_unabs->bind_param("s", $tgl_hari_ini);
    } else {
        $stmt_unabs = $conn->prepare("SELECT siswa.nis, siswa.nama, siswa.no_hp_ortu FROM siswa 
            LEFT JOIN absensi ON siswa.nis = absensi.nis AND DATE(absensi.waktu_masuk) = ? 
            WHERE absensi.nis IS NULL AND siswa.kelas = ?");
        $stmt_unabs->bind_param("ss", $tgl_hari_ini, $kelas_diampu);
    }
    $stmt_unabs->execute();
    $res_unabs = $stmt_unabs->get_result();
    
    $pesan_template = "Assalamualaikum. Menginformasikan bahwa Ananda [nama] belum melakukan absensi masuk sekolah hari ini. Mohon konfirmasinya. Terima kasih.";
    
    $count = 0;
    while($s = $res_unabs->fetch_assoc()){
        $pesan = str_replace("[nama]", $s['nama'], $pesan_template);
        $hp = $s['no_hp_ortu'] ?? '';
        $nis = $s['nis'];
        
        if(!empty($hp)){
            // Cek apakah sudah ada antrean pengingat untuk siswa ini hari ini
            $stmt_check_q = $conn->prepare("SELECT COUNT(*) as exist FROM wa_queue WHERE nis = ? AND DATE(created_at) = ? AND message LIKE '%belum melakukan absensi%'");
            $stmt_check_q->bind_param("ss", $nis, $tgl_hari_ini);
            $stmt_check_q->execute();
            $exist = $stmt_check_q->get_result()->fetch_assoc()['exist'];
            
            if($exist == 0){
                $stmt_ins_q = $conn->prepare("INSERT INTO wa_queue (nis, target, message, status) VALUES (?, ?, ?, 'pending')");
                $stmt_ins_q->bind_param("sss", $nis, $hp, $pesan);
                $stmt_ins_q->execute();
                $count++;
            }
        }
    }
    
    echo "<script>alert('Berhasil membuat $count antrean pesan pengingat WhatsApp.'); window.location='dashboard.php';</script>";
    exit;
}

$querySetting = mysqli_query($conn, "SELECT timezone FROM pengaturan WHERE id=1");
$sett = mysqli_fetch_assoc($querySetting);
$timezone_aktif = $sett['timezone'] ?? 'Asia/Jakarta';

$daftar_hari = array('Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu');
$daftar_bulan = array('January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember');
$tgl_indo = $daftar_hari[date('l')] . ', ' . date('d ') . $daftar_bulan[date('F')] . date(' Y');

// Greeting & Inisial
$hour = (int)date('H');
if ($hour >= 5 && $hour < 11) {
    $ucapan_waktu = "pagi";
} elseif ($hour >= 11 && $hour < 15) {
    $ucapan_waktu = "siang";
} elseif ($hour >= 15 && $hour < 18) {
    $ucapan_waktu = "sore";
} else {
    $ucapan_waktu = "malam";
}

$words = explode(" ", $nama_tampil);
$initials = "";
foreach ($words as $w) {
    $initials .= strtoupper(substr($w, 0, 1));
}
$initials = substr($initials, 0, 2);
if (empty($initials)) $initials = "AD";

include 'header.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Porcalabs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc; 
            min-height: 100vh; 
            margin: 0;
            padding: 0;
        }

        /* Hide standard top navbar on desktop */
        .navbar-custom {
            display: none !important;
        }
        body {
            padding-top: 0 !important;
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
            gap: 0.5rem;
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

        /* Main Content style */
        .main-content {
            margin-left: 280px;
            padding: 2.5rem 3rem;
            min-height: 100vh;
            background-color: #f8fafc;
        }

        /* Header UI */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }
        .header-profile-box {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .btn-notification {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-notification:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .btn-notification .dot {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 8px;
            height: 8px;
            background: #f97316;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }
        .profile-pill {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 6px 16px 6px 6px;
            border-radius: 30px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        .profile-pill:hover {
            border-color: #cbd5e1;
        }
        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .profile-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .profile-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
        }
        .profile-role {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Stats Row */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 125px;
            transition: all 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.02);
        }
        .stat-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .stat-header i {
            font-size: 0.95rem;
        }
        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.25rem;
            line-height: 1.1;
        }
        .stat-subtext {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
            z-index: 1;
        }
        .stat-progress {
            height: 6px;
            background: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        .stat-progress-bar {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
        }
        .stat-decor {
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            opacity: 0.05;
            pointer-events: none;
            transition: all 0.3s;
        }
        .stat-card:hover .stat-decor {
            transform: scale(1.15);
            opacity: 0.08;
        }
        .stat-decor.blue { background: #3b82f6; }
        .stat-decor.green { background: #10b981; }
        .stat-decor.purple { background: #8b5cf6; }
        .stat-decor.red { background: #ef4444; }
        .stat-decor.orange { background: #f59e0b; }

        /* Grid Cards */
        .grid-container {
            display: grid;
            grid-template-columns: 8.2fr 3.8fr;
            gap: 1.5rem;
        }
        .left-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Card Style */
        .content-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 1.75rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.01), 0 2px 4px -2px rgb(0 0 0 / 0.01);
        }
        .card-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }
        .card-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.25rem;
            margin-bottom: 0;
        }

        /* Quick Access Grid */
        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        .quick-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        .quick-btn:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
        }
        .quick-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }
        .quick-info {
            display: flex;
            flex-direction: column;
        }
        .quick-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
        }

        @media (max-width: 991.98px) {
            .quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }
        }
        @media (max-width: 575.98px) {
            .quick-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }

        /* Unabsent List */
        .ba-search-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }
        .ba-search-input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .ba-search-input:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .ba-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .ba-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 380px;
            overflow-y: auto;
            padding-right: 2px;
        }
        .ba-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: all 0.2s;
        }
        .ba-item:hover {
            background: #f1f5f9;
        }
        .ba-avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fee2e2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }
        .ba-student-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e293b;
        }
        .ba-student-class {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Actions Box */
        .action-box-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .action-box-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 1.25rem;
            line-height: 1.4;
        }

        /* Custom scrollbar for Belum Absen list */
        .ba-list::-webkit-scrollbar {
            width: 4px;
        }
        .ba-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .ba-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .ba-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Responsiveness for mobile screens */
        @media (max-width: 991.98px) {
            .sidebar-left {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 1.5rem !important;
            }
            .navbar-custom {
                display: flex !important; /* Show top navbar on mobile! */
            }
            body {
                padding-top: 80px !important; /* Restore padding on mobile */
            }
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .grid-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Main Content Area -->
<div class="main-content">
    <!-- Header Row -->
    <div class="header-row">
        <div>
            <div class="text-muted small fw-bold mb-1"><?= xss($tgl_indo) ?></div>
            <h2 class="fw-extrabold text-dark m-0" style="font-weight: 800; letter-spacing: -0.5px;">Selamat <?= $ucapan_waktu ?>, <?= xss($nama_tampil) ?> 👋</h2>
            <div class="text-muted small">Berikut ringkasan aktivitas sekolah hari ini.</div>
        </div>
        
        <div class="header-profile-box">
            <div class="btn-notification">
                <i class="bi bi-bell-fill text-muted"></i>
                <div class="dot"></div>
            </div>
            
            <a href="#" class="profile-pill">
                <div class="profile-avatar"><?= $initials ?></div>
                <div class="profile-info">
                    <span class="profile-name"><?= xss($nama_tampil) ?></span>
                    <span class="profile-role"><?= xss(ucwords($role)) ?></span>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Stats Row -->
    <div class="stats-grid">
        <!-- 1. Total Siswa -->
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <i class="bi bi-people-fill text-primary"></i> Total Siswa
                </div>
                <div class="stat-value text-primary"><?= (int)$total_siswa ?></div>
            </div>
            <div class="stat-subtext">Siswa aktif</div>
            <div class="stat-decor blue"></div>
        </div>
        
        <!-- 2. Hadir -->
        <a href="grafik_kehadiran.php" class="text-decoration-none h-100">
            <div class="stat-card">
                <div>
                    <div class="stat-header">
                        <i class="bi bi-check-circle-fill text-success"></i> Hadir
                    </div>
                    <div class="stat-value text-success"><?= (int)$total_hadir ?></div>
                </div>
                <div class="stat-subtext"><?= $total_hadir > 0 ? "$total_hadir siswa sudah hadir" : "Belum ada tap masuk" ?></div>
                <div class="stat-decor green"></div>
            </div>
        </a>
        
        <!-- 3. Terlambat -->
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <i class="bi bi-clock-fill text-purple"></i> Terlambat
                </div>
                <div class="stat-value text-purple" style="color: #8b5cf6 !important;"><?= (int)$total_telat ?></div>
            </div>
            <div class="stat-subtext">Hari ini</div>
            <div class="stat-decor purple"></div>
        </div>
        
        <!-- 4. Belum Absen -->
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <i class="bi bi-x-circle-fill text-danger"></i> Belum Absen
                </div>
                <div class="stat-value text-danger"><?= (int)$total_tidak_hadir ?></div>
            </div>
            <div class="stat-subtext">Perlu ditindaklanjuti</div>
            <div class="stat-decor red"></div>
        </div>
        
        <!-- 5. Kehadiran -->
        <a href="monitoring_kelas.php" class="text-decoration-none h-100">
            <div class="stat-card">
                <div>
                    <div class="stat-header">
                        <i class="bi bi-percent text-warning"></i> Kehadiran
                    </div>
                    <div class="stat-value text-warning" style="color: #f59e0b !important;"><?= (int)$persentase ?>%</div>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: <?= (int)$persentase ?>%; background: #f59e0b;"></div>
                </div>
                <div class="stat-decor orange"></div>
            </div>
        </a>
    </div>
    
    <!-- Grid Panels -->
    <div class="grid-container">
        <!-- Left column -->
        <div class="left-column">
            <!-- Akses Cepat -->
            <div class="content-card">
                <div class="card-title-row">
                    <div>
                        <h5 class="card-title">Akses cepat</h5>
                        <p class="card-subtitle">Fitur yang paling sering digunakan.</p>
                    </div>
                </div>
                
                <div class="quick-grid">
                    <!-- 1. SCAN QR -->
                    <a href="index.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #eff6ff; color: #3b82f6;"><i class="bi bi-qr-code-scan"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Scan QR</span>
                        </div>
                    </a>
                    
                    <!-- 2. SCAN WAJAH -->
                    <a href="scan_wajah.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #f5f3ff; color: #8b5cf6;"><i class="bi bi-camera"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Scan Wajah</span>
                        </div>
                    </a>
                    
                    <!-- 3. SCAN RFID -->
                    <a href="scan_rfid.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #ecfeff; color: #06b6d4;"><i class="bi bi-credit-card-2-front"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Scan RFID</span>
                        </div>
                    </a>
                    
                    <!-- 4. LAPORAN -->
                    <?php if($role == 'admin' || $role == 'walikelas'): ?>
                    <a href="laporan.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #fdf2f8; color: #ec4899;"><i class="bi bi-bar-chart-line-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Laporan</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 5. INPUT MANUAL -->
                    <?php if($role == 'admin' || $role == 'walikelas'): ?>
                    <a href="input_manual.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #fff7ed; color: #f97316;"><i class="bi bi-pencil-square"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Input Manual</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 6. TOKEN -->
                    <?php if($role == 'admin'): ?>
                    <a href="buat_token.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #fefce8; color: #eab308;"><i class="bi bi-key-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Token</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 7. SISWA -->
                    <?php if($role == 'admin'): ?>
                    <a href="data_siswa.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #eff6ff; color: #2563eb;"><i class="bi bi-mortarboard-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Siswa</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 8. GURU -->
                    <?php if($role == 'admin'): ?>
                    <a href="data_guru.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #e0e7ff; color: #4f46e5;"><i class="bi bi-person-workspace"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Guru</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 9. KELAS -->
                    <?php if($role == 'admin'): ?>
                    <a href="data_kelas.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #fafaf9; color: #78716c;"><i class="bi bi-building-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Lembaga</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 10. REKAP -->
                    <?php if($role == 'admin' || $role == 'walikelas'): ?>
                    <a href="rekap_bulanan.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #f0fdfa; color: #0d9488;"><i class="bi bi-calendar-check-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Rekap</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 11. USER -->
                    <?php if($role == 'admin'): ?>
                    <a href="data_user.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #eff6ff; color: #1d4ed8;"><i class="bi bi-person-badge-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">User</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 12. SETTING -->
                    <?php if($role == 'admin'): ?>
                    <a href="pengaturan.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #f8fafc; color: #64748b;"><i class="bi bi-gear-wide-connected"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Setting</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 13. BACKUP -->
                    <?php if($role == 'admin'): ?>
                    <a href="backup_database.php" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #fff5f5; color: #e53e3e;"><i class="bi bi-database-fill-down"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Backup</span>
                        </div>
                    </a>
                    <?php endif; ?>
                    
                    <!-- 14. DEVELOPER -->
                    <?php if($role == 'admin'): ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#devModal" class="quick-btn">
                        <div class="quick-icon-box" style="background-color: #eff6ff; color: #0284c7;"><i class="bi bi-laptop-fill"></i></div>
                        <div class="quick-info">
                            <span class="quick-name">Developer</span>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Aktivitas WhatsApp -->
            <div class="content-card">
                <div class="card-title-row mb-3">
                    <div>
                        <h5 class="card-title">Aktivitas WhatsApp</h5>
                        <p class="card-subtitle">Status pengiriman notifikasi terbaru.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 text-xs">
                            <span id="wa-count">0</span> antrean
                            <i id="wa-loader" class="bi bi-arrow-repeat spin-icon ms-1" style="display: none;"></i>
                        </span>
                        <form method="POST" class="m-0">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" name="bersihkan_wa" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold">Bersihkan</button>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 border-0">WAKTU</th>
                                <th class="border-0">SISWA</th>
                                <th class="border-0">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($query_wa) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_wa)): ?>
                                <tr style="background: transparent;">
                                    <td class="ps-3 text-muted border-0 align-middle"><?= xss(date('H:i', strtotime($row['created_at']))) ?></td>
                                    <td class="fw-bold text-dark border-0 align-middle"><?= xss($row['nama_siswa'] ?? $row['target']) ?></td>
                                    <td class="border-0 align-middle">
                                        <?php if ($row['status'] == 'sent'): ?>
                                            <span class="text-success fw-bold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                <span style="font-size: 0.65rem;">●</span> Terkirim
                                            </span>
                                        <?php else: ?>
                                            <span class="text-warning fw-bold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                                <span style="font-size: 0.65rem;">●</span> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted small border-0">Tidak ada antrean pesan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Right column -->
        <div class="right-column">
            <!-- Belum Absen -->
            <div class="content-card">
                <div class="card-title-row align-items-start mb-2">
                    <div>
                        <h5 class="card-title">Belum absen</h5>
                        <p class="card-subtitle"><?= $total_tidak_hadir ?> siswa belum melakukan tap.</p>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1 text-xs fw-bold"><?= $total_tidak_hadir ?> siswa</span>
                </div>
                
                <div class="ba-search-wrapper">
                    <i class="bi bi-search ba-search-icon"></i>
                    <input type="text" id="search-ba" class="ba-search-input" placeholder="Cari siswa...">
                </div>
                
                <div class="ba-list mb-3">
                    <?php if($q_ba_paginated->num_rows > 0): ?>
                        <?php while($siswa = $q_ba_paginated->fetch_assoc()): ?>
                            <div class="ba-item">
                                <?php 
                                $path_ba = "img/siswa/" . $siswa['foto'];
                                if(!empty($siswa['foto']) && file_exists($path_ba)): 
                                ?>
                                    <img src="<?= $path_ba ?>" class="rounded-circle border border-white" style="width:36px; height:36px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="ba-avatar-circle"><i class="bi bi-person-fill"></i></div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <div class="ba-student-name"><?= xss($siswa['nama']) ?></div>
                                    <div class="ba-student-class"><?= xss($siswa['kelas']) ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success fs-2"></i>
                            <p class="text-muted small fw-bold mt-2 mb-0">Semua Hadir!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination for Belum Absen -->
                <?php if($total_hal_ba > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3 mb-3 px-1">
                        <a href="?p_ba=<?= max(1, $halaman_ba - 1) ?>" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2.5 <?= ($halaman_ba <= 1) ? 'disabled' : '' ?>" style="font-size: 0.75rem;">
                            <i class="bi bi-chevron-left"></i> Prev
                        </a>
                        <span class="text-muted text-xs font-bold" style="font-size: 0.75rem;">Hal <?= $halaman_ba ?> dari <?= $total_hal_ba ?></span>
                        <a href="?p_ba=<?= min($total_hal_ba, $halaman_ba + 1) ?>" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2.5 <?= ($halaman_ba >= $total_hal_ba) ? 'disabled' : '' ?>" style="font-size: 0.75rem;">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                <?php endif; ?>
                
                <a href="monitoring_kelas.php" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold text-sm">Lihat semua siswa</a>
            </div>
            
            <!-- Saran Tindakan -->
            <div class="content-card">
                <h6 class="action-box-title">Saran tindakan</h6>
                <p class="action-box-desc">Kirim pengingat WhatsApp kepada wali siswa yang belum melakukan absensi.</p>
                <form method="POST" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" name="kirim_pengingat" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold text-sm">Kirim pengingat</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Live Search Belum Absen
    document.getElementById('search-ba').addEventListener('input', function(e) {
        const val = e.target.value.toLowerCase();
        document.querySelectorAll('.ba-item').forEach(item => {
            const name = item.querySelector('.ba-student-name').textContent.toLowerCase();
            const kelas = item.querySelector('.ba-student-class').textContent.toLowerCase();
            if(name.includes(val) || kelas.includes(val)) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    });
</script>
<script>
    function monitorWA() {
        fetch('wa_status.php').then(res => res.json()).then(data => {
            document.getElementById('wa-count').innerText = data.pending + " ";
            if(data.pending > 0) fetch('wa_worker.php');
        });
    }
    setInterval(monitorWA, 20000); monitorWA();
</script>
<script>
// Fungsi untuk menjalankan pengiriman secara berulang (Recursive)
function jalankanPengirimanOtomatis() {
    const waCountEl = document.getElementById('wa-count');
    const waLoader = document.getElementById('wa-loader');

    // 1. Cek jumlah antrian ke server
    fetch('wa_status.php')
        .then(response => response.json())
        .then(data => {
            // Update angka di UI
            waCountEl.innerText = data.pending;

            // 2. Jika ada pesan (misal 109 pesan), jalankan worker
            if (parseInt(data.pending) > 0) {
                // Munculkan animasi loading
                waLoader.style.display = 'inline-block';
                waLoader.classList.add('bi-spin'); 

                // Panggil file worker untuk mengirim 5 pesan
                fetch('wa_worker.php')
                    .then(() => {
                        console.log('5 Pesan berhasil diproses...');
                        // Tunggu 3 detik, lalu cek & kirim lagi sampai habis
                        setTimeout(jalankanPengirimanOtomatis, 3000);
                    })
                    .catch(err => {
                        console.error('Gagal memproses wa_worker:', err);
                        waLoader.style.display = 'none';
                    });
            } else {
                // Jika antrian sudah 0, matikan loading & cek lagi 1 menit kemudian
                waLoader.style.display = 'none';
                setTimeout(jalankanPengirimanOtomatis, 60000);
            }
        })
        .catch(err => console.error('Gagal mengambil status:', err));
}

// Tambahkan CSS sederhana agar icon loading bisa berputar
const style = document.createElement('style');
style.innerHTML = `
    @keyframes spin { 100% { transform:rotate(360deg); } }
    .bi-spin { animation: spin 2s linear infinite; display: inline-block; }
`;
document.head.appendChild(style);

// Jalankan fungsi saat halaman dashboard pertama kali dibuka
document.addEventListener('DOMContentLoaded', jalankanPengirimanOtomatis);
</script>

<?php
// Developer Modal
$modal_dev_porcalabs = "PGRpdiBjbGFzcz0ibW9kYWwgZmFkZSIgaWQ9ImRldk1vZGFsIiB0YWJpbmRleD0iLTEiIGFyaWEtaGlkZGVuPSJ0cnVlIj48ZGl2IGNsYXNzPSJtb2RhbC1kaWFsb2cgbW9kYWwtZGlhbG9nLWNlbnRlcmVkIj48ZGl2IGNsYXNzPSJtb2RhbC1jb250ZW50IHNoYWRvdy1sZyIgc3R5bGU9ImJvcmRlci1yYWRpdXM6IDI0cHg7IGJvcmRlcjogbm9uZTsgYmFja2dyb3VuZDogcmdiYSgyNTUsMjU1LDI1NSwwLjk1KTsgYmFja2Ryb3AtZmlsdGVyOiBibHVyKDEwcHgpOyI+PGRpdiBjbGFzcz0ibW9kYWwtaGVhZGVyIGJvcmRlci0wIHBiLTAganVzdGlmeS1jb250ZW50LWVuZCBwLTMiPjxidXR0b24gdHlwZT0iYnV0dG9uIiBjbGFzcz0iYnRuLWNsb3NlIiBkYXRhLWJzLWRpc21pc3M9Im1vZGFsIiBhcmlhLWxhYmVsPSJDbG9zZSI+PC9idXR0b24+PC9kaXY+PGRpdiBjbGFzcz0ibW9kYWwtYm9keSB0ZXh0LWNlbnRlciBweC00IHBiLTQgbXQtbjMiPjxkaXYgY2xhc3M9Im1iLTMiPjxpbWcgc3JjPSJodHRwczovL2ltZy5pY29uczguY29tLzNkLWZsdWVuY3kvOTQvYm90LnBuZyIgd2lkdGg9Ijg1IiBzdHlsZT0iZmlsdGVyOiBkcm9wLXNoYWRvdygwIDEwcHggMTBweCByZ2JhKDAsMCwwLDAuMTUpKTsiPjwvZGl2PjxoNCBjbGFzcz0iZnctYm9sZGVyIHRleHQtZGFyayBtYi0xIj5Qb3JjYWxhYnM8L2g0PjxwIGNsYXNzPSJ0ZXh0LW11dGVkIHNtYWxsIHB4LTMgbWItNCI+U2lzdGVtIEFic2Vuc2kgVGVycGFkdSBkZW5nYW4gdGVrbm9sb2dpIFFSLCBSRklEICYgRmFjZSBTY2FubmVyLiBUZXJpbnRlZ3Jhc2kgcGVudWggZGVuZ2FuIG5vdGlmaWthc2kgV2hhdHNBcHAsIFRlbGVncmFtIGRhbiBFbWFpbCBzZWNhcmEgcmVhbC10aW1lLjwvcD48ZGl2IGNsYXNzPSJkLWdyaWQgZ2FwLTMgcHgtMyI+PGEgaHJlZj0iaHR0cHM6Ly93d3cudGlrdG9rLmNvbS9AcG9yY2FsYWJzIiB0YXJnZXQ9Il9ibGFuayIgY2xhc3M9ImJ0biByb3VuZGVkLXBpbGwgZnctYm9sZCBweS0yIGQtZmxleCBhbGlnbi1pdGVtcy1jZW50ZXIganVzdGlmeS1jb250ZW50LWNlbnRlciB0ZXh0LXdoaXRlIHNoYWRvdy1zbSIgc3R5bGU9ImJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcsICMwMTAxMDEsICNlZTFkNTIpOyBib3JkZXI6IG5vbmU7Ij48aSBjbGFzcz0iYmkgYmktdGlrdG9rIGZzLTUgbWUtMiI+PC9pPiBUaWtUb2sgUG9yY2FsYWJzPC9hPjxhIGhyZWY9Imh0dHBzOi8vd2EubWUvNjI4NTE1NjExMDM5NSIgdGFyZ2V0PSJfYmxhbmsiIGNsYXNzPSJidG4gcm91bmRlZC1waWxsIGZ3LWJvbGQgcHktMiBkLWZsZXggYWxpZ24taXRlbXMtY2VudGVyIGp1c3RpZnktY29udGVudC1jZW50ZXIgdGV4dC13aGl0ZSBzaGFkb3ctc20iIHN0eWxlPSJiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLCAjMjVkMzY2LCAjMTI4YzdlKTsgYm9yZGVyOiBub25lIj48aSBjbGFzcz0iYmkgYmktd2hhdHNhcHAgZnMtNSBtZS0yIj48L2k+IFdoYXRzQXBwIFBvcmNhbGFiczwvYT48L2Rpdj48L2Rpdj48ZGl2IGNsYXNzPSJtb2RhbC1mb290ZXIganVzdGlmeS1jb250ZW50LWNlbnRlciBib3JkZXItMCBiZy1saWdodCBweS0zIj48c3BhbiBjbGFzcz0ic21hbGwgdGV4dC1tdXRlZCBmdy1ib2xkIj4mY29weTsgW1RBSFVOXSB8IERldmVsb3BlciBieSBQb3JjYWxhYnM8L3NwYW4+PC9kaXY+PC9kaXY+PC9kaXY+PC9kaXY+";

$html_final = base64_decode($modal_dev_porcalabs);
$html_final = str_replace("[TAHUN]", date('Y'), $html_final);

echo $html_final;
?>
</body>
</html>