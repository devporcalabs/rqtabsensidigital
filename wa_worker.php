<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/src/Exception.php';
require 'libs/PHPMailer/src/PHPMailer.php';
require 'libs/PHPMailer/src/SMTP.php';
include 'koneksi.php';
include 'fungsi_wa.php';

// 1. AMBIL PENGATURAN GLOBAL
$q_set = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
$p = mysqli_fetch_assoc($q_set);

// 2. AMBIL ANTREAN 
// Mendukung pesan instan dan pesan terjadwal yang sudah memasuki waktu kirim
$query = mysqli_query($conn, "SELECT q.*, s.email, s.nama, s.telegram_chat_id 
                              FROM wa_queue q 
                              LEFT JOIN siswa s ON q.nis = s.nis 
                              WHERE q.status = 'pending' 
                                AND (q.scheduled_at IS NULL OR q.scheduled_at <= NOW())
                              ORDER BY q.id ASC LIMIT 10");

while($row = mysqli_fetch_assoc($query)) {
    $id = (int)$row['id'];
    $pesan_mentah = $row['message'];
    $no_hp = $row['target'];
    $chat_id = $row['telegram_chat_id'] ?? null;
    $target_email = $row['email'] ?? null;
    $is_spp = ($row['tipe'] ?? '') === 'spp_tagihan';
    $tagihan_id = (int)($row['tagihan_id'] ?? 0);

    // UPDATE STATUS KE 'sent' SEGERA (Agar tidak dieksekusi dobel)
    mysqli_query($conn, "UPDATE wa_queue SET status = 'sent', sent_at = NOW() WHERE id = $id");

    // --- LOGIKA KODE RAHASIA EMAIL ONLY ---
    $is_email_only = false;
    if (strpos($pesan_mentah, '[EMAIL_ONLY]') === 0) {
        $is_email_only = true;
        // Hapus teks '[EMAIL_ONLY]' dari pesan agar tidak ikut terkirim/terbaca oleh wali murid
        $pesan_fix = str_replace('[EMAIL_ONLY]', '', $pesan_mentah);
    } else {
        $pesan_fix = $pesan_mentah;
    }

    // --- A. JALUR WHATSAPP (Abaikan jika is_email_only aktif) ---
    if (!$is_email_only && !empty($no_hp) && !empty($p['wa_token'])) {
        $res = sendWa($no_hp, $pesan_fix, $p['wa_token'], $p['wa_api_url']);
        
        // Jika ini notifikasi SPP, catat status ke spp_log_notifikasi
        if ($is_spp && $tagihan_id > 0) {
            $res_arr = json_decode($res, true);
            $log_status = 'Terkirim';
            if (is_array($res_arr) && !((isset($res_arr['status']) && $res_arr['status'] == true) || (isset($res_arr['is_success']) && $res_arr['is_success'] == true))) {
                $log_status = 'Gagal: ' . ($res_arr['reason'] ?? ($res_arr['message'] ?? 'Error server WA'));
            }
            $stmt_log = $conn->prepare("INSERT INTO spp_log_notifikasi (tagihan_id, no_hp, pesan, status, sent_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt_log->bind_param("isss", $tagihan_id, $no_hp, $pesan_fix, $log_status);
            $stmt_log->execute();
        }
    }

    // --- B. JALUR TELEGRAM (Abaikan jika is_email_only aktif atau pesan SPP) ---
    if (!$is_email_only && !$is_spp && !empty($chat_id) && !empty($p['tg_bot_token'])) {
        $url_tg = "https://api.telegram.org/bot" . $p['tg_bot_token'] . "/sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($pesan_fix);
        @file_get_contents($url_tg);
    }

    // --- C. JALUR EMAIL (Selalu dieksekusi jika siswa punya email) ---
    if (!empty($target_email) && !empty($p['smtp_user'])) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $p['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $p['smtp_user'];
            $mail->Password   = $p['smtp_pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $p['smtp_port'];

            $mail->setFrom($p['smtp_user'], $p['nama_sekolah']);
            $mail->addAddress($target_email);
            $mail->isHTML(true);
            $mail->Subject = $is_spp ? ("Informasi Tagihan: " . ($row['nama'] ?? 'Siswa')) : ("Laporan Presensi: " . ($row['nama'] ?? 'Siswa'));
            
            // Format HTML Email yang lebih rapi
            $mail->Body    = "<div style='font-family:sans-serif; padding:20px; border:1px solid #eee; border-radius:10px;'>
                                <h3 style='color:#0d6efd;'>" . ($is_spp ? "Notifikasi Tagihan SPP" : "Notifikasi Kehadiran") . "</h3>
                                <p>" . nl2br($pesan_fix) . "</p>
                                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                                <small style='color: #888;'>Sistem Informasi <b>" . $p['nama_sekolah'] . "</b></small>
                              </div>";
            $mail->send();
        } catch (Exception $e) { }
    }

    // Jeda antar pengiriman (Pesan SPP 30 detik agar aman dari ban/spam, Absensi 2 detik)
    $delay = $is_spp ? 30 : 2;
    sleep($delay);
}
?>