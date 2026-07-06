<?php
include 'koneksi.php';

// Ambil 5 absensi terbaru hari ini (siswa & guru)
$tgl = date('Y-m-d');
$sql = "SELECT waktu_masuk, waktu_pulang, status_kehadiran, keterangan, nama, kelas, foto
        FROM (
            SELECT a.waktu_masuk, a.waktu_pulang, a.status_kehadiran, a.keterangan, s.nama, s.kelas, CONCAT('siswa/', s.foto) AS foto
            FROM absensi a 
            JOIN siswa s ON a.nis = s.nis 
            WHERE DATE(a.waktu_masuk) = '$tgl' 
            UNION ALL
            SELECT ag.waktu_masuk, ag.waktu_pulang, ag.status_kehadiran, ag.keterangan, g.nama, 'Guru' AS kelas, CONCAT('guru/', g.foto) AS foto
            FROM absensi_guru ag 
            JOIN guru g ON ag.nip = g.nip 
            WHERE DATE(ag.waktu_masuk) = '$tgl' 
        ) combined
        ORDER BY GREATEST(IFNULL(waktu_masuk, 0), IFNULL(waktu_pulang, 0)) DESC LIMIT 5";

$query = mysqli_query($conn, $sql);

if(mysqli_num_rows($query) > 0){
    while($row = mysqli_fetch_assoc($query)){
        // Logika Status & Waktu
        if(!empty($row['waktu_pulang']) && $row['waktu_pulang'] != '0000-00-00 00:00:00'){
            $jam_tampil = date('H:i', strtotime($row['waktu_pulang']));
            $status_label = "PULANG";
            $badge_class = "bg-danger-subtle text-danger border-danger-subtle";
        } else {
            $jam_tampil = date('H:i', strtotime($row['waktu_masuk']));
            $status_label = ($row['status_kehadiran'] == 'Terlambat') ? "TERLAMBAT" : "MASUK";
            $badge_class = ($row['status_kehadiran'] == 'Terlambat') ? "bg-warning-subtle text-warning border-warning-subtle" : "bg-success-subtle text-success border-success-subtle";
        }

        // Logika Foto atau Icon CSS
        $foto_path = "img/" . $row['foto'];
        if(!empty($row['foto']) && file_exists($foto_path)){
            $avatar = '<img src="'.$foto_path.'" class="avatar-img shadow-sm" alt="foto">';
        } else {
            $avatar = '<div class="avatar-icon-css shadow-sm"><i class="bi bi-person-fill"></i></div>';
        }
        
        echo '
        <div class="attendance-item">
            <div class="d-flex align-items-center w-100">
                <div class="flex-shrink-0">
                    '.$avatar.'
                </div>
                
                <div class="flex-grow-1 ms-3">
                    <div class="student-name">'.htmlspecialchars($row['nama']).'</div>
                    <div class="student-class">'.htmlspecialchars($row['kelas']).'</div>
                </div>
                
                <div class="text-end">
                    <span class="badge-status border '.$badge_class.'">'.$status_label.'</span>
                    <div class="attendance-time">'.$jam_tampil.'</div>
                </div>
            </div>
        </div>';
    }
} else {
    echo '
    <div class="text-center py-5">
        <i class="bi bi-inbox text-muted fs-1"></i>
        <p class="text-muted small fw-bold mt-2">Belum ada aktivitas</p>
    </div>';
}
?>