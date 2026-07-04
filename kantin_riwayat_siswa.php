<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    echo "<tr><td colspan='3' class='text-center text-danger'>Unauthorized</td></tr>";
    exit;
}

$nis = trim($_GET['nis'] ?? '');
if (empty($nis)) {
    echo "<tr><td colspan='3' class='text-center text-muted'>Parameter NIS kosong.</td></tr>";
    exit;
}

// Ambil 10 transaksi terakhir
$stmt = $conn->prepare("SELECT * FROM kantin_transaksi WHERE nis = ? ORDER BY waktu DESC LIMIT 10");
$stmt->bind_param("s", $nis);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $tipe = $row['tipe']; // 'kredit' (Top Up) atau 'debet' (Belanja)
        $waktu = date('d/m H:i', strtotime($row['waktu']));
        $nominal = number_format($row['nominal'], 0, ',', '.');
        $saldo_akhir = number_format($row['saldo_akhir'], 0, ',', '.');
        $ket = htmlspecialchars($row['keterangan'] ?? '');
        
        if ($tipe === 'kredit') {
            $badge = "<span class='badge bg-success bg-opacity-10 text-success rounded-pill px-2'>+Rp $nominal</span>";
            $icon = "<i class='bi bi-arrow-down-left-circle-fill text-success me-1'></i>";
        } else {
            $badge = "<span class='badge bg-danger bg-opacity-10 text-danger rounded-pill px-2'>-Rp $nominal</span>";
            $icon = "<i class='bi bi-arrow-up-right-circle-fill text-danger me-1'></i>";
        }

        echo "
        <tr class='border-bottom border-light'>
            <td><small class='text-muted'>$waktu</small></td>
            <td>
                <div class='fw-bold text-dark' style='font-size:0.75rem;'>$icon $ket</div>
                <small class='text-muted' style='font-size:0.65rem;'>Saldo: Rp $saldo_akhir</small>
            </td>
            <td class='text-end fw-bold'>$badge</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center py-4 text-muted small'><i class='bi bi-journal-x fs-4 d-block mb-1 opacity-50'></i> Belum ada riwayat transaksi.</td></tr>";
}
?>
