<?php
session_start();
include 'koneksi.php';
include_once 'spp_init_db.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// --- PROSES VERIFIKASI PEMBAYARAN TRANSFER / QRIS ---
if (isset($_POST['verifikasi_aksi'])) {
    if ($role !== 'admin' && $role !== 'bendahara') {
        die("Akses ditolak. Hanya Admin dan Bendahara yang berhak melakukan verifikasi.");
    }

    $pembayaran_id = (int)$_POST['pembayaran_id'];
    $aksi          = $_POST['verifikasi_aksi']; // 'setujui' atau 'tolak'
    $catatan_admin = trim($_POST['catatan_admin'] ?? '');
    $petugas_id    = $_SESSION['user_id'] ?? 1;

    // Ambil detail pembayaran & tagihan
    $stmt_p = $conn->prepare("SELECT p.*, t.nominal as total_tagihan, t.dibayar as total_dibayar, t.sisa as total_sisa, s.nama as nama_siswa, s.no_hp_ortu, jt.nama as nama_tagihan, t.token FROM spp_pembayaran p JOIN spp_tagihan t ON p.tagihan_id=t.id JOIN siswa s ON p.nis=s.nis JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id=jt.id WHERE p.id=?");
    $stmt_p->bind_param("i", $pembayaran_id);
    $stmt_p->execute();
    $p = $stmt_p->get_result()->fetch_assoc();

    if ($p && $p['status_verifikasi'] === 'Pending') {
        if ($aksi === 'setujui') {
            $nominal_bayar = min($p['nominal'], $p['total_sisa']);
            $dibayar_baru  = $p['total_dibayar'] + $nominal_bayar;
            $sisa_baru     = max(0, $p['total_tagihan'] - $dibayar_baru);
            $status_baru   = ($sisa_baru <= 0) ? 'Lunas' : 'Sebagian';

            // 1. Update spp_pembayaran
            $stmt_up = $conn->prepare("UPDATE spp_pembayaran SET status_verifikasi='Disetujui', catatan=?, petugas_id=? WHERE id=?");
            $stmt_up->bind_param("sii", $catatan_admin, $petugas_id, $pembayaran_id);
            $stmt_up->execute();

            // 2. Update spp_tagihan
            $stmt_ut = $conn->prepare("UPDATE spp_tagihan SET dibayar=?, sisa=?, status=? WHERE id=?");
            $stmt_ut->bind_param("ddsi", $dibayar_baru, $sisa_baru, $status_baru, $p['tagihan_id']);
            $stmt_ut->execute();

            echo "<script>alert('Pembayaran BERHASIL DISETUJUI!'); window.location='spp_pembayaran.php';</script>";
        } else {
            // Tolak pembayaran
            $stmt_up = $conn->prepare("UPDATE spp_pembayaran SET status_verifikasi='Ditolak', catatan=?, petugas_id=? WHERE id=?");
            $stmt_up->bind_param("sii", $catatan_admin, $petugas_id, $pembayaran_id);
            $stmt_up->execute();

            echo "<script>alert('Pembayaran DITOLAK.'); window.location='spp_pembayaran.php';</script>";
        }
        exit;
    }
}

