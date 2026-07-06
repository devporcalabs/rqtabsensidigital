<?php
session_start();
include 'koneksi.php';

// --- SECURITY: INISIALISASI CSRF TOKEN ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fungsi Keamanan XSS
function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// 1. Cek Login
if(!isset($_SESSION['login'])){ 
    header("location: login.php"); 
    exit; 
}

$role = $_SESSION['role'];
if($role !== 'admin') {
    header("location: dashboard.php");
    exit;
}

// --- SECURITY: LOGIKA HAPUS DATA ---
if(isset($_GET['hapus_id']) && isset($_GET['token'])){
    if($_GET['token'] !== $_SESSION['csrf_token']){
        die("Terdeteksi upaya ilegal (CSRF)!");
    }

    $id_hapus = (int)$_GET['hapus_id'];
    
    $stmt_foto = $conn->prepare("SELECT foto FROM guru WHERE id = ?");
    $stmt_foto->bind_param("i", $id_hapus);
    $stmt_foto->execute();
    $data_lama = $stmt_foto->get_result()->fetch_assoc();

    if($data_lama && !empty($data_lama['foto']) && $data_lama['foto'] !== 'default.jpg'){
        $path_foto = "img/guru/" . $data_lama['foto'];
        if(file_exists($path_foto)) unlink($path_foto);
    }

    $stmt_del = $conn->prepare("DELETE FROM guru WHERE id = ?");
    $stmt_del->bind_param("i", $id_hapus);
    
    if($stmt_del->execute()){
        echo "<script>alert('Data guru berhasil dihapus!'); window.location='data_guru.php';</script>";
    }
    exit;
}

// Ambil Nama Sekolah
$query_set = mysqli_query($conn, "SELECT nama_sekolah FROM pengaturan WHERE id=1");
$set_sch = mysqli_fetch_assoc($query_set);
$nama_sekolah = $set_sch['nama_sekolah'] ?? 'Sistem Absensi';

// --- LOGIKA TANGGAL INDONESIA ---
$daftar_hari = array('Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu');
$daftar_bulan = array('January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember');
$tgl_indo = $daftar_hari[date('l')] . ', ' . date('d ') . $daftar_bulan[date('F')] . date(' Y');

// --- LOGIKA PAGINATION ---
$limit = 40; 
$halaman = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($halaman - 1) * $limit;

// --- PENCARIAN & FILTER ---
$keyword = $_GET['q'] ?? '';
$where = "WHERE 1=1";
$params = [];
$types = "";

if(!empty($keyword)) {
    $where .= " AND (nama LIKE ? OR nip LIKE ? OR jabatan LIKE ?)";
    $search_key = "%$keyword%"; 
    $params[] = $search_key; 
    $params[] = $search_key; 
    $params[] = $search_key; 
    $types .= "sss";
}

$stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM guru $where");
if($types) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_data = $stmt_count->get_result()->fetch_assoc()['total'];
$total_halaman = ceil($total_data / $limit);

$final_query = "SELECT * FROM guru $where ORDER BY nama ASC LIMIT ?, ?";
$params[] = $offset; $params[] = $limit; $types .= "ii";
$stmt_main = $conn->prepare($final_query);
$stmt_main->bind_param($types, ...$params);
$stmt_main->execute();
$data_guru = $stmt_main->get_result();

