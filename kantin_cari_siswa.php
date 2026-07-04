<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Unauthorized']);
    exit;
}

$q = trim($_GET['q'] ?? '');

if (empty($q)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Pencarian kosong!']);
    exit;
}

// Cari berdasarkan rfid_uid atau nis (toleran terhadap perbedaan leading zero)
$q_clean = ltrim($q, '0');
$stmt = $conn->prepare("SELECT * FROM siswa WHERE rfid_uid = ? OR nis = ? OR TRIM(LEADING '0' FROM rfid_uid) = ? OR TRIM(LEADING '0' FROM nis) = ? LIMIT 1");
$stmt->bind_param("ssss", $q, $q, $q_clean, $q_clean);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'nis' => $row['nis'],
        'nama' => $row['nama'],
        'kelas' => $row['kelas'],
        'foto' => $row['foto'],
        'saldo' => (int)($row['saldo'] ?? 0)
    ]);
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Siswa tidak ditemukan']);
}
?>
