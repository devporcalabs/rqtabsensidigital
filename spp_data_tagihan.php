<?php
session_start();
include 'koneksi.php';
include_once 'spp_init_db.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// --- PROSES BAYAR TUNAI LANGSUNG DARI ADMIN / BENDAHARA ---
if (isset($_POST['bayar_tunai'])) {
    $tagihan_id = (int)$_POST['tagihan_id'];
    $nominal_bayar = (float)$_POST['nominal_bayar'];
    $catatan = trim($_POST['catatan'] ?? '');
    $petugas_id = $_SESSION['user_id'] ?? 1;

    // Ambil data tagihan saat ini
    $stmt_t = $conn->prepare("SELECT t.*, s.nama, s.no_hp_ortu, s.kelas, jt.nama as nama_tagihan FROM spp_tagihan t JOIN siswa s ON t.nis=s.nis JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id=jt.id WHERE t.id=?");
    $stmt_t->bind_param("i", $tagihan_id);
    $stmt_t->execute();
    $t = $stmt_t->get_result()->fetch_assoc();

    if ($t && $nominal_bayar > 0) {
        $nominal_bayar = min($nominal_bayar, $t['sisa']);
        $dibayar_baru = $t['dibayar'] + $nominal_bayar;
        $sisa_baru = max(0, $t['nominal'] - $dibayar_baru);
        $status_baru = ($sisa_baru <= 0) ? 'Lunas' : 'Sebagian';

        // Generate Nomor Transaksi
        $no_trx = 'SPP-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999));

        // Insert ke spp_pembayaran
        $stmt_p = $conn->prepare("INSERT INTO spp_pembayaran (nomor_transaksi, tagihan_id, nis, metode, nominal, status_verifikasi, catatan, petugas_id) VALUES (?, ?, ?, 'Tunai', ?, 'Disetujui', ?, ?)");
        $stmt_p->bind_param("sisdsi", $no_trx, $tagihan_id, $t['nis'], $nominal_bayar, $catatan, $petugas_id);
        $stmt_p->execute();

        // Update spp_tagihan
        $stmt_u = $conn->prepare("UPDATE spp_tagihan SET dibayar=?, sisa=?, status=? WHERE id=?");
        $stmt_u->bind_param("ddsi", $dibayar_baru, $sisa_baru, $status_baru, $tagihan_id);
        $stmt_u->execute();

        echo "<script>alert('Pembayaran Tunai sebesar Rp " . number_format($nominal_bayar, 0, ',', '.') . " berhasil dicatat!'); window.location='spp_data_tagihan.php';</script>";
        exit;
    }
}

// --- PROSES KOSONGKAN DATA TAGIHAN & PEMBAYARAN SPP ---
if (isset($_POST['kosongkan_tagihan']) && ($role == 'admin' || $role == 'bendahara')) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0;");
    mysqli_query($conn, "TRUNCATE TABLE spp_pembayaran");
    mysqli_query($conn, "TRUNCATE TABLE spp_tagihan");
    mysqli_query($conn, "TRUNCATE TABLE spp_log_notifikasi");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1;");

    echo "<script>alert('Seluruh data tagihan dan riwayat pembayaran SPP telah berhasil dikosongkan!'); window.location='spp_data_tagihan.php';</script>";
    exit;
}

// --- FILTER DATA ---
$f_kelas  = trim($_GET['kelas'] ?? '');
$f_status = trim($_GET['status'] ?? '');
$f_q      = trim($_GET['q'] ?? '');

$sql = "SELECT t.*, s.nama as nama_siswa, s.kelas, s.no_hp_ortu, jt.nama as nama_tagihan, jt.jatuh_tempo, jt.tahun_ajaran
        FROM spp_tagihan t
        JOIN siswa s ON t.nis = s.nis
        JOIN spp_jenis_tagihan jt ON t.jenis_tagihan_id = jt.id
        WHERE 1=1";

if (!empty($f_kelas)) {
    $sql .= " AND s.kelas = '" . mysqli_real_escape_string($conn, $f_kelas) . "'";
}
if (!empty($f_status)) {
    $sql .= " AND t.status = '" . mysqli_real_escape_string($conn, $f_status) . "'";
}
if (!empty($f_q)) {
    $q_clean = mysqli_real_escape_string($conn, $f_q);
    $sql .= " AND (s.nama LIKE '%$q_clean%' OR s.nis LIKE '%$q_clean%' OR jt.nama LIKE '%$q_clean%')";
}

$sql .= " ORDER BY t.id DESC";
$q_tagihan = mysqli_query($conn, $sql);
$tagihan_list = [];
if ($q_tagihan) {
    while ($r_tag = mysqli_fetch_assoc($q_tagihan)) {
        $tagihan_list[] = $r_tag;
    }
}

