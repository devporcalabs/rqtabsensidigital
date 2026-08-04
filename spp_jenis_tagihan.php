<?php
session_start();
include 'koneksi.php';
include_once 'spp_init_db.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

// Hanya Admin dan Bendahara yang boleh mengelola Master Tagihan
if ($role !== 'admin' && $role !== 'bendahara') {
    die("Akses ditolak. Halaman ini khusus Administrator dan Bendahara.");
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// --- 1. PROSES TAMBAH / EDIT / HAPUS ---
if (isset($_POST['simpan'])) {
    $id             = (int)($_POST['id'] ?? 0);
    $nama           = trim($_POST['nama']);
    $nominal        = (float)$_POST['nominal'];
    $tahun_ajaran   = trim($_POST['tahun_ajaran']);
    $jatuh_tempo    = $_POST['jatuh_tempo'];
    $berlaku_untuk  = $_POST['berlaku_untuk'];
    $aktif          = isset($_POST['aktif']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE spp_jenis_tagihan SET nama=?, nominal=?, tahun_ajaran=?, jatuh_tempo=?, berlaku_untuk=?, aktif=? WHERE id=?");
        $stmt->bind_param("sdsssii", $nama, $nominal, $tahun_ajaran, $jatuh_tempo, $berlaku_untuk, $aktif, $id);
        $msg = "Jenis tagihan berhasil diperbarui!";
    } else {
        $stmt = $conn->prepare("INSERT INTO spp_jenis_tagihan (nama, nominal, tahun_ajaran, jatuh_tempo, berlaku_untuk, aktif) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsssi", $nama, $nominal, $tahun_ajaran, $jatuh_tempo, $berlaku_untuk, $aktif);
        $msg = "Jenis tagihan baru berhasil ditambahkan!";
    }

    if ($stmt->execute()) {
        echo "<script>alert('$msg'); window.location='spp_jenis_tagihan.php';</script>";
        exit;
    }
}

if (isset($_GET['hapus'])) {
    $id_del = (int)$_GET['hapus'];
    // Cek apakah sudah terpakai di tagihan_siswa
    $stmt_cek = $conn->prepare("SELECT COUNT(*) as total FROM spp_tagihan WHERE jenis_tagihan_id=?");
    $stmt_cek->bind_param("i", $id_del);
    $stmt_cek->execute();
    $tot_pakai = $stmt_cek->get_result()->fetch_assoc()['total'] ?? 0;

    if ($tot_pakai > 0) {
        echo "<script>alert('Gagal menghapus! Jenis tagihan ini sudah terpakai pada $tot_pakai tagihan siswa.'); window.location='spp_jenis_tagihan.php';</script>";
    } else {
        $stmt_del = $conn->prepare("DELETE FROM spp_jenis_tagihan WHERE id=?");
        $stmt_del->bind_param("i", $id_del);
        $stmt_del->execute();
        echo "<script>alert('Jenis tagihan berhasil dihapus!'); window.location='spp_jenis_tagihan.php';</script>";
    }
    exit;
}

// Ambil daftar kelas untuk opsi berlaku_untuk
$q_kelas = mysqli_query($conn, "SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");
$list_kelas = [];
while ($k = mysqli_fetch_assoc($q_kelas)) {
    $list_kelas[] = $k['nama_kelas'];
}

// Data jenis tagihan
$q_master = mysqli_query($conn, "SELECT * FROM spp_jenis_tagihan ORDER BY id DESC");

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Jenis Tagihan SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.5rem; }
    </style>
</head>
<body>

<div class="container py-4">
    <!-- Nav Tab SPP -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-tags-fill me-2 text-primary"></i>Master Jenis Tagihan SPP</h4>
            <p class="text-muted small mb-0">Kelola rincian biaya (SPP, Uang Gedung, Seragam, Ujian, dll)</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah" onclick="resetForm()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Tagihan Baru
        </button>
    </div>

    <!-- Tabel Data Master Tagihan -->
    <div class="card-custom shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Tagihan</th>
                        <th>Nominal</th>
                        <th>Tahun Ajaran</th>
                        <th>Jatuh Tempo</th>
                        <th>Berlaku Untuk</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($q_master) > 0): $no=1; ?>
                        <?php while ($row = mysqli_fetch_assoc($q_master)): ?>
                        <tr>
                            <td class="text-muted"><?= $no++ ?></td>
                            <td><strong class="text-dark"><?= xss($row['nama']) ?></strong></td>
                            <td class="fw-bold text-success">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= xss($row['tahun_ajaran']) ?></span></td>
                            <td class="small text-muted"><i class="bi bi-calendar-event me-1"></i><?= date('d M Y', strtotime($row['jatuh_tempo'])) ?></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info fw-bold"><?= xss($row['berlaku_untuk']) ?></span></td>
                            <td>
                                <?php if ($row['aktif']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold px-3 py-2 rounded-pill">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-warning rounded-circle me-1" onclick='editData(<?= json_encode($row) ?>)' title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Yakin ingin menghapus jenis tagihan ini?')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data jenis tagihan. Klik "Tambah Tagihan Baru".</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST">
                <input type="hidden" name="id" id="form-id" value="0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalLabel">Tambah Jenis Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nama Tagihan</label>
                        <input type="text" name="nama" id="form-nama" class="form-control" placeholder="Misal: SPP Bulan Agustus 2026" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nominal (Rp)</label>
                        <input type="number" name="nominal" id="form-nominal" class="form-control" placeholder="150000" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold mb-1">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="form-tahun_ajaran" class="form-control" value="2025/2026" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold mb-1">Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo" id="form-jatuh_tempo" class="form-control" value="<?= date('Y-m-25') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Berlaku Untuk</label>
                        <select name="berlaku_untuk" id="form-berlaku_untuk" class="form-select">
                            <option value="Semua">Semua Lembaga / Kelas</option>
                            <?php foreach ($list_kelas as $k): ?>
                            <option value="<?= xss($k) ?>"><?= xss($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="aktif" id="form-aktif" value="1" checked>
                        <label class="form-check-label fw-bold small" for="form-aktif">Status Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Data</button>
                </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function resetForm() {
    document.getElementById('form-id').value = '0';
    document.getElementById('form-nama').value = '';
    document.getElementById('form-nominal').value = '';
    document.getElementById('form-tahun_ajaran').value = '2025/2026';
    document.getElementById('form-berlaku_untuk').value = 'Semua';
    document.getElementById('form-aktif').checked = true;
    document.getElementById('modalLabel').innerText = 'Tambah Jenis Tagihan';
}

function editData(row) {
    document.getElementById('form-id').value = row.id;
    document.getElementById('form-nama').value = row.nama;
    document.getElementById('form-nominal').value = row.nominal;
    document.getElementById('form-tahun_ajaran').value = row.tahun_ajaran;
    document.getElementById('form-jatuh_tempo').value = row.jatuh_tempo;
    document.getElementById('form-berlaku_untuk').value = row.berlaku_untuk;
    document.getElementById('form-aktif').checked = row.aktif == 1;
    document.getElementById('modalLabel').innerText = 'Edit Jenis Tagihan';
    var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
    modal.show();
}
</script>

</body>
</html>
