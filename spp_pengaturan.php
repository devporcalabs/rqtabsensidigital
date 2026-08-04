<?php
session_start();
include 'koneksi.php';
include_once 'spp_init_db.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'admin' && $role !== 'bendahara') {
    die("Akses ditolak. Hanya Admin dan Bendahara yang berhak mengakses Pengaturan SPP.");
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

$msg_info = null;

// --- PROSES SIMPAN PENGATURAN ---
if (isset($_POST['simpan_pengaturan'])) {
    $nama_bank           = trim($_POST['nama_bank']);
    $no_rekening         = trim($_POST['no_rekening']);
    $atas_nama           = trim($_POST['atas_nama']);
    $wa_template_tagihan = trim($_POST['wa_template_tagihan']);
    $wa_template_lunas   = trim($_POST['wa_template_lunas']);

    // Cek upload gambar QRIS baru
    $qris_image_name = $_POST['qris_old'] ?? 'qris_default.png';
    if (isset($_FILES['qris_image']) && $_FILES['qris_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['qris_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $nama_baru = 'qris_statis_' . date('Ymd_His') . '.' . $ext;
            if (move_uploaded_file($_FILES['qris_image']['tmp_name'], __DIR__ . '/img/' . $nama_baru)) {
                $qris_image_name = $nama_baru;
            }
        }
    }

    $q_check = mysqli_query($conn, "SELECT id FROM spp_pengaturan LIMIT 1");
    if (mysqli_num_rows($q_check) > 0) {
        $stmt_u = $conn->prepare("UPDATE spp_pengaturan SET nama_bank=?, no_rekening=?, atas_nama=?, qris_image=?, wa_template_tagihan=?, wa_template_lunas=? WHERE id=1");
        $stmt_u->bind_param("ssssss", $nama_bank, $no_rekening, $atas_nama, $qris_image_name, $wa_template_tagihan, $wa_template_lunas);
        $stmt_u->execute();
    } else {
        $stmt_i = $conn->prepare("INSERT INTO spp_pengaturan (nama_bank, no_rekening, atas_nama, qris_image, wa_template_tagihan, wa_template_lunas) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_i->bind_param("ssssss", $nama_bank, $no_rekening, $atas_nama, $qris_image_name, $wa_template_tagihan, $wa_template_lunas);
        $stmt_i->execute();
    }

    $msg_info = ['status' => 'success', 'pesan' => 'Pengaturan Pembayaran & Notifikasi SPP berhasil diperbarui!'];
}

// Ambil data pengaturan saat ini
$q_set = mysqli_query($conn, "SELECT * FROM spp_pengaturan LIMIT 1");
$spp_set = mysqli_fetch_assoc($q_set);

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan SPP & Rekening</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 2rem; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-gear-wide-connected me-2 text-primary"></i>Pengaturan SPP & Rekening Sekolah</h4>
            <p class="text-muted small mb-0">Konfigurasi akun rekening bank, QRIS statis, dan template notifikasi WhatsApp SPP</p>
        </div>
    </div>

    <?php if ($msg_info): ?>
        <div class="alert alert-<?= $msg_info['status'] ?> alert-dismissible fade show rounded-4 p-3 mb-4">
            <?= $msg_info['pesan'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="qris_old" value="<?= xss($spp_set['qris_image'] ?? 'qris_default.png') ?>">

        <div class="row g-4">
            <!-- Kolom 1: Rekening Bank & QRIS -->
            <div class="col-md-6">
                <div class="card-custom shadow-sm h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bank me-2 text-primary"></i>Rekening Pembayaran Sekolah</h5>
                    
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nama Bank / E-Wallet</label>
                        <input type="text" name="nama_bank" class="form-control" value="<?= xss($spp_set['nama_bank'] ?? 'Bank BCA') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nomor Rekening / Akun</label>
                        <input type="text" name="no_rekening" class="form-control" value="<?= xss($spp_set['no_rekening'] ?? '1234567890') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Atas Nama Rekening</label>
                        <input type="text" name="atas_nama" class="form-control" value="<?= xss($spp_set['atas_nama'] ?? 'Rumah Quran Temi') ?>" required>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-qr-code me-2 text-success"></i>Upload Gambar QRIS Statis</h5>
                    <div class="mb-3 text-center p-3 bg-light rounded-4 border">
                        <img src="img/<?= xss($spp_set['qris_image'] ?? 'qris_default.png') ?>" style="max-width: 180px;" class="img-fluid border rounded-3 p-2 bg-white shadow-sm mb-2">
                        <input type="file" name="qris_image" class="form-control mt-2" accept="image/*">
                        <small class="text-muted d-block mt-1">Format: JPG atau PNG (Maks 2MB)</small>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Template WhatsApp Notifikasi -->
            <div class="col-md-6">
                <div class="card-custom shadow-sm h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-whatsapp me-2 text-success"></i>Template Pesan WhatsApp</h5>

                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Template Pesan Tagihan SPP Baru</label>
                        <textarea name="wa_template_tagihan" class="form-control" rows="5" required><?= xss($spp_set['wa_template_tagihan'] ?? '') ?></textarea>
                        <small class="text-muted mt-1 d-block">Variabel: <code>{nama_siswa}</code>, <code>{kelas}</code>, <code>{nama_tagihan}</code>, <code>{nominal}</code>, <code>{jatuh_tempo}</code>, <code>{link_portal}</code></small>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold mb-1">Template Pesan Konfirmasi Pelunasan SPP</label>
                        <textarea name="wa_template_lunas" class="form-control" rows="5" required><?= xss($spp_set['wa_template_lunas'] ?? '') ?></textarea>
                        <small class="text-muted mt-1 d-block">Variabel: <code>{nama_siswa}</code>, <code>{nama_tagihan}</code>, <code>{nominal}</code>, <code>{link_kwitansi}</code></small>
                    </div>

                    <div class="pt-3">
                        <button type="submit" name="simpan_pengaturan" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i> Simpan Seluruh Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

</body>
</html>
