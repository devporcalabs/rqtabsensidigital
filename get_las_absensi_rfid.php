<?php
include 'koneksi.php';

// Ambil 10 absensi terbaru hari ini
$tgl = date('Y-m-d');
$sql = "SELECT a.waktu_masuk, a.waktu_pulang, s.nama, s.kelas, s.foto 
        FROM absensi a 
        JOIN siswa s ON a.nis = s.nis 
        WHERE DATE(a.waktu_masuk) = '$tgl' 
        ORDER BY GREATEST(IFNULL(a.waktu_masuk, 0), IFNULL(a.waktu_pulang, 0)) DESC LIMIT 5";

$query = mysqli_query($conn, $sql);

if(mysqli_num_rows($query) > 0){
    while($row = mysqli_fetch_assoc($query)){
        // Logika Jam & Label
        if(!empty($row['waktu_pulang']) && $row['waktu_pulang'] != '0000-00-00 00:00:00'){
            $jam_tampil = date('H:i', strtotime($row['waktu_pulang']));
            $label = "PULANG";
            $color = "#ef4444"; // Merah
        } else {
            $jam_tampil = date('H:i', strtotime($row['waktu_masuk']));
            $label = "MASUK";
            $color = "#10b981"; // Hijau
        }

        // --- LOGIKA FOTO / ICON CSS ---
        $foto_path = "img/siswa/" . $row['foto'];
        if(!empty($row['foto']) && file_exists($foto_path)){
            $img_html = '<img src="'.$foto_path.'" class="log-img shadow-sm" alt="foto">';
        } else {
            // Jika foto tidak ada, gunakan CSS Icon
            $img_html = '<div class="log-icon-css shadow-sm"><i class="bi bi-person-fill"></i></div>';
        }
        
        echo '
        <div class="log-item shadow-sm">
            '.$img_html.'
            <div style="flex:1">
                <div class="log-name">'.htmlspecialchars($row['nama']).'</div>
                <div class="log-info">'.htmlspecialchars($row['kelas']).'</div>
            </div>
            <div class="text-end">
                <div class="log-time" style="background: '.$color.'15; color: '.$color.';">'.$jam_tampil.'</div>
                <div style="font-size: 0.55rem; font-weight: 800; color: #94a3b8; margin-top:3px;">'.$label.'</div>
            </div>
        </div>';
    }
} else {
    echo '<div class="text-center py-5 text-muted small">Belum ada aktivitas hari ini.</div>';
}
?>