include 'header.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Guru - <?= xss($nama_sekolah) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f0f3f9;
            background-image: radial-gradient(at 0% 0%, rgba(13, 110, 253, 0.05) 0px, transparent 50%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
        }

        .btn-action {
            border-radius: 18px;
            font-weight: 700;
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.4);
            backdrop-filter: blur(5px);
        }

        .btn-action:hover {
            transform: translateY(-3px);
            background-color: rgba(255,255,255,0.9);
        }

        #formTambah { display: none; }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 12px;
        }

        .photo-circle { 
            width: 48px; height: 48px; 
            border-radius: 12px; 
            background: #fff; 
            overflow: hidden; 
            border: 1px solid #e2e8f0;
        }
        .photo-circle img { width: 100%; height: 100%; object-fit: cover; }

        .table thead th { 
            background: rgba(13, 110, 253, 0.05); 
            color: #0d6efd;
            font-size: 0.75rem;
            text-transform: uppercase;
            border: none;
            padding: 15px;
        }

        #live-clock {
            background: rgba(255, 255, 255, 0.5);
            padding: 5px 15px;
            border-radius: 10px;
            font-weight: 800;
        }

        /* Dropdown Styling */
        .dropdown-menu { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .dropdown-item { font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-5" style="margin-top: 50px;">
    <div class="glass-card p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h3 class="fw-bold mb-1 text-primary">Manajemen Data Guru</h3>
                <p class="text-muted mb-0 small"><?= $tgl_indo ?> | <span id="live-clock">--:--:--</span></p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <button onclick="toggleForm()" class="btn btn-primary btn-action px-4 py-2">
                    <i class="bi bi-person-plus-fill me-2"></i> Tambah Guru
                </button>
            </div>
        </div>
    </div>

    <!-- Form Tambah Guru -->
    <div id="formTambah" class="glass-card p-4 mb-4 border-primary border-top border-4 border-opacity-25 animate__animated animate__fadeIn">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Input Guru Baru</h5>
            <button onclick="toggleForm()" class="btn-close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data" action="proses_tambah_guru.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="row g-3">
                <div class="col-md-2 text-center">
                    <div class="mx-auto rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 110px; height: 110px; overflow: hidden;">
                        <img id="img-preview" src="" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                        <i id="icon-placeholder" class="bi bi-person-bounding-box fs-1 text-muted"></i>
                    </div>
                    <label for="foto-input" class="btn btn-sm btn-outline-primary mt-3 rounded-pill">Upload Foto</label>
                    <input type="file" name="foto" id="foto-input" class="d-none" accept="image/*" onchange="previewImage(event)">
                </div>
                
                <div class="col-md-10">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="small fw-bold">NIP / ID Guru</label>
                            <input type="text" name="nip" class="form-control" placeholder="19XXXXXXXXXXXXX" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">RFID UID</label>
                            <input type="text" name="rfid_uid" class="form-control" placeholder="Tempel kartu RFID..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap dengan Gelar..." required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="small fw-bold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" placeholder="cth: Guru Mapel, Wali Kelas, Kepala Sekolah" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">No. WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="628..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="guru@email.com">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-10"></div>
                        <div class="col-md-2 d-flex align-items-end justify-content-end">
                            <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold py-2 rounded-3">SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Pencarian & Tabel -->
    <div class="glass-card p-4">
        <form method="GET" class="row g-3 mb-4 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="<?= xss($keyword) ?>" class="form-control border-start-0 ps-0" placeholder="Cari Guru berdasarkan Nama, NIP atau Jabatan...">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 btn-action py-2">Terapkan Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th width="80">Foto</th>
                        <th>NIP</th>
                        <th>RFID UID</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>No. WhatsApp</th>
                        <th>Biometrik</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data_guru->num_rows > 0): ?>
                        <?php while($row = $data_guru->fetch_assoc()): ?>
                            <?php 
                            $foto_path = "img/guru/" . $row['foto'];
                            $foto_tampil = (file_exists($foto_path) && !empty($row['foto'])) ? $foto_path : 'img/siswa/default.jpg';
                            $sudah_ada_wajah = !empty($row['face_embedding']);
                            ?>
                            <tr>
                                <td>
                                    <div class="photo-circle shadow-sm">
                                        <img src="<?= $foto_tampil ?>" alt="foto">
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border p-2 font-monospace"><?= xss($row['nip']) ?></span></td>
                                <td><span class="badge bg-light text-dark border p-2 font-monospace"><?= xss($row['rfid_uid'] ?? '-') ?></span></td>
                                <td><strong class="text-dark"><?= xss($row['nama']) ?></strong></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary p-2 px-3"><?= xss($row['jabatan'] ?? '-') ?></span></td>
                                <td><?= xss($row['no_hp'] ?? '-') ?></td>
                                <td>
                                    <?php if($sudah_ada_wajah): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-shield-fill-check me-1"></i>Terdaftar</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-shield-fill-x me-1"></i>Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="daftar_wajah_guru.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-light border text-info rounded-3" title="Rekam Wajah">
                                            <i class="bi bi-person-bounding-box"></i>
                                        </a>

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light border text-dark rounded-3" data-bs-toggle="dropdown" title="Cetak Kartu">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li><a class="dropdown-item" href="cetak_kartu.php?id=<?= $row['id'] ?>&tipe=guru" target="_blank"><i class="bi bi-person-badge text-danger me-2"></i>Kartu Guru</a></li>
                                            </ul>
                                        </div>

                                        <a href="edit_guru.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-light border text-primary rounded-3" title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <a href="data_guru.php?hapus_id=<?= $row['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn btn-sm btn-light border text-danger rounded-3" onclick="return confirm('Hapus permanen data guru ini?')" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1"></i>
                                <p class="mt-2 small fw-bold">Tidak ada data guru yang ditemukan.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($total_halaman > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?p=<?= $halaman - 1 ?>&q=<?= urlencode($keyword) ?>">Sebelumnya</a>
                    </li>
                    <?php for($i=1; $i<=$total_halaman; $i++): ?>
                        <li class="page-item <?= ($halaman == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?p=<?= $i ?>&q=<?= urlencode($keyword) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?p=<?= $halaman + 1 ?>&q=<?= urlencode($keyword) ?>">Berikutnya</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleForm() {
        const form = document.getElementById('formTambah');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('img-preview');
            const placeholder = document.getElementById('icon-placeholder');
            preview.src = reader.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    function updateClock() {
        const tz = '<?= $timezone_aktif ?? "Asia/Jakarta" ?>';
        const opt = { timeZone: tz, hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        try {
            document.getElementById('live-clock').textContent = new Intl.DateTimeFormat('id-ID', opt).format(new Date());
        } catch (e) {
            document.getElementById('live-clock').textContent = new Date().toLocaleTimeString();
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
</body>
</html>
