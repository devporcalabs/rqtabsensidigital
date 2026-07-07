<?php
ob_start();

$host = "localhost";
$user = "homestead";
$pass = "secret";
$db   = "absensi-digital";

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

if (!function_exists('kirim_wa')) {
    function kirim_wa($no_hp, $pesan, $wa_api_url, $wa_token) {
        if (empty($no_hp) || empty($wa_api_url) || empty($wa_token)) {
            return false;
        }

        // Format number to 62...
        $noTujuan = preg_replace('/[^0-9]/', '', $no_hp);
        if (substr($noTujuan, 0, 1) === '0') {
            $noTujuan = '62' . substr($noTujuan, 1);
        } elseif (substr($noTujuan, 0, 2) === '62') {
            // Already starts with 62
        } else {
            $noTujuan = '62' . $noTujuan;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $wa_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'api_key' => $wa_token,
                'receiver' => $noTujuan,
                'data' => array('message' => $pesan)
            )),
        ));
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
?>