<?php
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$tgl = date('Y-m-d');

$query = mysqli_query($conn, "SELECT nama, kelas, foto, waktu_masuk, waktu_pulang
                              FROM (
                                  SELECT s.nama, s.kelas, CONCAT('siswa/', s.foto) AS foto, a.waktu_masuk, a.waktu_pulang 
                                  FROM absensi a 
                                  JOIN siswa s ON a.nis = s.nis 
                                  WHERE DATE(a.waktu_masuk) = '$tgl' OR DATE(a.waktu_pulang) = '$tgl'
                                  UNION ALL
                                  SELECT g.nama, 'Guru' AS kelas, CONCAT('guru/', g.foto) AS foto, ag.waktu_masuk, ag.waktu_pulang 
                                  FROM absensi_guru ag 
                                  JOIN guru g ON ag.nip = g.nip 
                                  WHERE DATE(ag.waktu_masuk) = '$tgl' OR DATE(ag.waktu_pulang) = '$tgl'
                              ) combined
                              ORDER BY GREATEST(IFNULL(waktu_masuk, 0), IFNULL(waktu_pulang, 0)) DESC LIMIT 15");

$count = mysqli_num_rows(mysqli_query($conn, "
    SELECT id FROM absensi WHERE DATE(waktu_masuk) = '$tgl' OR DATE(waktu_pulang) = '$tgl'
    UNION ALL
    SELECT id FROM absensi_guru WHERE DATE(waktu_masuk) = '$tgl' OR DATE(waktu_pulang) = '$tgl'
"));

$html = '';

if ($query && mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_assoc($query)) {
        $is_pulang = (!empty($row['waktu_pulang']) && $row['waktu_pulang'] != '0000-00-00 00:00:00');
        $waktu = $is_pulang ? $row['waktu_pulang'] : $row['waktu_masuk'];
        $jam = date('H:i', strtotime($waktu));
        $label_waktu = ($is_pulang ? 'Jam Pulang: ' : 'Jam Masuk: ') . $jam;
        $color_class = $is_pulang ? 'text-danger' : 'text-success';

        $path_foto = 'img/' . $row['foto'];
        if (!empty($row['foto']) && file_exists($path_foto)) {
            $img_html = '<img src="' . $path_foto . '" class="log-img shadow-sm" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid #3b82f6;" alt="foto">';
        } else {
            $img_html = '<div class="log-icon-css shadow-sm" style="width: 50px; height: 50px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; border: 2px solid #3b82f6;"><i class="bi bi-person-fill"></i></div>';
        }

        $html .= '
        <div class="log-item shadow-sm p-3 mb-2 bg-white rounded-4 border d-flex align-items-center gap-3">
            ' . $img_html . '
            <div style="flex: 1; min-width: 0;">
                <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">' . htmlspecialchars($row['nama']) . '</div>
                <div style="font-size: 0.8rem;" class="mt-1">
                    <span class="fw-bold ' . $color_class . '">' . $label_waktu . '</span>
                    <span class="text-muted ms-2">• ' . htmlspecialchars($row['kelas']) . '</span>
                </div>
            </div>
        </div>';
    }
}

if ($html == '') $html = '<div class="text-center py-5 text-muted small"><i class="bi bi-info-circle me-1"></i>Belum ada aktivitas hari ini.</div>';

echo json_encode(['html' => $html, 'count' => $count]);