// Data Pembayaran Pending (Menunggu Verifikasi)
$q_pending = mysqli_query($conn, "SELECT p.*, s.nama as nama_siswa, s.kelas, jt.nama as nama_tagihan, t.sisa 
                                  FROM spp_pembayaran p 
                                  JOIN siswa s ON p.nis=s.nis 
                                  JOIN spp_tagihan t ON p.tagihan_id=t.id 
                                  JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id=jt.id 
                                  WHERE p.status_verifikasi='Pending' 
                                  ORDER BY p.id ASC");

// Data Riwayat Pembayaran (Disetujui / Ditolak / Tunai)
$q_riwayat = mysqli_query($conn, "SELECT p.*, s.nama as nama_siswa, s.kelas, jt.nama as nama_tagihan 
                                  FROM spp_pembayaran p 
                                  JOIN siswa s ON p.nis=s.nis 
                                  JOIN spp_tagihan t ON p.tagihan_id=t.id 
                                  JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id=jt.id 
                                  WHERE p.status_verifikasi != 'Pending' 
                                  ORDER BY p.id DESC LIMIT 100");

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi & Riwayat Pembayaran SPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.5rem; }
        .bukti-img { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-wallet2 me-2 text-primary"></i>Verifikasi & Riwayat Pembayaran</h4>
            <p class="text-muted small mb-0">Verifikasi bukti transfer dari orang tua & pantau seluruh transaksi masuk</p>
        </div>
    </div>

    <!-- TABEL 1: MENUNGGU VERIFIKASI -->
    <div class="card-custom shadow-sm mb-5">
        <div class="d-flex align-items-center mb-3">
            <h5 class="fw-bold text-warning m-0 me-2"><i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi Bendahara</h5>
            <span class="badge bg-warning text-dark rounded-pill px-3"><?= mysqli_num_rows($q_pending) ?> Transaksi</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl & No. Transaksi</th>
                        <th>Siswa & Kelas</th>
                        <th>Tagihan</th>
                        <th>Metode</th>
                        <th>Nominal Transfer</th>
                        <th>Bukti Transfer</th>
                        <th class="text-end">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($q_pending) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($q_pending)): ?>
                        <tr>
                            <td>
                                <div><strong class="text-dark"><?= xss($p['nomor_transaksi']) ?></strong></div>
                                <small class="text-muted"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></small>
                            </td>
                            <td>
                                <strong class="text-dark d-block"><?= xss($p['nama_siswa']) ?></strong>
                                <small class="text-muted"><?= xss($p['kelas']) ?> (<?= xss($p['nis']) ?>)</small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= xss($p['nama_tagihan']) ?></span></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info fw-bold"><?= xss($p['metode']) ?></span></td>
                            <td class="fw-bold text-success">Rp <?= number_format($p['nominal'], 0, ',', '.') ?></td>
                            <td>
                                <?php if (!empty($p['bukti_transfer'])): ?>
                                    <a href="img/bukti_spp/<?= xss($p['bukti_transfer']) ?>" target="_blank">
                                        <img src="img/bukti_spp/<?= xss($p['bukti_transfer']) ?>" class="bukti-img border shadow-sm">
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($role == 'admin' || $role == 'bendahara'): ?>
                                <button class="btn btn-sm btn-success rounded-pill px-3 me-1 fw-bold" onclick='prosesVerif(<?= json_encode($p) ?>, "setujui")'>
                                    <i class="bi bi-check-circle me-1"></i> Setujui
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick='prosesVerif(<?= json_encode($p) ?>, "tolak")'>
                                    <i class="bi bi-x-circle me-1"></i> Tolak
                                </button>
                                <?php else: ?>
                                <span class="badge bg-secondary">Perlu Akses Bendahara</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted small"><i class="bi bi-check-all me-1 text-success fs-5"></i>Tidak ada transaksi menunggu verifikasi saat ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL 2: RIWAYAT TRANSAKSI DISAKSIKAN / VERIFIKASI -->
    <div class="card-custom shadow-sm">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Seluruh Transaksi</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tgl & No. Transaksi</th>
                        <th>Siswa</th>
                        <th>Jenis Tagihan</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                        <th>Status Verifikasi</th>
                        <th class="text-end">Kwitansi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($q_riwayat) > 0): ?>
                        <?php while ($r = mysqli_fetch_assoc($q_riwayat)): ?>
                        <tr>
                            <td>
                                <strong class="text-dark"><?= xss($r['nomor_transaksi']) ?></strong>
                                <div class="small text-muted"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
                            </td>
                            <td>
                                <div><strong><?= xss($r['nama_siswa']) ?></strong></div>
                                <small class="text-muted"><?= xss($r['kelas']) ?></small>
                            </td>
                            <td><?= xss($r['nama_tagihan']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= xss($r['metode']) ?></span></td>
                            <td class="fw-bold text-success">Rp <?= number_format($r['nominal'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($r['status_verifikasi'] == 'Disetujui'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill">Disetujui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold px-3 py-1 rounded-pill">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if ($r['status_verifikasi'] == 'Disetujui'): ?>
                                <a href="spp_kwitansi.php?tagihan_id=<?= $r['tagihan_id'] ?>&pembayaran_id=<?= $r['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-printer me-1"></i> Kwitansi
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Konfirmasi Verifikasi -->
<div class="modal fade" id="modalVerif" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST">
                <input type="hidden" name="pembayaran_id" id="v-pembayaran_id">
                <input type="hidden" name="verifikasi_aksi" id="v-verifikasi_aksi">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="v-title">Konfirmasi Verifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">Siswa: <strong id="v-siswa" class="text-dark"></strong></div>
                        <div class="small text-muted">Nominal Transfer: <strong id="v-nominal" class="text-success"></strong></div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Catatan Admin / Bendahara (Opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="v-btn-submit" class="btn btn-primary rounded-pill px-4 fw-bold">Proses Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function prosesVerif(row, aksi) {
    document.getElementById('v-pembayaran_id').value = row.id;
    document.getElementById('v-verifikasi_aksi').value = aksi;
    document.getElementById('v-siswa').innerText = row.nama_siswa + ' (' + row.kelas + ')';
    document.getElementById('v-nominal').innerText = 'Rp ' + Number(row.nominal).toLocaleString('id-ID');

    if (aksi === 'setujui') {
        document.getElementById('v-title').innerText = 'Setujui Pembayaran Transfer';
        document.getElementById('v-btn-submit').className = 'btn btn-success rounded-pill px-4 fw-bold';
        document.getElementById('v-btn-submit').innerText = 'Ya, Setujui Pembayaran';
    } else {
        document.getElementById('v-title').innerText = 'Tolak Pembayaran Transfer';
        document.getElementById('v-btn-submit').className = 'btn btn-danger rounded-pill px-4 fw-bold';
        document.getElementById('v-btn-submit').innerText = 'Ya, Tolak Pembayaran';
    }

    var modal = new bootstrap.Modal(document.getElementById('modalVerif'));
    modal.show();
}
</script>

</body>
</html>
