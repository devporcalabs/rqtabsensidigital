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
$kelas_diampu = $_SESSION['kelas_diampu'] ?? '';

// --- SECURITY: LOGIKA HAPUS DATA ---
if(isset($_GET['hapus_id']) && isset($_GET['token'])){
    if($_GET['token'] !== $_SESSION['csrf_token']){
        die("Terdeteksi upaya ilegal (CSRF)!");
    }

    $id_hapus = (int)$_GET['hapus_id'];
    
    $stmt_foto = $conn->prepare("SELECT foto FROM siswa WHERE id = ?");
    $stmt_foto->bind_param("i", $id_hapus);
    $stmt_foto->execute();
    $data_lama = $stmt_foto->get_result()->fetch_assoc();

    if($data_lama && !empty($data_lama['foto'])){
        $path_foto = "img/siswa/" . $data_lama['foto'];
        if(file_exists($path_foto)) unlink($path_foto);
    }

    $stmt_del = $conn->prepare("DELETE FROM siswa WHERE id = ?");
    $stmt_del->bind_param("i", $id_hapus);
    
    if($stmt_del->execute()){
        echo "<script>alert('Data berhasil dihapus!'); window.location='data_siswa.php';</script>";
    }
    exit;
}

// Ambil Nama Sekolah & Timezone
$query_set = mysqli_query($conn, "SELECT nama_sekolah FROM pengaturan WHERE id=1");
$set_sch = mysqli_fetch_assoc($query_set);
$nama_sekolah = $set_sch['nama_sekolah'] ?? 'Sistem Absensi';

$querySetting = mysqli_query($conn, "SELECT timezone FROM pengaturan WHERE id=1");
$sett = mysqli_fetch_assoc($querySetting);
$timezone_aktif = $sett['timezone'] ?? 'Asia/Jakarta';

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
$kelas_filter = $_GET['kelas'] ?? '';
$where = ($role == 'walikelas') ? "WHERE kelas = ?" : "WHERE 1=1";
$params = [];
$types = "";

if($role == 'walikelas') { $params[] = $kelas_diampu; $types .= "s"; }
if(!empty($keyword)) {
    $where .= " AND (nama LIKE ? OR nis LIKE ?)";
    $search_key = "%$keyword%"; $params[] = $search_key; $params[] = $search_key; $types .= "ss";
}
if(!empty($kelas_filter)) { $where .= " AND kelas = ?"; $params[] = $kelas_filter; $types .= "s"; }

$stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM siswa $where");
if($types) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_data = $stmt_count->get_result()->fetch_assoc()['total'];
$total_halaman = ceil($total_data / $limit);

$final_query = "SELECT * FROM siswa $where ORDER BY nama ASC LIMIT ?, ?";
$params[] = $offset; $params[] = $limit; $types .= "ii";
$stmt_main = $conn->prepare($final_query);
$stmt_main->bind_param($types, ...$params);
$stmt_main->execute();
$data_siswa = $stmt_main->get_result();

// --- PENCEGAT & INFORMASI BATAS SISWA (MAX 150 SISWA) ---
$q_tot_all = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$total_siswa_db = (int)(mysqli_fetch_assoc($q_tot_all)['total'] ?? 0);
$is_kuota_penuh = ($total_siswa_db >= 150);

