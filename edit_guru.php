<?php
session_start();
include 'koneksi.php';

// --- SECURITY: INISIALISASI CSRF TOKEN ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// 1. Cek Login
if(!isset($_SESSION['login'])){ header("location: login.php"); exit; }

// 2. Ambil ID secara aman
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 3. Ambil Data Guru (Prepared Statement)
$stmt_get = $conn->prepare("SELECT * FROM guru WHERE id = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$res_get = $stmt_get->get_result();
$d = $res_get->fetch_assoc();

if(!$d) { header("location: data_guru.php"); exit; }

// --- LOGIKA UPDATE DATA ---
if(isset($_POST['update'])){
    // Validasi CSRF
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
        die("Serangan CSRF terdeteksi!");
    }

    $nip     = $_POST['nip'];
    $rfid    = $_POST['rfid_uid'];
    $nama    = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $no_hp   = $_POST['no_hp'];
    $email   = $_POST['email'];

    // --- ANTI ERROR: CEK DUPLIKAT RFID/NIP ---
    $stmt_cek = $conn->prepare("SELECT id FROM guru WHERE (rfid_uid = ? OR nip = ?) AND id != ?");
    $stmt_cek->bind_param("ssi", $rfid, $nip, $id);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();

    if($res_cek->num_rows > 0) {
        echo "<script>alert('Gagal! NIP atau Nomor RFID sudah digunakan oleh guru lain.'); window.history.back();</script>";
        exit;
    }

    $foto_final = $d['foto'];
    if(!empty($_FILES['foto']['name'])){
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if(in_array($ext, $allowed)){
            $foto_baru = "guru_" . $nip . "_" . time() . "." . $ext;
            if(move_uploaded_file($_FILES['foto']['tmp_name'], "img/guru/" . $foto_baru)){
                // Hapus foto lama jika bukan default
                if(!empty($d['foto']) && $d['foto'] !== 'default.jpg' && file_exists("img/guru/" . $d['foto'])){
                    unlink("img/guru/" . $d['foto']);
                }
                $foto_final = $foto_baru;
            }
        }
    }

    // Update
    $stmt_upd = $conn->prepare("UPDATE guru SET 
                nip=?, rfid_uid=?, nama=?, jabatan=?, no_hp=?, email=?, foto=? 
                WHERE id=?");
    $stmt_upd->bind_param("sssssssi", $nip, $rfid, $nama, $jabatan, $no_hp, $email, $foto_final, $id);

    if($stmt_upd->execute()){
        echo "<script>alert('Data Guru Berhasil Diperbarui!'); window.location='data_guru.php';</script>";
        exit;
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Guru - <?= xss($d['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0f3f9; min-height: 100vh; }
        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 30px; }
        .form-control, .form-select { border-radius: 12px; }
        .btn-update { border-radius: 15px; font-weight: 800; }
    </style>
</head>
<body>

<div class="container py-5" style="margin-top: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-5 shadow-lg border-0">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <h4 class="fw-bold m-0 text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Data Guru</h4>
                    <a href="data_guru.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Kembali</a>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="mx-auto rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 140px; height: 140px; overflow: hidden; border: 4px solid #fff !important; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                                <?php 
                                $foto_path = "img/guru/" . $d['foto'];
                                $foto_tampil = (file_exists($foto_path) && !empty($d['foto'])) ? $foto_path : 'img/siswa/default.jpg';
                                ?>
                                <img id="img-preview" src="<?= $foto_tampil ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <label for="foto-input" class="btn btn-sm btn-outline-primary mt-3 rounded-pill px-3">Ubah Foto</label>
                            <input type="file" name="foto" id="foto-input" class="d-none" accept="image/*" onchange="previewImage(event)">
                        </div>

                        <div class="col-md-8">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">NIP / ID Guru</label>
                                    <input type="text" name="nip" value="<?= xss($d['nip']) ?>" class="form-control font-monospace" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">RFID UID</label>
                                    <input type="text" name="rfid_uid" value="<?= xss($d['rfid_uid']) ?>" class="form-control font-monospace" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= xss($d['nama']) ?>" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-muted">Jabatan</label>
                                <input type="text" name="jabatan" value="<?= xss($d['jabatan']) ?>" class="form-control" required>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">No. WhatsApp</label>
                                    <input type="text" name="no_hp" value="<?= xss($d['no_hp']) ?>" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted">Alamat Email</label>
                                    <input type="email" name="email" value="<?= xss($d['email']) ?>" class="form-control">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="update" class="btn btn-primary btn-update py-3 shadow">SIMPAN PERUBAHAN</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('img-preview');
            preview.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</body>
</html>
