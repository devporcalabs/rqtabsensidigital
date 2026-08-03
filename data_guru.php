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

// --- STATISTIK GURU ---
$q_tot_guru = mysqli_query($conn, "SELECT COUNT(*) as total FROM guru");
$total_guru = (int)(mysqli_fetch_assoc($q_tot_guru)['total'] ?? 0);

$q_wali = mysqli_query($conn, "SELECT COUNT(*) as total FROM guru WHERE jabatan = 'Wali Kelas' OR jabatan LIKE '%Wali%'");
$total_wali = (int)(mysqli_fetch_assoc($q_wali)['total'] ?? 0);

$q_bio = mysqli_query($conn, "SELECT COUNT(*) as total FROM guru WHERE face_embedding IS NOT NULL AND face_embedding != ''");
$total_bio = (int)(mysqli_fetch_assoc($q_bio)['total'] ?? 0);

$q_rfid = mysqli_query($conn, "SELECT COUNT(*) as total FROM guru WHERE rfid_uid IS NOT NULL AND rfid_uid != ''");
$total_rfid = (int)(mysqli_fetch_assoc($q_rfid)['total'] ?? 0);

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
        #formTambah { display: none; }

        /* Action Buttons Circle */
        .btn-action-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #64748b;
            font-size: 0.85rem;
            transition: all 0.2s;
            padding: 0;
            text-decoration: none;
        }
        .btn-action-circle:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .btn-action-circle.blue { color: #3b82f6; border-color: #dbeafe; background: #eff6ff; }
        .btn-action-circle.green { color: #10b981; border-color: #d1fae5; background: #ecfdf5; }
        .btn-action-circle.cyan { color: #06b6d4; border-color: #cffafe; background: #ecfeff; }
        .btn-action-circle.gray { color: #64748b; border-color: #e2e8f0; background: #f8fafc; }
        .btn-action-circle.red { color: #ef4444; border-color: #fee2e2; background: #fee2e2; }

        /* Badge custom classes */
        .badge-kelas {
            background-color: #eff6ff !important;
            color: #3b82f6 !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 8px !important;
            border: none !important;
        }

        .photo-circle { 
            width: 40px; height: 40px; 
            border-radius: 50%; 
            background: #f1f5f9; 
            overflow: hidden; 
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-circle img { width: 100%; height: 100%; object-fit: cover; }

        #live-clock {
            background: rgba(0, 0, 0, 0.03);
            padding: 3px 10px;
            border-radius: 8px;
            font-weight: 700;
        }

        /* Dropdown Styling */
        .dropdown-menu { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .dropdown-item { font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-4">
    <!-- Header Row -->
    <div class="glass-card p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h3 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Manajemen Data Guru</h3>
                <p class="text-muted mb-0 small"><?= $tgl_indo ?> | <span id="live-clock">--:--:--</span></p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <button onclick="toggleForm()" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold">
                    <i class="bi bi-plus me-1"></i> + Tambah Guru
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid Row -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Guru -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="glass-card p-4 mb-0" style="min-height: 140px; display: flex; flex-direction: column; justify-content: space-between;">
                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Guru</div>
                <div class="h2 fw-extrabold text-dark my-1" style="font-weight: 800; font-size: 1.8rem;"><?= $total_guru ?></div>
                <div class="text-muted small" style="font-size: 0.75rem;">Guru aktif terdaftar</div>
            </div>
        </div>
        <!-- 2. Wali Lembaga -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="glass-card p-4 mb-0" style="min-height: 140px; display: flex; flex-direction: column; justify-content: space-between;">
                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Wali Lembaga</div>
                <div class="h2 fw-extrabold text-dark my-1" style="font-weight: 800; font-size: 1.8rem;"><?= $total_wali ?></div>
                <div class="text-muted small" style="font-size: 0.75rem;">Memiliki tanggung jawab lembaga</div>
            </div>
        </div>
        <!-- 3. Biometrik Aktif -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="glass-card p-4 mb-0" style="min-height: 140px; display: flex; flex-direction: column; justify-content: space-between;">
                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Biometrik Aktif</div>
                <div class="h2 fw-extrabold text-dark my-1" style="font-weight: 800; font-size: 1.8rem;"><?= $total_bio ?></div>
                <div class="text-muted small" style="font-size: 0.75rem;"><?= $total_bio > 0 ? "$total_bio data biometrik aktif" : "Belum ada data biometrik" ?></div>
            </div>
        </div>
        <!-- 4. RFID Terhubung -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="glass-card p-4 mb-0" style="min-height: 140px; display: flex; flex-direction: column; justify-content: space-between;">
                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">RFID Terhubung</div>
                <div class="h2 fw-extrabold text-dark my-1" style="font-weight: 800; font-size: 1.8rem;"><?= $total_rfid ?></div>
                <div class="text-muted small" style="font-size: 0.75rem;">Kartu guru telah terdaftar</div>
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
                            <input type="text" name="jabatan" class="form-control" placeholder="cth: Ustadz/Ustadzah, Wali Lembaga, Pimpinan" required>
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
        <!-- Header Row -->
        <div class="d-flex align-items-center gap-2 mb-4">
            <h5 class="fw-bold m-0 text-dark" style="font-size: 1.1rem;">Daftar Guru</h5>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 text-xs fw-bold" style="font-size: 0.75rem;"><?= $total_data ?></span>
        </div>

        <!-- Filter form -->
        <form method="GET" class="row g-2 mb-4 align-items-center">
            <div class="col flex-grow-1 position-relative">
                <i class="bi bi-search position-absolute start-0 top-50 translate-middle-y text-muted ms-3" style="font-size: 0.9rem;"></i>
                <input type="text" name="q" class="form-control border-0 bg-light rounded-pill ps-5 pe-3" style="font-size: 0.85rem; height: 46px;" placeholder="Cari guru berdasarkan nama, NIP atau jabatan..." value="<?= xss($keyword) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold" style="height: 46px; font-size: 0.875rem;">Terapkan Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="ps-3" width="8%">FOTO</th>
                        <th>NIP</th>
                        <th>RFID UID</th>
                        <th>NAMA LENGKAP</th>
                        <th>JABATAN</th>
                        <th>NO. WHATSAPP</th>
                        <th>BIOMETRIK</th>
                        <th class="text-center" width="18%">AKSI</th>
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
                                <td class="ps-3">
                                    <div class="photo-circle">
                                        <img src="<?= $foto_tampil ?>" alt="foto">
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-1.5 font-monospace" style="font-size: 0.8rem;"><?= xss($row['nip']) ?></span></td>
                                <td><span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-1.5 font-monospace" style="font-size: 0.8rem;"><?= xss($row['rfid_uid'] ?? '-') ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= xss($row['nama']) ?></div>
                                    <div class="small text-muted" style="font-size: 0.75rem; font-weight: 500;">Guru aktif</div>
                                </td>
                                <td><span class="badge badge-kelas"><?= xss($row['jabatan'] ?? '-') ?></span></td>
                                <td><span class="text-secondary fw-semibold" style="font-size: 0.85rem;"><?= xss($row['no_hp'] ?? '-') ?></span></td>
                                <td>
                                    <?php if($sudah_ada_wajah): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">● Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.75rem;">● Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                                        <!-- 1. Rekam Wajah (Camera) -->
                                        <a href="daftar_wajah_guru.php?id=<?= $row['id'] ?>" class="btn-action-circle blue" title="Rekam Wajah">
                                            <i class="bi bi-camera"></i>
                                        </a>

                                        <!-- 2. Cetak Kartu -->
                                        <div class="dropdown d-inline-block">
                                            <button type="button" class="btn-action-circle gray" data-bs-toggle="dropdown" title="Cetak Kartu">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                <li><a class="dropdown-item py-2 px-3 rounded" href="cetak_kartu.php?id=<?= $row['id'] ?>&tipe=guru" target="_blank"><i class="bi bi-person-badge text-danger me-2"></i>Kartu Guru</a></li>
                                            </ul>
                                        </div>

                                        <!-- 3. Edit -->
                                        <a href="edit_guru.php?id=<?= $row['id'] ?>" class="btn-action-circle blue" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- 4. Hapus -->
                                        <a href="data_guru.php?hapus_id=<?= $row['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn-action-circle red" onclick="return confirm('Hapus permanen data guru ini?')" title="Hapus Data">
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