include 'header.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Siswa - <?= xss($nama_sekolah) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        #formTambah { display: none; }

        /* Quick Actions Mockup Look */
        .quick-action-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.25rem;
            transition: all 0.2s;
        }
        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            border-color: #cbd5e1;
        }
        .quick-action-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .quick-action-icon.green { background-color: #ecfdf5; color: #10b981; }
        .quick-action-icon.orange { background-color: #fffbeb; color: #f59e0b; }
        .quick-action-icon.cyan { background-color: #ecfeff; color: #06b6d4; }
        .quick-action-icon.blue { background-color: #eff6ff; color: #3b82f6; }

        .quick-action-label {
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 0.1rem;
        }
        .quick-action-title {
            font-size: 0.85rem;
            color: #1e293b;
            font-weight: 700;
        }

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
                <h3 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Manajemen Data Siswa</h3>
                <p class="text-muted mb-0 small"><?= $tgl_indo ?> | <span id="live-clock">--:--:--</span></p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <button onclick="toggleForm()" class="btn <?= $is_kuota_penuh ? 'btn-secondary' : 'btn-primary' ?> px-4 py-2.5 rounded-pill fw-bold">
                    <i class="bi <?= $is_kuota_penuh ? 'bi-lock-fill' : 'bi-plus' ?> me-1"></i> <?= $is_kuota_penuh ? 'Kuota Penuh ('.$total_siswa_db.'/150)' : '+ Tambah Siswa' ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row g-3 mb-4">
        <!-- 1. Import Siswa -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="import_siswa.php" class="quick-action-card text-decoration-none" <?= $is_kuota_penuh ? 'onclick="alert(\'Gagal! Kuota maksimal 150 siswa telah penuh ('.$total_siswa_db.'/150). Silakan hapus data siswa terlebih dahulu.\'); return false;"' : '' ?>>
                <div class="quick-action-icon green"><i class="bi bi-arrow-down-short"></i></div>
                <div>
                    <div class="quick-action-label">Data Masal</div>
                    <div class="quick-action-title">Import Siswa</div>
                </div>
            </a>
        </div>
        <!-- 2. Atur Sesi -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="bulk_update_sesi.php" class="quick-action-card text-decoration-none">
                <div class="quick-action-icon orange"><i class="bi bi-clock"></i></div>
                <div>
                    <div class="quick-action-label">Pengaturan</div>
                    <div class="quick-action-title">Atur Sesi</div>
                </div>
            </a>
        </div>
        <!-- 3. Cetak Kartu -->
        <div class="col-12 col-sm-6 col-md-3" style="cursor: pointer;">
            <div data-bs-toggle="modal" data-bs-target="#modalCetak" class="quick-action-card text-decoration-none">
                <div class="quick-action-icon cyan"><i class="bi bi-credit-card-2-front"></i></div>
                <div>
                    <div class="quick-action-label">Kartu ID</div>
                    <div class="quick-action-title">Cetak Kartu</div>
                </div>
            </div>
        </div>
        <!-- 4. Data Siswa Download -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="export_siswa_excel.php?kelas=<?= xss($kelas_filter) ?>&q=<?= xss($keyword) ?>" class="quick-action-card text-decoration-none">
                <div class="quick-action-icon blue"><i class="bi bi-download"></i></div>
                <div>
                    <div class="quick-action-label">Download</div>
                    <div class="quick-action-title">Data Siswa</div>
                </div>
            </a>
        </div>
    </div>

    <div id="formTambah" class="glass-card p-4 mb-4 border-primary border-top border-4 border-opacity-25">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Input Siswa Baru</h5>
            <button onclick="toggleForm()" class="btn-close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data" action="proses_tambah_siswa.php">
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
                            <label class="small fw-bold">NIS / NIP</label>
                            <input type="text" name="nis" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">RFID UID</label>
                            <input type="text" name="rfid_uid" class="form-control" placeholder="Tempel kartu..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="small fw-bold">Lembaga</label>
                            <select name="kelas" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <?php 
                                $q_k = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                                while($k = mysqli_fetch_assoc($q_k)) echo "<option value='".xss($k['nama_kelas'])."'>".xss($k['nama_kelas'])."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small fw-bold">Sesi</label>
                            <select name="sesi" class="form-select">
                                <option value="1">Sesi 1</option>
                                <option value="2">Sesi 2</option>
                                <option value="3">Sesi 3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">WA Ortu</label>
                            <input type="text" name="hp" class="form-control" placeholder="628..." required>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">ID Telegram</label>
                            <input type="text" name="telegram_id" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold py-2 rounded-3">SIMPAN DATA</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="glass-card p-4">
        <!-- Table Header Row -->
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold m-0 text-dark" style="font-size: 1.1rem;">Daftar Siswa</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 text-xs fw-bold" style="font-size: 0.75rem;"><?= $total_data ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex align-items-center justify-content-md-end gap-2 m-0">
                    <select name="kelas" class="form-select w-auto border-0 bg-light rounded-pill px-3 py-2" style="font-size: 0.85rem; height: 38px;" onchange="this.form.submit()">
                        <option value="">Semua Lembaga</option>
                        <?php 
                        $q_k3 = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                        while($k3 = mysqli_fetch_assoc($q_k3)): ?>
                            <option value="<?= xss($k3['nama_kelas']) ?>" <?= ($kelas_filter == $k3['nama_kelas']) ? 'selected' : '' ?>><?= xss($k3['nama_kelas']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    
                    <div class="position-relative">
                        <input type="text" name="q" class="form-control border-0 bg-light rounded-pill ps-3 pe-5" style="font-size: 0.85rem; height: 38px; width: 200px;" placeholder="Cari siswa..." value="<?= xss($keyword) ?>">
                        <button type="submit" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted p-0 me-3"><i class="bi bi-search" style="font-size: 0.85rem;"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th class="ps-3" width="8%">FOTO</th>
                        <th>NAMA & IDENTITAS</th>
                        <th class="text-center">LEMBAGA</th>
                        <th class="text-center">SALDO</th>
                        <th class="text-center" width="25%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data_siswa->num_rows > 0): ?>
                        <?php while($row = $data_siswa->fetch_assoc()): ?>
                        <tr class="border-bottom border-white border-opacity-50">
                            <td class="ps-3">
                                <div class="photo-circle">
                                    <?php $foto_rel = "img/siswa/" . $row['foto']; $foto_abs = __DIR__ . "/" . $foto_rel; if (!empty($row['foto']) && $row['foto'] != 'default.jpg' && file_exists($foto_abs)): ?>
                                        <img src="<?= $foto_rel ?>">
                                    <?php else: ?><i class="bi bi-person text-muted fs-5"></i><?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= xss($row['nama']) ?></div>
                                <div class="small text-muted" style="font-size: 0.75rem; font-weight: 500;"><?= xss($row['nis']) ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-kelas"><?= xss($row['kelas']) ?></span>
                            </td>
                            <td class="text-center fw-bold text-success" style="font-size: 0.9rem;">
                                Rp <?= number_format($row['saldo'] ?? 0, 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1.5">
                                    <!-- 1. Detail -->
                                    <button type="button" class="btn-action-circle blue btn-detail-siswa" 
                                            title="Detail Siswa"
                                            data-nama="<?= xss($row['nama']) ?>"
                                            data-nis="<?= xss($row['nis']) ?>"
                                            data-rfid="<?= xss($row['rfid_uid'] ?? '-') ?>"
                                            data-kelas="<?= xss($row['kelas']) ?>"
                                            data-sesi="<?= xss($row['sesi'] == '2' ? 'Sesi 2 (Siang)' : ($row['sesi'] == '3' ? 'Sesi 3 (Pagi)' : 'Sesi 1 (Pagi)')) ?>"
                                            data-hp="<?= xss($row['no_hp_ortu'] ?? '-') ?>"
                                            data-telegram="<?= xss($row['telegram_chat_id'] ?? '-') ?>"
                                            data-email="<?= xss($row['email'] ?? '-') ?>"
                                            data-foto="<?= xss($row['foto']) ?>"
                                            data-saldo="Rp <?= number_format($row['saldo'] ?? 0, 0, ',', '.') ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- 2. Top Up -->
                                    <a href="kantin_topup.php?nis=<?= $row['nis'] ?>" class="btn-action-circle green" title="Top Up Saldo">
                                        <i class="bi bi-wallet2"></i>
                                    </a>

                                    <!-- 3. Rekam Wajah -->
                                    <a href="daftar_wajah.php?id=<?= $row['id'] ?>" class="btn-action-circle cyan" title="Rekam Wajah">
                                        <i class="bi bi-camera"></i>
                                    </a>

                                    <!-- 4. Cetak -->
                                    <div class="dropdown d-inline-block">
                                        <button type="button" class="btn-action-circle gray" data-bs-toggle="dropdown" title="Cetak Kartu">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item py-2 px-3 rounded" href="cetak_kartu.php?id=<?= $row['id'] ?>&tipe=siswa" target="_blank"><i class="bi bi-person-vcard text-primary me-2"></i>Kartu Siswa</a></li>
                                            <li><a class="dropdown-item py-2 px-3 rounded" href="cetak_kartu.php?id=<?= $row['id'] ?>&tipe=guru" target="_blank"><i class="bi bi-person-badge text-danger me-2"></i>Kartu Guru</a></li>
                                        </ul>
                                    </div>

                                    <!-- 5. Edit -->
                                    <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="btn-action-circle blue" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- 6. Hapus -->
                                    <a href="data_siswa.php?hapus_id=<?= $row['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>" class="btn-action-circle red" onclick="return confirm('Hapus permanen?')" title="Hapus Data">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($total_halaman > 1): ?>
        <nav class="mt-4"><ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>"><a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?p=<?= $halaman - 1 ?>&q=<?= xss($keyword) ?>&kelas=<?= xss($kelas_filter) ?>"><i class="bi bi-chevron-left"></i></a></li>
            <li class="page-item active"><span class="page-link border-0 shadow-sm mx-1 rounded-circle px-3"><?= $halaman ?></span></li>
            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : '' ?>"><a class="page-link border-0 shadow-sm mx-1 rounded-circle" href="?p=<?= $halaman + 1 ?>&q=<?= xss($keyword) ?>&kelas=<?= xss($kelas_filter) ?>"><i class="bi bi-chevron-right"></i></a></li>
        </ul></nav>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalCetak" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card p-2 border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-primary">Cetak Kartu Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="cetak_kelas.php" method="GET" target="_blank">
          <div class="modal-body">
              <label class="small fw-bold mb-2">Pilih Lembaga</label>
              <select name="kelas" class="form-select py-3 border-0 bg-light rounded-4" required>
                  <option value="">-- Pilih Lembaga --</option>
                  <?php 
                  $q_m = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                  while($m = mysqli_fetch_assoc($q_m)) echo "<option value='".xss($m['nama_kelas'])."'>".xss($m['nama_kelas'])."</option>";
                  ?>
              </select>
          </div>
          <!-- <div class="modal-footer border-0">
            <button type="submit" class="btn btn-primary w-100 btn-action py-3">MULAI CETAK</button>
          </div> -->
          <div class="modal-footer border-0 flex-column">
            <button type="submit" class="btn btn-primary w-100 btn-action py-3 mb-2">
                <i class="bi bi-printer me-2"></i>PRINT PDF / CETAK
            </button>
            
            <button type="button" onclick="downloadZipMode()" class="btn btn-success w-100 btn-action py-3">
                <i class="bi bi-file-earmark-zip me-2"></i>DOWNLOAD GAMBAR (.ZIP)
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleForm() {
        <?php if($is_kuota_penuh): ?>
        alert("Gagal! Batas maksimal kuota 150 siswa telah tercapai (Saat ini: <?= $total_siswa_db ?>/150).\n\nTidak dapat menambah siswa baru. Silakan hapus data siswa yang ada terlebih dahulu.");
        return;
        <?php endif; ?>
        const form = document.getElementById('formTambah');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            form.style.display = 'none';
        }
    }

    function updateClock() {
        const options = { timeZone: '<?= $timezone_aktif ?>', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        document.getElementById('live-clock').textContent = new Intl.DateTimeFormat('id-ID', options).format(new Date());
    }
    setInterval(updateClock, 1000); updateClock();

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('img-preview');
            output.src = reader.result;
            output.style.display = 'block';
            document.getElementById('icon-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function downloadZipMode() {
        // Tambahkan '#modalCetak' sebelum selector select agar lebih spesifik
        const selectKelas = document.querySelector('#modalCetak select[name="kelas"]');
        const kelas = selectKelas.value;

        if (!kelas || kelas === "") {
            alert("Pilih lembaga terlebih dahulu di dalam modal!");
            return;
        }

        // Arahkan ke halaman generator
        window.location.href = `generate_zip_kartu.php?kelas=${encodeURIComponent(kelas)}`;
    }

    // Detail Siswa Modal handler (Vanilla JS)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-detail-siswa');
        if (!btn) return;

        const nama = btn.getAttribute('data-nama');
        const nis = btn.getAttribute('data-nis');
        const rfid = btn.getAttribute('data-rfid');
        const kelas = btn.getAttribute('data-kelas');
        const sesi = btn.getAttribute('data-sesi');
        const hp = btn.getAttribute('data-hp');
        const telegram = btn.getAttribute('data-telegram');
        const email = btn.getAttribute('data-email');
        const foto = btn.getAttribute('data-foto');
        const saldo = btn.getAttribute('data-saldo');

        document.getElementById('detail-nama').textContent = nama;
        document.getElementById('detail-nis').textContent = nis;
        document.getElementById('detail-rfid').textContent = rfid;
        document.getElementById('detail-kelas').textContent = kelas;
        document.getElementById('detail-sesi').textContent = sesi;
        document.getElementById('detail-hp').textContent = hp;
        document.getElementById('detail-telegram').textContent = telegram;
        document.getElementById('detail-email').textContent = email;
        document.getElementById('detail-saldo').textContent = saldo;

        // Foto preview
        const imgFoto = document.getElementById('detail-foto');
        if (foto && foto !== "default.jpg") {
            imgFoto.setAttribute('src', 'img/siswa/' + foto);
        } else {
            imgFoto.setAttribute('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(nama) + '&background=random&size=200');
        }

        // Topup button link
        document.getElementById('detail-btn-topup').setAttribute('href', 'kantin_topup.php?nis=' + nis);

        // Reset and load riwayat transaksi
        const riwayatBody = document.getElementById('detail-riwayat-body');
        riwayatBody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuat...</td></tr>';
        
        fetch('kantin_riwayat_siswa.php?nis=' + encodeURIComponent(nis))
            .then(response => response.text())
            .then(html => {
                riwayatBody.innerHTML = html;
            })
            .catch(() => {
                riwayatBody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-danger">Gagal memuat riwayat transaksi.</td></tr>';
            });

        // Show modal via Bootstrap 5 Native API
        const modalEl = document.getElementById('modalDetailSiswa');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    });
</script>

<!-- Modal Detail Siswa -->
<div class="modal fade" id="modalDetailSiswa" tabindex="-1" aria-labelledby="modalDetailSiswaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card p-2 border-0 shadow-lg" style="border-radius: 25px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-primary" id="modalDetailSiswaLabel"><i class="bi bi-person-card-details me-2"></i>Detail Informasi Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center mb-4">
          <div class="position-relative d-inline-block">
            <img id="detail-foto" src="" class="rounded-circle border border-4 border-primary shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
          </div>
          <h4 id="detail-nama" class="fw-bold text-dark mt-3 mb-1"></h4>
          <span id="detail-kelas" class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-bold"></span>
        </div>

        <div class="card border-0 bg-light p-3 rounded-4 mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="small fw-bold text-muted uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">SALDO E-KANTIN</span>
            <span id="detail-saldo" class="fs-4 fw-extrabold text-success" style="font-weight: 800;"></span>
          </div>
        </div>

        <div class="table-responsive mb-3">
          <table class="table table-borderless align-middle mb-0 small">
            <tbody>
              <tr class="border-bottom border-light">
                <td class="fw-bold text-muted" width="40%">NIS / ID Absensi</td>
                <td id="detail-nis" class="text-dark fw-bold"></td>
              </tr>
              <tr class="border-bottom border-light">
                <td class="fw-bold text-muted">RFID / UID Kartu</td>
                <td id="detail-rfid" class="text-dark"></td>
              </tr>
              <tr class="border-bottom border-light">
                <td class="fw-bold text-muted">Sesi Absensi</td>
                <td id="detail-sesi" class="text-dark"></td>
              </tr>
              <tr class="border-bottom border-light">
                <td class="fw-bold text-muted">No. HP Orang Tua</td>
                <td id="detail-hp" class="text-dark"></td>
              </tr>
              <tr class="border-bottom border-light">
                <td class="fw-bold text-muted">Telegram Chat ID</td>
                <td id="detail-telegram" class="text-dark"></td>
              </tr>
              <tr>
                <td class="fw-bold text-muted">Email Orang Tua</td>
                <td id="detail-email" class="text-dark"></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Riwayat Transaksi Siswa -->
        <div class="mt-4 pt-3 border-top">
          <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>10 Transaksi Terakhir</h6>
          <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
            <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.75rem;">
              <tbody id="detail-riwayat-body">
                <!-- Data loaded via AJAX -->
              </tbody>
            </table>
          </div>
        </div>

      </div>
      <div class="modal-footer border-0 pt-0">
        <a id="detail-btn-topup" href="" class="btn btn-success w-100 py-3 rounded-4 fw-bold shadow"><i class="bi bi-wallet2 me-2"></i>Top Up Saldo</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>