<?php
/**
 * Script Inisialisasi & Auto-Check Tabel Database Modul SPP
 */
include_once __DIR__ . '/koneksi.php';

if (!function_exists('init_spp_tables')) {
    function init_spp_tables($conn) {
        $check = @mysqli_query($conn, "SHOW TABLES LIKE 'spp_tagihan'");
        if ($check && mysqli_num_rows($check) > 0) {
            return true; // Tabel sudah ada
        }

        $queries = [
            "CREATE TABLE IF NOT EXISTS `spp_jenis_tagihan` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nama` VARCHAR(100) NOT NULL,
                `nominal` DECIMAL(12,2) NOT NULL,
                `tahun_ajaran` VARCHAR(20) NOT NULL,
                `jatuh_tempo` DATE NOT NULL,
                `berlaku_untuk` VARCHAR(50) DEFAULT 'Semua',
                `aktif` TINYINT(1) DEFAULT 1,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

            "CREATE TABLE IF NOT EXISTS `spp_tagihan` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nis` VARCHAR(20) NOT NULL,
                `jenis_tagihan_id` INT NOT NULL,
                `nominal` DECIMAL(12,2) NOT NULL,
                `dibayar` DECIMAL(12,2) DEFAULT 0,
                `sisa` DECIMAL(12,2) NOT NULL,
                `status` ENUM('Belum Bayar', 'Sebagian', 'Lunas', 'Terlambat', 'Dibatalkan') DEFAULT 'Belum Bayar',
                `token` VARCHAR(64) UNIQUE NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`nis`),
                INDEX (`jenis_tagihan_id`),
                INDEX (`status`),
                INDEX (`token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

            "CREATE TABLE IF NOT EXISTS `spp_pembayaran` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nomor_transaksi` VARCHAR(50) UNIQUE NOT NULL,
                `tagihan_id` INT NOT NULL,
                `nis` VARCHAR(20) NOT NULL,
                `metode` ENUM('Tunai', 'Transfer', 'QRIS') NOT NULL,
                `nominal` DECIMAL(12,2) NOT NULL,
                `bukti_transfer` VARCHAR(255) DEFAULT NULL,
                `status_verifikasi` ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
                `catatan` TEXT DEFAULT NULL,
                `petugas_id` INT DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`tagihan_id`),
                INDEX (`nis`),
                INDEX (`status_verifikasi`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

            "CREATE TABLE IF NOT EXISTS `spp_pengaturan` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nama_bank` VARCHAR(50) DEFAULT 'Bank BCA',
                `no_rekening` VARCHAR(50) DEFAULT '1234567890',
                `atas_nama` VARCHAR(100) DEFAULT 'Rumah Quran Temi',
                `qris_image` VARCHAR(255) DEFAULT 'qris_default.png',
                `no_wa_bendahara` VARCHAR(30) DEFAULT '',
                `wa_template_tagihan` TEXT,
                `wa_template_lunas` TEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

            "CREATE TABLE IF NOT EXISTS `spp_log_notifikasi` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `tagihan_id` INT NOT NULL,
                `no_hp` VARCHAR(20) NOT NULL,
                `pesan` TEXT NOT NULL,
                `status` VARCHAR(20) DEFAULT 'Terkirim',
                `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`tagihan_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
        ];

        foreach ($queries as $q) {
            @mysqli_query($conn, $q);
        }

        // Pastikan ENUM role di tabel users mencakup 'bendahara'
        @mysqli_query($conn, "ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin','walikelas','piket','kantin','bendahara') NOT NULL DEFAULT 'walikelas'");
        
        // Pastikan kolom no_wa_bendahara ada pada tabel spp_pengaturan
        $check_col = @mysqli_query($conn, "SHOW COLUMNS FROM spp_pengaturan LIKE 'no_wa_bendahara'");
        if ($check_col && mysqli_num_rows($check_col) == 0) {
            @mysqli_query($conn, "ALTER TABLE `spp_pengaturan` ADD COLUMN `no_wa_bendahara` VARCHAR(30) DEFAULT ''");
        }

        $check_setting = @mysqli_query($conn, "SELECT COUNT(*) as total FROM spp_pengaturan");
        if ($check_setting) {
            $r_setting = mysqli_fetch_assoc($check_setting);
            if (($r_setting['total'] ?? 0) == 0) {
                $wa_tagihan = "Bismillah, Yth. Orang Tua dari {nama_siswa} ({kelas}).\n\nTagihan SPP {nama_tagihan} telah diterbitkan sebesar *Rp {nominal}* dengan jatuh tempo tanggal *{jatuh_tempo}*.\n\nLink pembayaran: {link_portal}\n\nTerima Kasih.";
                $wa_lunas   = "Alhamdulillah, Pembayaran {nama_tagihan} sebesar *Rp {nominal}* untuk {nama_siswa} telah DITERIMA dan VERIFIKASI (LUNAS).\n\nKwitansi digital: {link_kwitansi}\n\nTerima kasih atas partisipasinya.";

                $stmt_init = $conn->prepare("INSERT INTO spp_pengaturan (nama_bank, no_rekening, atas_nama, qris_image, wa_template_tagihan, wa_template_lunas) VALUES ('Bank BCA', '1234567890', 'Rumah Qur\'an Temi', 'qris_default.png', ?, ?)");
                if ($stmt_init) {
                    $stmt_init->bind_param("ss", $wa_tagihan, $wa_lunas);
                    $stmt_init->execute();
                }
            }
        }
        return true;
    }
}

if (isset($conn)) {
    init_spp_tables($conn);
}
