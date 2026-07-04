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
$nis = trim($_POST['nis'] ?? '');
$nominal = (int)($_POST['nominal'] ?? 0);
$keterangan = trim($_POST['keterangan'] ?? '');

if (empty($nis)) {
    echo json_encode(['status' => 'error', 'pesan' => 'NIS siswa tidak boleh kosong!']);
    exit;
}

if ($nominal < 1000 || $nominal > 1000000) {
    echo json_encode(['status' => 'error', 'pesan' => 'Nominal top up minimal Rp 1.000 dan maksimal Rp 1.000.000!']);
    exit;
}

// 4. Cari data siswa berdasarkan nis
$stmt_siswa = $conn->prepare("SELECT * FROM siswa WHERE nis = ? LIMIT 1");
$stmt_siswa->bind_param("s", $nis);
$stmt_siswa->execute();
$siswa = $stmt_siswa->get_result()->fetch_assoc();

if (!$siswa) {
    echo json_encode(['status' => 'error', 'pesan' => 'Siswa tidak ditemukan!']);
    exit;
}

$nama = $siswa['nama'];
$kelas = $siswa['kelas'];
$saldo_awal = (int)($siswa['saldo'] ?? 0);
$saldo_akhir = $saldo_awal + $nominal;
$operator = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Admin/Kasir';

// 5. Jalankan Transaksi Database
$conn->begin_transaction();

try {
    // A. Tambahkan saldo siswa
    $stmt_update = $conn->prepare("UPDATE siswa SET saldo = ? WHERE nis = ?");
    $stmt_update->bind_param("is", $saldo_akhir, $nis);
    $stmt_update->execute();

    // B. Catat mutasi transaksi
    $ket_final = !empty($keterangan) ? $keterangan : "Top Up Saldo E-Kantin";
    $waktu_sekarang = date('Y-m-d H:i:s');
    $stmt_trans = $conn->prepare("INSERT INTO kantin_transaksi (nis, tipe, nominal, saldo_awal, saldo_akhir, keterangan, waktu, operator) VALUES (?, 'kredit', ?, ?, ?, ?, ?, ?)");
    $stmt_trans->bind_param("siiiiss", $nis, $nominal, $saldo_awal, $saldo_akhir, $ket_final, $waktu_sekarang, $operator);
    $stmt_trans->execute();

    // C. Ambil Pengaturan WA / Telegram
    $stmt_setting = $conn->prepare("SELECT wa_mode, tg_bot_token FROM pengaturan WHERE id = 1");
    $stmt_setting->execute();
    $setting = $stmt_setting->get_result()->fetch_assoc();

    $nominal_rp = "Rp " . number_format($nominal, 0, ',', '.');
    $saldo_akhir_rp = "Rp " . number_format($saldo_akhir, 0, ',', '.');

    // D. Buat pesan notifikasi
    $pesan_notif = "Assalamualaikum Wr. Wb. Bpk/Ibu, menginfokan bahwa pengisian saldo (Top Up) kartu jajan E-Kantin untuk ananda *{$nama}* ({$kelas}) sebesar *{$nominal_rp}* berhasil diproses pada pukul " . date('H:i') . ". Saldo aktif ananda saat ini: *{$saldo_akhir_rp}*. Terima kasih.";

    // E. Queue WhatsApp jika wa_mode = 1
    if (($setting['wa_mode'] ?? 0) == 1 && !empty($siswa['no_hp_ortu'])) {
        $stmt_wa = $conn->prepare("INSERT INTO wa_queue (nis, target, message, status) VALUES (?, ?, ?, 'pending')");
        $stmt_wa->bind_param("sss", $nis, $siswa['no_hp_ortu'], $pesan_notif);
        $stmt_wa->execute();
    }

    // F. Kirim Telegram jika bot token dan chat ID orang tua tersedia
    if (!empty($setting['tg_bot_token']) && !empty($siswa['telegram_chat_id'])) {
        include_once 'fungsi_telegram.php';
        $pesan_telegram = str_replace(['*', '_'], ['<b>', '<i>'], $pesan_notif);
        $pesan_telegram = str_replace(['</b><b>', '</i><i>'], ['', ''], $pesan_telegram);
        sendTelegram($siswa['telegram_chat_id'], $pesan_telegram, $setting['tg_bot_token']);
    }

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'pesan' => 'Top Up saldo sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' untuk ' . $nama . ' berhasil!'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'pesan' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
}
?>
