<?php
ob_start();

$host_header = $_SERVER['HTTP_HOST'] ?? '';

if (strpos($host_header, 'rqt.porcalabs.my.id') !== false) {
    $host = "localhost";
    $user = "sql_rqt";
    $pass = "sql_rqt"; // Sesuaikan jika password beda
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

$conn = mysqli_connect($host, $user, $pass, $db);

// Ambil timezone dari database
$q_time = mysqli_query($conn, "SELECT timezone FROM pengaturan WHERE id=1");
$res_time = mysqli_fetch_assoc($q_time);
$timezone_aktif = $res_time['timezone'] ?? 'Asia/Jakarta';

// Set timezone PHP
date_default_timezone_set($timezone_aktif);

// Set timezone MySQL agar fungsi NOW() di SQL juga ikut berubah
$now = new DateTime();
$mins = $now->getOffset() / 60;
$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;
$offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
mysqli_query($conn, "SET time_zone='$offset'");
?>