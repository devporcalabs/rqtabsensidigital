<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

// 1. Validasi Login & Role
if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Sesi login telah berakhir!']);
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin' && $role !== 'kantin') {
    echo json_encode(['status' => 'error', 'pesan' => 'Akses ditolak!']);
    exit;
}

// 2. Validasi CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'pesan' => 'Akses ilegal terdeteksi (CSRF Token invalid)!']);
    exit;
}

// 3. Sanitasi Input
$rfid_uid = trim($_POST['rfid_uid'] ?? '');
$nominal = (int)($_POST['nominal'] ?? 0);

if (empty($rfid_uid)) {
    echo json_encode(['status' => 'error', 'pesan' => 'RFID UID tidak boleh kosong!']);
    exit;
}

if ($nominal <= 0) {
    echo json_encode(['status' => 'error', 'pesan' => 'Nominal transaksi harus lebih dari Rp 0!']);
    exit;
}

// 4. Cari data siswa berdasarkan rfid_uid atau nis (sebagai cadangan) - toleran terhadap perbedaan leading zero
$rfid_clean = ltrim($rfid_uid, '0');
$stmt_siswa = $conn->prepare("SELECT * FROM siswa WHERE rfid_uid = ? OR nis = ? OR TRIM(LEADING '0' FROM rfid_uid) = ? OR TRIM(LEADING '0' FROM nis) = ? LIMIT 1");
$stmt_siswa->bind_param("ssss", $rfid_uid, $rfid_uid, $rfid_clean, $rfid_clean);
$stmt_siswa->execute();
$siswa = $stmt_siswa->get_result()->fetch_assoc();

if (!$siswa) {
    echo json_encode(['status' => 'error', 'pesan' => 'Kartu RFID/NIS tidak terdaftar!']);
    exit;
}

$nis = $siswa['nis'];
$nama = $siswa['nama'];
$kelas = $siswa['kelas'];
$foto = $siswa['foto'];
$saldo_awal = (int)($siswa['saldo'] ?? 0);

// 5. Cek kecukupan saldo
if ($saldo_awal < $nominal) {
    echo json_encode([
        'status' => 'error',
        'pesan' => 'Saldo tidak mencukupi! Sisa saldo saat ini: Rp ' . number_format($saldo_awal, 0, ',', '.'),
        'nama' => $nama,
        'kelas' => $kelas,
        'foto' => $foto,
        'saldo_akhir' => $saldo_awal
    ]);
    exit;
}

$saldo_akhir = $saldo_awal - $nominal;
$operator = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Kasir Kantin';

// 6. Jalankan Transaksi Database
$conn->begin_transaction();

try {
    // A. Potong saldo siswa
    $stmt_update = $conn->prepare("UPDATE siswa SET saldo = ? WHERE nis = ?");
    $stmt_update->bind_param("is", $saldo_akhir, $nis);
    $stmt_update->execute();

    // B. Catat mutasi transaksi
    $keterangan = "Belanja di Kantin";
    $waktu_sekarang = date('Y-m-d H:i:s');
    $stmt_trans = $conn->prepare("INSERT INTO kantin_transaksi (nis, tipe, nominal, saldo_awal, saldo_akhir, keterangan, waktu, operator) VALUES (?, 'debet', ?, ?, ?, ?, ?, ?)");
    $stmt_trans->bind_param("siiiiss", $nis, $nominal, $saldo_awal, $saldo_akhir, $keterangan, $waktu_sekarang, $operator);
    $stmt_trans->execute();

    // C. Ambil Pengaturan WA / Telegram untuk pengiriman notifikasi
    $stmt_setting = $conn->prepare("SELECT wa_mode, tg_bot_token FROM pengaturan WHERE id = 1");
    $stmt_setting->execute();
    $setting = $stmt_setting->get_result()->fetch_assoc();

    $nominal_rp = "Rp " . number_format($nominal, 0, ',', '.');
    $saldo_akhir_rp = "Rp " . number_format($saldo_akhir, 0, ',', '.');

    // D. Buat pesan notifikasi
    $pesan_notif = "Assalamualaikum Wr. Wb. Bpk/Ibu, menginfokan bahwa ananda *{$nama}* ({$kelas}) telah melakukan pembayaran jajan di Kantin Sekolah sebesar *{$nominal_rp}* pada pukul " . date('H:i') . ". Sisa saldo E-Kantin saat ini: *{$saldo_akhir_rp}*. Terima kasih.";

    // E. Queue WhatsApp jika wa_mode = 1 dan ada nomor HP orang tua
    if (($setting['wa_mode'] ?? 0) == 1 && !empty($siswa['no_hp_ortu'])) {
        $stmt_wa = $conn->prepare("INSERT INTO wa_queue (nis, target, message, status) VALUES (?, ?, ?, 'pending')");
        $stmt_wa->bind_param("sss", $nis, $siswa['no_hp_ortu'], $pesan_notif);
        $stmt_wa->execute();
    }

    // F. Kirim Telegram jika bot token dan chat ID orang tua tersedia
    if (!empty($setting['tg_bot_token']) && !empty($siswa['telegram_chat_id'])) {
        include_once 'fungsi_telegram.php';
        // Konversi markdown format WA (*) ke HTML format Telegram (<b>)
        $pesan_telegram = str_replace(['*', '_'], ['<b>', '<i>'], $pesan_notif);
        $pesan_telegram = str_replace(['</b><b>', '</i><i>'], ['', ''], $pesan_telegram);
        // Tambahkan tag penutup yang sesuai jika terjadi kesalahan format
        sendTelegram($siswa['telegram_chat_id'], $pesan_telegram, $setting['tg_bot_token']);
    }

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'pesan' => 'Pembayaran Belanja Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil diproses!',
        'nama' => $nama,
        'kelas' => $kelas,
        'foto' => $foto,
        'saldo_akhir' => $saldo_akhir
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
}
?>
