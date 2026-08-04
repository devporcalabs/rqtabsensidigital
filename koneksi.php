<?php
if (session_status() === PHP_SESSION_NONE) {
    ob_start();
}

// Check if server-specific config file exists
if (file_exists(__DIR__ . '/koneksi_env.php')) {
    include __DIR__ . '/koneksi_env.php';
} else {
    $host_header = $_SERVER['HTTP_HOST'] ?? '';

    if (strpos($host_header, 'rqt.porcalabs.my.id') !== false) {
        $host = "localhost";
        $user = "sql_rqt";
        $pass = "bee5ccfa4f5b18"; // Ganti di koneksi_env.php di server
        $db   = "sql_rqt";
    } elseif ($host_header == 'demo-absensi.porcalabs.com' || $host_header == '146.235.16.115') {
        $host = "localhost";
        $user = "demo_absensi";
        $pass = "dJm2xL58KieB3N5H";
        $db   = "demo_absensi";
    } else {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "absensi_rqtemi";
    }

    $conn = @mysqli_connect($host, $user, $pass, $db);
}

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set Charset UTF-8 untuk performa & kompatibilitas penuh
mysqli_set_charset($conn, "utf8mb4");

// Set timezone PHP & MySQL
$timezone_aktif = 'Asia/Jakarta';
$q_time = @mysqli_query($conn, "SELECT timezone FROM pengaturan WHERE id=1 LIMIT 1");
if ($q_time && $res_time = mysqli_fetch_assoc($q_time)) {
    $timezone_aktif = $res_time['timezone'] ?? 'Asia/Jakarta';
}
date_default_timezone_set($timezone_aktif);

// Set timezone offset MySQL
$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
@mysqli_query($conn, "SET time_zone='$offset'");
?>