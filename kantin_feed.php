<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    exit;
}

$query = mysqli_query($conn, "
    SELECT t.*, s.nama, s.kelas, s.foto 
    FROM kantin_transaksi t 
    LEFT JOIN siswa s ON t.nis = s.nis 
    ORDER BY t.waktu DESC 
    LIMIT 5
");

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $foto = "img/siswa/" . $row['foto'];
        $has_foto = !empty($row['foto']) && file_exists($foto);
        $waktu = date('H:i', strtotime($row['waktu']));
        $tipe_label = $row['tipe'] == 'debet' ? '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2" style="font-size:0.6rem;">BELANJA</span>' : '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2" style="font-size:0.6rem;">TOP UP</span>';
        $nominal_formatted = "Rp " . number_format($row['nominal'], 0, ',', '.');
        
        echo '
        <div class="feed-item">
            ' . ($has_foto ? '<img src="' . $foto . '" class="feed-avatar">' : '<div class="feed-avatar-placeholder"><i class="bi bi-person text-muted"></i></div>') . '
            <div style="flex: 1; min-width: 0;">
                <div class="fw-bold text-dark text-truncate small" style="max-width: 170px;">' . htmlspecialchars($row['nama'] ?? 'N/A') . '</div>
                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                    <span>' . htmlspecialchars($row['kelas'] ?? '-') . '</span>
                    <span>•</span>
                    ' . $tipe_label . '
                </div>
            </div>
            <div class="text-end">
                <div class="fw-bold ' . ($row['tipe'] == 'debet' ? 'text-danger' : 'text-success') . '" style="font-size: 0.85rem;">
                    ' . ($row['tipe'] == 'debet' ? '-' : '+') . $nominal_formatted . '
                </div>
                <span class="text-muted small" style="font-size: 0.65rem;">' . $waktu . '</span>
            </div>
        </div>';
    }
} else {
    echo '<div class="text-center py-5 text-muted small"><i class="bi bi-info-circle me-1"></i> Belum ada transaksi hari ini.</div>';
}
?>
