<?php
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');
$tgl = date('Y-m-d');

$query = mysqli_query($conn, "SELECT nama, kelas, sesi, foto, waktu_masuk
                              FROM (
                                  SELECT s.nama, s.kelas, s.sesi, CONCAT('siswa/', s.foto) AS foto, a.waktu_masuk 
                                  FROM absensi a 
                                  JOIN siswa s ON a.nis = s.nis 
                                  WHERE DATE(a.waktu_masuk) = '$tgl' 
                                  UNION ALL
                                  SELECT g.nama, 'Guru' AS kelas, '1' AS sesi, CONCAT('guru/', g.foto) AS foto, ag.waktu_masuk 
                                  FROM absensi_guru ag 
                                  JOIN guru g ON ag.nip = g.nip 
                                  WHERE DATE(ag.waktu_masuk) = '$tgl' 
                              ) combined
                              ORDER BY waktu_masuk DESC LIMIT 15");

$html = '';
$count = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM absensi WHERE DATE(waktu_masuk) = '$tgl'
    UNION ALL
    SELECT id FROM absensi_guru WHERE DATE(waktu_masuk) = '$tgl'
"));

while($row = mysqli_fetch_assoc($query)) {
    $jam = date('H:i', strtotime($row['waktu_masuk']));
    
    // --- LOGIKA FOTO VS ICON CSS ---
    $path_foto = 'img/' . $row['foto'];
    if (!empty($row['foto']) && file_exists($path_foto)) {
        // Jika ada foto, tampilkan gambar
        $display_foto = '<img src="'.$path_foto.'" class="log-img">';
    } else {
        // Jika tidak ada, tampilkan Icon CSS (Gunakan inisial atau icon orang)
        $display_foto = '<div class="log-img-icon"><i class="bi bi-person-fill"></i></div>';
    }
    
    $html .= '
    <div class="log-item shadow-sm">
        ' . $display_foto . '
        <div style="flex: 1;">
            <div class="log-name">'.$row['nama'].'</div>
            <div class="log-info">'.$row['kelas'].' • <span class="text-primary">'.$jam.'</span></div>
        </div>
        <div class="badge bg-light text-dark small border">S'.$row['sesi'].'</div>
    </div>';
}

if($html == '') $html = '<div class="text-center p-5 text-muted small">Belum ada absen.</div>';

echo json_encode(['html' => $html, 'count' => $count]);