// Opsi list kelas untuk filter
$q_kelas_list = mysqli_query($conn, "SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");

// Base URL portal
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tagihan SPP Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 1.5rem; }

        .btn-action-spp {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            padding: 6px 12px !important;
            border-radius: 12px !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            line-height: 1.2 !important;
            text-decoration: none !important;
            transition: all 0.15s ease-in-out !important;
            border: 1px solid transparent;
        }
        .btn-action-spp:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .btn-action-spp.btn-wa {
            background-color: #10b981 !important;
            color: #ffffff !important;
            border-color: #10b981 !important;
        }
        .btn-action-spp.btn-copy {
            background-color: #ffffff !important;
            color: #475569 !important;
            border-color: #cbd5e1 !important;
            padding: 6px 10px !important;
        }
        .btn-action-spp.btn-bayar {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }
        .btn-action-spp.btn-kwitansi {
            background-color: #ffffff !important;
            color: #2563eb !important;
            border-color: #3b82f6 !important;
        }

        /* Tabel Tagihan Content Font Size */
        .table-tagihan {
            font-size: 0.85rem !important;
        }
        .table-tagihan th {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            text-uppercase: uppercase;
            letter-spacing: 0.5px;
            color: #475569 !important;
            background-color: #f8fafc !important;
            padding: 12px 14px !important;
        }
        .table-tagihan td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
        }
        .table-tagihan small {
            font-size: 0.78rem !important;
        }
        .table-tagihan .badge {
            font-size: 0.75rem !important;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Data Tagihan SPP Siswa</h4>
            <p class="text-muted small mb-0">Kelola status pembayaran, penerimaan tunai, dan link portal orang tua</p>
        </div>
        <?php if ($role == 'admin' || $role == 'bendahara'): ?>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalKosongkan">
                <i class="bi bi-trash3 me-1"></i> Kosongkan Tagihan
            </button>
            <a href="spp_generate_tagihan.php" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-magic me-1"></i> Generate Tagihan
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Filter Section -->
    <div class="card-custom shadow-sm mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Filter Kelas / Lembaga</label>
                <select name="kelas" class="form-select border-0 bg-light">
                    <option value="">-- Semua Kelas --</option>
                    <?php while ($kls = mysqli_fetch_assoc($q_kelas_list)): ?>
                    <option value="<?= xss($kls['nama_kelas']) ?>" <?= $f_kelas == $kls['nama_kelas'] ? 'selected' : '' ?>><?= xss($kls['nama_kelas']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">Status Pembayaran</label>
                <select name="status" class="form-select border-0 bg-light">
                    <option value="">-- Semua Status --</option>
                    <option value="Belum Bayar" <?= $f_status == 'Belum Bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                    <option value="Sebagian" <?= $f_status == 'Sebagian' ? 'selected' : '' ?>>Sebagian (Cicilan)</option>
                    <option value="Lunas" <?= $f_status == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                    <option value="Terlambat" <?= $f_status == 'Terlambat' ? 'selected' : '' ?>>Terlambat</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold text-muted">Pencarian (Nama / NIS / Tagihan)</label>
                <input type="text" name="q" value="<?= xss($f_q) ?>" class="form-control border-0 bg-light" placeholder="Cari nama atau NIS...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>

    <!-- Desktop Data Table (d-none d-md-block) -->
    <div class="card-custom shadow-sm d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-tagihan">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIS & Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tagihan</th>
                        <th>Nominal</th>
                        <th>Dibayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tagihan_list)): $no=1; ?>
                        <?php foreach ($tagihan_list as $row): 
                            $badge_color = 'bg-secondary';
                            if ($row['status'] == 'Lunas') $badge_color = 'bg-success';
                            elseif ($row['status'] == 'Sebagian') $badge_color = 'bg-warning text-dark';
                            elseif ($row['status'] == 'Belum Bayar') $badge_color = 'bg-danger';
                            
                            $link_portal = $base_url . '/spp_portal.php?token=' . $row['token'];
                            
                            $hp_ortu = preg_replace('/[^0-9]/', '', $row['no_hp_ortu'] ?? '');
                            if (!empty($hp_ortu) && substr($hp_ortu, 0, 1) === '0') {
                                $hp_ortu = '62' . substr($hp_ortu, 1);
                            }
                            $pesan_wa = "Bismillah, Yth. Orang Tua dari " . $row['nama_siswa'] . " (" . $row['kelas'] . ").\n\nTagihan SPP *" . $row['nama_tagihan'] . "* sebesar *Rp " . number_format($row['nominal'], 0, ',', '.') . "* (Sisa: *Rp " . number_format($row['sisa'], 0, ',', '.') . "*).\n\nLink pembayaran:\n" . $link_portal . "\n\nTerima kasih.";
                            $wa_url = !empty($hp_ortu) ? "https://api.whatsapp.com/send?phone=" . $hp_ortu . "&text=" . urlencode($pesan_wa) : "https://api.whatsapp.com/send?text=" . urlencode($pesan_wa);
                        ?>
                        <tr>
                            <td class="text-muted"><?= $no++ ?></td>
                            <td>
                                <strong class="text-dark d-block"><?= xss($row['nama_siswa']) ?></strong>
                                <small class="text-muted">NIS: <?= xss($row['nis']) ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= xss($row['kelas']) ?></span></td>
                            <td>
                                <div><strong><?= xss($row['nama_tagihan']) ?></strong></div>
                                <small class="text-muted">JT: <?= date('d/m/Y', strtotime($row['jatuh_tempo'])) ?></small>
                            </td>
                            <td class="fw-bold">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                            <td class="text-success fw-bold">Rp <?= number_format($row['dibayar'], 0, ',', '.') ?></td>
                            <td class="text-danger fw-bold">Rp <?= number_format($row['sisa'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $badge_color ?> bg-opacity-10 text-<?= $row['status'] == 'Lunas' ? 'success' : ($row['status'] == 'Sebagian' ? 'warning' : 'danger') ?> fw-bold px-3 py-2 rounded-pill">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center justify-content-end gap-1" style="white-space: nowrap;">
                                    <a href="<?= $wa_url ?>" target="_blank" class="btn-action-spp btn-wa" title="Kirim Tagihan ke WhatsApp Orang Tua">
                                        <i class="bi bi-whatsapp me-1" style="font-size: 0.85rem;"></i> Kirim ke WA
                                    </a>
                                    <button type="button" class="btn-action-spp btn-copy" onclick="copyLink('<?= $link_portal ?>')" title="Salin Link Portal Ortu">
                                        <i class="bi bi-copy" style="font-size: 0.85rem;"></i>
                                    </button>
                                    
                                    <?php if ($row['sisa'] > 0 && ($role == 'admin' || $role == 'bendahara' || $role == 'operator')): ?>
                                    <button class="btn-action-spp btn-bayar" onclick='bukaModalBayar(<?= json_encode($row) ?>)' title="Bayar Tunai">
                                        <i class="bi bi-wallet2 me-1" style="font-size: 0.85rem;"></i> Bayar
                                    </button>
                                    <?php endif; ?>

                                    <?php if ($row['dibayar'] > 0): ?>
                                    <a href="spp_kwitansi.php?tagihan_id=<?= $row['id'] ?>" class="btn-action-spp btn-kwitansi" target="_blank" title="Cetak Kwitansi">
                                        <i class="bi bi-file-earmark-text me-1" style="font-size: 0.85rem;"></i> Kwitansi
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">Data tagihan tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Smartphone Cards (d-block d-md-none) -->
    <div class="d-block d-md-none">
        <?php if (!empty($tagihan_list)): ?>
            <?php foreach ($tagihan_list as $row): 
                $badge_color = 'bg-secondary';
                if ($row['status'] == 'Lunas') $badge_color = 'bg-success';
                elseif ($row['status'] == 'Sebagian') $badge_color = 'bg-warning text-dark';
                elseif ($row['status'] == 'Belum Bayar') $badge_color = 'bg-danger';
                
                $link_portal = $base_url . '/spp_portal.php?token=' . $row['token'];
                
                $hp_ortu = preg_replace('/[^0-9]/', '', $row['no_hp_ortu'] ?? '');
                if (!empty($hp_ortu) && substr($hp_ortu, 0, 1) === '0') {
                    $hp_ortu = '62' . substr($hp_ortu, 1);
                }
                $pesan_wa = "Bismillah, Yth. Orang Tua dari " . $row['nama_siswa'] . " (" . $row['kelas'] . ").\n\nTagihan SPP *" . $row['nama_tagihan'] . "* sebesar *Rp " . number_format($row['nominal'], 0, ',', '.') . "* (Sisa: *Rp " . number_format($row['sisa'], 0, ',', '.') . "*).\n\nLink pembayaran:\n" . $link_portal . "\n\nTerima kasih.";
                $wa_url = !empty($hp_ortu) ? "https://api.whatsapp.com/send?phone=" . $hp_ortu . "&text=" . urlencode($pesan_wa) : "https://api.whatsapp.com/send?text=" . urlencode($pesan_wa);
            ?>
            <div class="card mb-3 border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 <?= $row['status'] == 'Lunas' ? 'border-success' : ($row['status'] == 'Sebagian' ? 'border-warning' : 'border-danger') ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong class="text-dark fs-6 d-block mb-1"><?= xss($row['nama_siswa']) ?></strong>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="badge bg-light text-dark border">NIS: <?= xss($row['nis']) ?></span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><?= xss($row['kelas']) ?></span>
                        </div>
                    </div>
                    <span class="badge <?= $badge_color ?> bg-opacity-10 text-<?= $row['status'] == 'Lunas' ? 'success' : ($row['status'] == 'Sebagian' ? 'warning' : 'danger') ?> fw-bold px-3 py-2 rounded-pill">
                        <?= $row['status'] ?>
                    </span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tagihan:</span>
                        <strong class="text-dark"><?= xss($row['nama_tagihan']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Nominal:</span>
                        <span class="fw-bold text-dark">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Sisa Tagihan:</span>
                        <span class="fw-bold text-danger">Rp <?= number_format($row['sisa'], 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= $wa_url ?>" target="_blank" class="btn btn-success btn-sm flex-fill rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-whatsapp me-1 fs-6"></i> Kirim WA
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-center" onclick="copyLink('<?= $link_portal ?>')">
                        <i class="bi bi-copy me-1"></i> Copy
                    </button>
                    <?php if ($row['sisa'] > 0 && ($role == 'admin' || $role == 'bendahara' || $role == 'operator')): ?>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 d-flex align-items-center justify-content-center" onclick='bukaModalBayar(<?= json_encode($row) ?>)'>
                        <i class="bi bi-wallet2 me-1"></i> Bayar
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card p-4 text-center text-muted rounded-4 bg-white shadow-sm border-0">Data tagihan tidak ditemukan.</div>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- Modal Input Pembayaran Tunai -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST">
                <input type="hidden" name="tagihan_id" id="m-tagihan_id">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Input Pembayaran Tunai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">Siswa / Tagihan:</div>
                        <strong id="m-nama-siswa" class="d-block text-primary"></strong>
                        <div id="m-nama-tagihan" class="small fw-semibold text-dark mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Sisa Tagihan</label>
                        <input type="text" id="m-sisa-text" class="form-control bg-light fw-bold text-danger" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nominal Bayar (Rp)</label>
                        <input type="number" name="nominal_bayar" id="m-nominal_bayar" class="form-control form-control-lg fw-bold" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="bayar_tunai" class="btn btn-success rounded-pill px-4 fw-bold">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirm Kosongkan Tagihan -->
<?php if ($role == 'admin' || $role == 'bendahara'): ?>
<div class="modal fade" id="modalKosongkan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Kosongkan Data Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted mb-3">
                        Apakah Anda yakin ingin <strong>menghapus/mengosongkan seluruh data tagihan siswa</strong> dan riwayat pembayaran SPP?
                    </p>
                    <div class="alert alert-warning small rounded-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Tindakan ini tidak dapat dibatalkan. Seluruh data tagihan akan di-reset (0 record).
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="kosongkan_tagihan" class="btn btn-danger rounded-pill px-4 fw-bold">Ya, Kosongkan Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyLink(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function() {
            alert('Link Portal Orang Tua berhasil disalin:\n' + url);
        }).catch(function() {
            fallbackCopyText(url);
        });
    } else {
        fallbackCopyText(url);
    }
}

function fallbackCopyText(url) {
    var tempInput = document.createElement("textarea");
    tempInput.value = url;
    tempInput.style.position = "fixed";
    tempInput.style.left = "-9999px";
    tempInput.style.top = "-9999px";
    document.body.appendChild(tempInput);
    tempInput.focus();
    tempInput.select();
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            alert('Link Portal Orang Tua berhasil disalin:\n' + url);
        } else {
            prompt('Salin link portal secara manual:', url);
        }
    } catch (err) {
        prompt('Salin link portal secara manual:', url);
    }
    document.body.removeChild(tempInput);
}

function bukaModalBayar(row) {
    document.getElementById('m-tagihan_id').value = row.id;
    document.getElementById('m-nama-siswa').innerText = row.nama_siswa + ' (' + row.kelas + ')';
    document.getElementById('m-nama-tagihan').innerText = row.nama_tagihan;
    document.getElementById('m-sisa-text').value = 'Rp ' + Number(row.sisa).toLocaleString('id-ID');
    document.getElementById('m-nominal_bayar').value = row.sisa;
    document.getElementById('m-nominal_bayar').max = row.sisa;
    var modal = new bootstrap.Modal(document.getElementById('modalBayar'));
    modal.show();
}
</script>

</body>
</html>
