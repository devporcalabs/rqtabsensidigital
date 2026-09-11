<?php
session_start();
header('Content-Type: application/json');

include 'koneksi.php';
include 'fungsi_wa.php';
include_once 'spp_init_db.php';

// Cek autentikasi admin atau bendahara
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak. Sesi telah berakhir.']);
    exit;
}
$role = strtolower(trim($_SESSION['role'] ?? ''));
if ($role !== 'admin' && $role !== 'bendahara') {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak.']);
    exit;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// 1. Cek jumlah antrean yang pending
if ($action === 'count') {
    $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM wa_queue WHERE status = 'pending' AND tipe = 'spp_tagihan'");
    $row = mysqli_fetch_assoc($q);
    echo json_encode(['success' => true, 'total' => (int)$row['total']]);
    exit;
}

// 2. Ambil 1 antrean berikutnya yang siap dikirim
if ($action === 'get_next') {
    $q = mysqli_query($conn, "SELECT q.*, s.nama as nama_siswa, s.kelas
                              FROM wa_queue q 
                              LEFT JOIN siswa s ON q.nis = s.nis 
                              WHERE q.status = 'pending' AND q.tipe = 'spp_tagihan'
                              ORDER BY q.id ASC LIMIT 1");
    if ($row = mysqli_fetch_assoc($q)) {
        echo json_encode([
            'success' => true,
            'has_item' => true,
            'item' => [
                'id'           => (int)$row['id'],
                'nis'          => $row['nis'],
                'nama_siswa'   => $row['nama_siswa'] ?? 'Siswa',
                'kelas'        => $row['kelas'] ?? '-',
                'target'       => $row['target'],
                'scheduled_at' => $row['scheduled_at']
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'has_item' => false]);
    }
    exit;
}

// 3. Kirim 1 pesan antrean
if ($action === 'send_item') {
    $queue_id = (int)($_POST['queue_id'] ?? 0);
    if ($queue_id <= 0) {
        // Ambil pesan pending pertama
        $q = mysqli_query($conn, "SELECT * FROM wa_queue WHERE status = 'pending' AND tipe = 'spp_tagihan' ORDER BY id ASC LIMIT 1");
    } else {
        $q = mysqli_query($conn, "SELECT * FROM wa_queue WHERE id = $queue_id AND status = 'pending' LIMIT 1");
    }

    if (!$q || mysqli_num_rows($q) == 0) {
        echo json_encode(['success' => false, 'error' => 'Pesan tidak ditemukan atau sudah terkirim.']);
        exit;
    }

    $row = mysqli_fetch_assoc($q);
    $id = (int)$row['id'];
    $target = $row['target'];
    $pesan = $row['message'];
    $tagihan_id = (int)$row['tagihan_id'];

    // Ambil pengaturan API WA
    $q_set = mysqli_query($conn, "SELECT wa_token, wa_api_url, nama_sekolah FROM pengaturan WHERE id = 1");
    $set = mysqli_fetch_assoc($q_set);
    $wa_token = $set['wa_token'] ?? '';
    $wa_api_url = $set['wa_api_url'] ?? '';

    if (empty($wa_token) || empty($wa_api_url)) {
        echo json_encode(['success' => false, 'error' => 'Pengaturan WA Gateway (Secret Key & URL) belum diisi di menu Pengaturan.']);
        exit;
    }

    // Kirim pesan WhatsApp
    $res = sendWa($target, $pesan, $wa_token, $wa_api_url);
    $res_arr = json_decode($res, true);

    $is_sent = false;
    $status_ket = 'Gagal';

    if (is_array($res_arr)) {
        if ((isset($res_arr['status']) && $res_arr['status'] == true) || (isset($res_arr['is_success']) && $res_arr['is_success'] == true)) {
            $is_sent = true;
            $status_ket = 'Terkirim';
        } else {
            $status_ket = 'Gagal: ' . ($res_arr['reason'] ?? ($res_arr['message'] ?? 'Error dari server WA'));
        }
    } else {
        $status_ket = 'Response tidak dikenali';
    }

    // Update status di wa_queue
    $stmt_up = $conn->prepare("UPDATE wa_queue SET status = 'sent', sent_at = NOW() WHERE id = ?");
    $stmt_up->bind_param("i", $id);
    $stmt_up->execute();

    // Catat ke log notifikasi SPP jika ada tagihan_id
    if ($tagihan_id > 0) {
        $stmt_log = $conn->prepare("INSERT INTO spp_log_notifikasi (tagihan_id, no_hp, pesan, status, sent_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt_log->bind_param("isss", $tagihan_id, $target, $pesan, $status_ket);
        $stmt_log->execute();
    }

    // Hitung sisa antrean
    $q_rem = mysqli_query($conn, "SELECT COUNT(*) as remaining FROM wa_queue WHERE status = 'pending' AND tipe = 'spp_tagihan'");
    $rem = mysqli_fetch_assoc($q_rem);

    echo json_encode([
        'success'   => true,
        'id'        => $id,
        'target'    => $target,
        'is_sent'   => $is_sent,
        'status'    => $status_ket,
        'remaining' => (int)$rem['remaining']
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Aksi tidak valid.']);
