<?php
session_start();
include 'koneksi.php';
include_once 'spp_init_db.php';

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }
$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role !== 'admin' && $role !== 'bendahara') {
    die("Akses ditolak. Halaman ini khusus Administrator dan Bendahara.");
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// --- PROSES GENERATE TAGIHAN ---
$result_info = null;

if (isset($_POST['generate'])) {
    $jenis_tagihan_id = (int)$_POST['jenis_tagihan_id'];
    $target_siswa     = $_POST['target_siswa'] ?? 'semua'; // 'semua', 'kelas', 'nis'
    $kelas_pilih      = $_POST['kelas'] ?? '';
    $nis_pilih        = trim($_POST['nis'] ?? '');
    $kirim_wa_aktif   = isset($_POST['kirim_wa']) ? 1 : 0;
    $opsi_jadwal      = $_POST['opsi_jadwal'] ?? 'sekarang';
    $custom_datetime  = $_POST['custom_datetime'] ?? '';

    // Ambil data jenis tagihan
    $stmt_jt = $conn->prepare("SELECT * FROM spp_jenis_tagihan WHERE id=?");
    $stmt_jt->bind_param("i", $jenis_tagihan_id);
    $stmt_jt->execute();
    $jt = $stmt_jt->get_result()->fetch_assoc();

    if (!$jt) {
        $result_info = ['status' => 'error', 'pesan' => 'Jenis tagihan tidak ditemukan.'];
    } else {
        // Tentukan jadwal pengingat (scheduled_at)
        $scheduled_at = date('Y-m-d H:i:s');
        $label_jadwal = "Sekarang";

        if (!$kirim_wa_aktif) {
            $scheduled_at = null;
            $label_jadwal = "Tidak Dikirim";
        } elseif ($opsi_jadwal === 'custom' && !empty($custom_datetime)) {
            $scheduled_at = date('Y-m-d H:i:s', strtotime($custom_datetime));
            $label_jadwal = date('d M Y H:i', strtotime($scheduled_at)) . " WIB";
        } else {
            $scheduled_at = date('Y-m-d H:i:s');
            $label_jadwal = "Sekarang";
        }

        // Query ambil daftar siswa target
        $sql_siswa = "SELECT nis, nama, no_hp_ortu AS no_hp, kelas FROM siswa WHERE 1=1";
        if ($target_siswa === 'kelas' && !empty($kelas_pilih)) {
            $sql_siswa .= " AND kelas = '" . mysqli_real_escape_string($conn, $kelas_pilih) . "'";
        } elseif ($target_siswa === 'nis' && !empty($nis_pilih)) {
            $sql_siswa .= " AND nis = '" . mysqli_real_escape_string($conn, $nis_pilih) . "'";
        }

        $q_siswa = mysqli_query($conn, $sql_siswa);

        // Ambil template WA & Pengaturan
        $q_setting = mysqli_query($conn, "SELECT * FROM spp_pengaturan LIMIT 1");
        $setting = mysqli_fetch_assoc($q_setting);
        $wa_template = $setting['wa_template_tagihan'] ?? "Tagihan SPP {nama_tagihan} Rp {nominal}. Link: {link_portal}";

        $created = 0;
        $skipped = 0;
        $enqueued = 0;

        // Base URL untuk portal ortu
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        while ($s = mysqli_fetch_assoc($q_siswa)) {
            $nis = $s['nis'];

            // Cek apakah tagihan jenis ini sudah dibuat untuk siswa ini
            $stmt_cek = $conn->prepare("SELECT id FROM spp_tagihan WHERE nis=? AND jenis_tagihan_id=?");
            $stmt_cek->bind_param("si", $nis, $jenis_tagihan_id);
            $stmt_cek->execute();
            if ($stmt_cek->get_result()->num_rows > 0) {
                $skipped++;
                continue;
            }

            // Generate Token Unik Ortu (32 byte hex = 64 karakter)
            $token = bin2hex(random_bytes(32));

            // Simpan tagihan siswa
            $nominal = $jt['nominal'];
            $sisa = $nominal;
            $status = 'Belum Bayar';

            $stmt_ins = $conn->prepare("INSERT INTO spp_tagihan (nis, jenis_tagihan_id, nominal, dibayar, sisa, status, token) VALUES (?, ?, ?, 0, ?, ?, ?)");
            $stmt_ins->bind_param("siddss", $nis, $jenis_tagihan_id, $nominal, $sisa, $status, $token);
            if ($stmt_ins->execute()) {
                $created++;
                $tagihan_id = $conn->insert_id;

                // Jika Opsi Pengingat WA aktif dan siswa memiliki no HP
                if ($kirim_wa_aktif && !empty($s['no_hp'])) {
                    $link_portal = $base_url . '/spp_portal.php?token=' . $token;

                    $pesan = str_replace(
                        ['{nama_siswa}', '{kelas}', '{nama_tagihan}', '{nominal}', '{jatuh_tempo}', '{link_portal}'],
                        [$s['nama'], $s['kelas'], $jt['nama'], number_format($nominal, 0, ',', '.'), date('d M Y', strtotime($jt['jatuh_tempo'])), $link_portal],
                        $wa_template
                    );

                    // Normalisasi nomor target
                    $target_hp = preg_replace('/[^0-9]/', '', $s['no_hp']);
                    if (substr($target_hp, 0, 1) === '0') {
                        $target_hp = '62' . substr($target_hp, 1);
                    }

                    // Simpan ke antrean pesan (wa_queue) dengan scheduled_at
                    $stmt_q = $conn->prepare("INSERT INTO wa_queue (nis, tagihan_id, tipe, target, message, status, scheduled_at) VALUES (?, ?, 'spp_tagihan', ?, ?, 'pending', ?)");
                    $stmt_q->bind_param("sisss", $nis, $tagihan_id, $target_hp, $pesan, $scheduled_at);
                    $stmt_q->execute();

                    $enqueued++;
                }
            }
        }

        $pesan_sukses = "Proses Selesai! Berhasil membuat <strong>$created</strong> tagihan baru ($skipped siswa dilewati karena sudah memiliki tagihan ini).";
        if ($enqueued > 0) {
            $pesan_sukses .= "<br><i class='bi bi-whatsapp text-success me-1'></i> Sebanyak <strong>$enqueued</strong> pesan pengingat WhatsApp berhasil dijadwalkan ke antrean (Jadwal: <strong>$label_jadwal</strong>). Antrean akan diproses dengan <strong>jeda aman 30 detik per pesan</strong>.";
        }

        $result_info = [
            'status'   => 'success',
            'pesan'    => $pesan_sukses,
            'enqueued' => $enqueued,
            'opsi'     => $opsi_jadwal
        ];
    }
}

// Option daftar jenis tagihan aktif
$q_jt_list = mysqli_query($conn, "SELECT * FROM spp_jenis_tagihan WHERE aktif=1 ORDER BY id DESC");

// Option daftar kelas
$q_kelas_list = mysqli_query($conn, "SELECT nama_kelas FROM kelas ORDER BY nama_kelas ASC");

// Hitung total antrean SPP pending saat ini
$q_count_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM wa_queue WHERE status = 'pending' AND tipe = 'spp_tagihan'");
$total_pending_now = 0;
if ($q_count_pending && $r_cnt = mysqli_fetch_assoc($q_count_pending)) {
    $total_pending_now = (int)$r_cnt['total'];
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generate Tagihan SPP Massal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 2rem; }
        .schedule-card {
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .schedule-card:hover {
            border-color: #0d6efd !important;
            background-color: #f0f7ff;
        }
        .log-box {
            max-height: 220px;
            overflow-y: auto;
            background: #0f172a;
            color: #38bdf8;
            font-family: 'Courier New', monospace;
            font-size: 0.82rem;
            border-radius: 12px;
            padding: 12px;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-magic me-2 text-primary"></i>Generate Tagihan SPP</h4>
            <p class="text-muted small mb-0">Terbitkan tagihan pembayaran secara otomatis untuk seluruh siswa atau per kelas</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAntreanWa">
                <i class="bi bi-whatsapp me-1"></i> Antrean WA (<span id="top-badge-pending"><?= $total_pending_now ?></span>)
            </button>
            <a href="spp_data_tagihan.php" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Data Tagihan
            </a>
        </div>
    </div>

    <?php if ($result_info): ?>
        <div class="alert alert-<?= $result_info['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show rounded-4 p-3 mb-4 shadow-sm">
            <?= $result_info['pesan'] ?>
            <?php if (!empty($result_info['enqueued'])): ?>
                <div class="mt-3">
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAntreanWa" onclick="startSendingProcess()">
                        <i class="bi bi-send-fill me-1"></i> Buka Antrean & Mulai Kirim Sekarang (Jeda 30 Detik)
                    </button>
                </div>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            <div class="card-custom shadow-sm mb-5">
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold">1. Pilih Master Jenis Tagihan</label>
                        <select name="jenis_tagihan_id" id="jenis_tagihan_select" class="form-select form-select-lg" required onchange="updateJtInfo()">
                            <option value="">-- Pilih Jenis Tagihan --</option>
                            <?php while ($jt_item = mysqli_fetch_assoc($q_jt_list)): ?>
                            <option value="<?= $jt_item['id'] ?>" data-jt="<?= $jt_item['jatuh_tempo'] ?>">
                                <?= xss($jt_item['nama']) ?> — Rp <?= number_format($jt_item['nominal'], 0, ',', '.') ?> (Jatuh Tempo: <?= date('d M Y', strtotime($jt_item['jatuh_tempo'])) ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">2. Sasaran Target Siswa</label>
                        <div class="d-flex gap-3 mb-3 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_siswa" id="t1" value="semua" checked onclick="toggleTarget('semua')">
                                <label class="form-check-label fw-semibold" for="t1">Seluruh Siswa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_siswa" id="t2" value="kelas" onclick="toggleTarget('kelas')">
                                <label class="form-check-label fw-semibold" for="t2">Per Kelas / Lembaga</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_siswa" id="t3" value="nis" onclick="toggleTarget('nis')">
                                <label class="form-check-label fw-semibold" for="t3">Per Siswa Specific (NIS)</label>
                            </div>
                        </div>

                        <!-- Dropdown Kelas -->
                        <div id="target-kelas-box" style="display:none;" class="mb-3">
                            <label class="small text-muted mb-1 fw-bold">Pilih Lembaga / Kelas</label>
                            <select name="kelas" class="form-select">
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($kls = mysqli_fetch_assoc($q_kelas_list)): ?>
                                <option value="<?= xss($kls['nama_kelas']) ?>"><?= xss($kls['nama_kelas']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Input NIS Specific -->
                        <div id="target-nis-box" style="display:none;" class="mb-3">
                            <label class="small text-muted mb-1 fw-bold">Ketik NIS Siswa</label>
                            <input type="text" name="nis" class="form-control" placeholder="Misal: 40012">
                        </div>
                    </div>

                    <!-- 3. PILIH JADWAL PENGINGAT WHATSAPP -->
                    <div class="card border-0 bg-light rounded-4 p-3 mb-4 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0 text-success fs-6">
                                <i class="bi bi-whatsapp me-1"></i> 3. Pilih Jadwal Pengingat WhatsApp Otomatis
                            </label>
                            <div class="form-check form-switch fs-5 mb-0">
                                <input class="form-check-input" type="checkbox" name="kirim_wa" id="kirim_wa_toggle" value="1" checked onchange="toggleWaSection(this.checked)">
                            </div>
                        </div>
                        <p class="small text-muted mb-3">
                            Pilih jadwal pengiriman pesan tagihan ke orang tua murid. Pengiriman diproses dalam antrean dengan <strong>jeda 30 detik per pesan</strong> untuk proteksi anti-spam.
                        </p>

                        <div id="wa-options-wrapper">
                            <div class="row g-2 mb-3">
                                <div class="col-md-6 col-12">
                                    <div class="form-check card p-3 h-100 border-1 schedule-card shadow-xs">
                                        <input class="form-check-input" type="radio" name="opsi_jadwal" id="jadwal_sekarang" value="sekarang" checked onclick="toggleJadwal('sekarang')">
                                        <label class="form-check-label fw-bold d-block" for="jadwal_sekarang">
                                            ⚡ Kirim Sekarang (Antrean Bertahap)
                                            <small class="d-block text-muted fw-normal mt-1">Pesan langsung dimasukkan ke antrean pengiriman hari ini.</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check card p-3 h-100 border-1 schedule-card shadow-xs">
                                        <input class="form-check-input" type="radio" name="opsi_jadwal" id="jadwal_custom" value="custom" onclick="toggleJadwal('custom')">
                                        <label class="form-check-label fw-bold d-block" for="jadwal_custom">
                                            🗓️ Tentukan Tanggal & Jam Sendiri
                                            <small class="d-block text-muted fw-normal mt-1">Pilih tanggal dan jam pengiriman sesuai kebutuhan Anda.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Custom Datetime Picker -->
                            <div id="box-custom-datetime" style="display: none;" class="p-3 bg-white rounded-3 border border-primary mb-3">
                                <label class="form-label small fw-bold text-primary mb-1">
                                    <i class="bi bi-calendar-event me-1"></i> Tentukan Tanggal & Jam Pengiriman:
                                </label>
                                <input type="datetime-local" name="custom_datetime" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+1 day 08:00')) ?>">
                                <small class="text-muted">Pesan akan mulai dikirim pada waktu yang ditentukan.</small>
                            </div>

                            <div class="alert alert-info py-2 px-3 mb-0 small d-flex align-items-center rounded-3">
                                <i class="bi bi-shield-check fs-5 me-2 text-primary"></i>
                                <div>
                                    <strong>Proteksi Anti-Spam Aktif:</strong> Setiap pesan WhatsApp akan dikirim bertahap satu per satu dengan jeda aman <strong>30 detik</strong> agar terhindar dari pemblokiran.
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="generate" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm" onclick="return confirm('Apakah Anda yakin ingin memproses generate tagihan ini?')">
                        <i class="bi bi-play-circle-fill me-2"></i> Proses Generate Tagihan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ANTREAN WHATSAPP & REAL-TIME SENDER -->
<div class="modal fade" id="modalAntreanWa" tabindex="-1" aria-labelledby="modalAntreanWaLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-0 py-3 px-4 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="modalAntreanWaLabel">
                    <i class="bi bi-whatsapp text-success me-2"></i> Pengiriman Antrean WhatsApp SPP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopSendingProcess()"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted small">Total Pesan Tertunda:</span>
                        <h4 class="fw-bold text-primary mb-0" id="modal-pending-count"><?= $total_pending_now ?> Pesan</h4>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            <i class="bi bi-hourglass-split text-warning me-1"></i> Jeda Pengiriman: <strong>30 Detik</strong>
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span id="progress-status-text">Siap diproses</span>
                        <span id="progress-percent-text">0%</span>
                    </div>
                    <div class="progress" style="height: 14px; border-radius: 10px;">
                        <div id="progress-bar-fill" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Countdown Box -->
                <div id="countdown-box" style="display: none;" class="alert alert-warning py-2 px-3 mb-3 small d-flex justify-content-between align-items-center rounded-3">
                    <div>
                        <i class="bi bi-clock-history me-1"></i> <strong>Jeda Anti-Spam:</strong> Mengirim pesan berikutnya dalam...
                    </div>
                    <div class="badge bg-danger fs-6 px-3 py-1" id="countdown-timer">30 detik</div>
                </div>

                <!-- Terminal Log Box -->
                <label class="form-label small fw-bold text-muted mb-1">Log Proses Pengiriman Real-Time:</label>
                <div class="log-box mb-3" id="log-console">
                    [System] Siap memproses antrean pesan WhatsApp...<br>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" id="btn-start-send" class="btn btn-success rounded-pill px-4 fw-bold" onclick="startSendingProcess()">
                        <i class="bi bi-play-fill me-1"></i> Mulai Kirim Antrean
                    </button>
                    <button type="button" id="btn-pause-send" class="btn btn-warning rounded-pill px-4 fw-bold" style="display:none;" onclick="pauseSendingProcess()">
                        <i class="bi bi-pause-fill me-1"></i> Jeda (Pause)
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal" onclick="stopSendingProcess()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTarget(val) {
    document.getElementById('target-kelas-box').style.display = (val === 'kelas') ? 'block' : 'none';
    document.getElementById('target-nis-box').style.display = (val === 'nis') ? 'block' : 'none';
}

function toggleJadwal(val) {
    document.getElementById('box-custom-datetime').style.display = (val === 'custom') ? 'block' : 'none';
}

function toggleWaSection(isChecked) {
    document.getElementById('wa-options-wrapper').style.display = isChecked ? 'block' : 'none';
}

// =========================================================================
// SISTEM PENGIRIMAN ANTREAN REAL-TIME DENGAN JEDA 30 DETIK (ANTI-SPAM)
// =========================================================================
let isRunning = false;
let isPaused = false;
let totalInitial = 0;
let sentCount = 0;
let countdownInterval = null;

function logMessage(text, color = '#38bdf8') {
    const consoleBox = document.getElementById('log-console');
    const time = new Date().toLocaleTimeString();
    consoleBox.innerHTML += `<span style="color:${color}">[${time}] ${text}</span><br>`;
    consoleBox.scrollTop = consoleBox.scrollHeight;
}

async function updatePendingCount() {
    try {
        const res = await fetch('spp_ajax_kirim_wa.php?action=count');
        const data = await res.json();
        if (data.success) {
            document.getElementById('modal-pending-count').innerText = data.total + ' Pesan';
            document.getElementById('top-badge-pending').innerText = data.total;
            return data.total;
        }
    } catch(e) {}
    return 0;
}

async function startSendingProcess() {
    if (isRunning) return;
    isRunning = true;
    isPaused = false;

    document.getElementById('btn-start-send').style.display = 'none';
    document.getElementById('btn-pause-send').style.display = 'inline-block';
    
    totalInitial = await updatePendingCount();
    if (totalInitial === 0) {
        logMessage('Tidak ada antrean pesan SPP yang perlu dikirim.', '#a7f3d0');
        stopSendingProcess();
        return;
    }

    logMessage(`Memulai proses pengiriman antrean (${totalInitial} pesan). Jeda antar pesan: 30 detik.`, '#fde047');
    sentCount = 0;
    processNextItem();
}

function pauseSendingProcess() {
    isPaused = true;
    isRunning = false;
    clearInterval(countdownInterval);
    document.getElementById('countdown-box').style.display = 'none';
    document.getElementById('btn-start-send').style.display = 'inline-block';
    document.getElementById('btn-start-send').innerHTML = '<i class="bi bi-play-fill me-1"></i> Lanjutkan';
    document.getElementById('btn-pause-send').style.display = 'none';
    logMessage('Pengiriman dijeda oleh pengguna.', '#fca5a5');
}

function stopSendingProcess() {
    isRunning = false;
    isPaused = false;
    clearInterval(countdownInterval);
    document.getElementById('countdown-box').style.display = 'none';
    document.getElementById('btn-start-send').style.display = 'inline-block';
    document.getElementById('btn-start-send').innerHTML = '<i class="bi bi-play-fill me-1"></i> Mulai Kirim Antrean';
    document.getElementById('btn-pause-send').style.display = 'none';
}

async function processNextItem() {
    if (!isRunning || isPaused) return;

    // Ambil item berikutnya
    try {
        const checkRes = await fetch('spp_ajax_kirim_wa.php?action=get_next');
        const checkData = await checkRes.json();

        if (!checkData.success || !checkData.has_item) {
            logMessage('Semua antrean pesan telah berhasil dikirim!', '#4ade80');
            document.getElementById('progress-status-text').innerText = 'Selesai!';
            document.getElementById('progress-percent-text').innerText = '100%';
            document.getElementById('progress-bar-fill').style.width = '100%';
            await updatePendingCount();
            stopSendingProcess();
            return;
        }

        const item = checkData.item;
        logMessage(`Mengirim ke ${item.nama_siswa} (${item.target})...`, '#e2e8f0');

        // Kirim via AJAX endpoint
        const formData = new FormData();
        formData.append('action', 'send_item');
        formData.append('queue_id', item.id);

        const sendRes = await fetch('spp_ajax_kirim_wa.php?action=send_item', {
            method: 'POST',
            body: formData
        });
        const sendData = await sendRes.json();

        if (sendData.success && sendData.is_sent) {
            sentCount++;
            logMessage(`Pesan ke ${item.nama_siswa} (${item.target}) TERKIRIM. Status: ${sendData.status}`, '#4ade80');
        } else {
            logMessage(`Pesan ke ${item.nama_siswa} (${item.target}) GAGAL: ${sendData.error || sendData.status}`, '#f87171');
        }

        // Update progress bar
        const remaining = sendData.remaining ?? 0;
        const totalProcessed = sentCount;
        const totalOverall = totalProcessed + remaining;
        const pct = totalOverall > 0 ? Math.round((totalProcessed / totalOverall) * 100) : 100;

        document.getElementById('progress-status-text').innerText = `Terkirim ${totalProcessed} dari ${totalOverall} pesan`;
        document.getElementById('progress-percent-text').innerText = pct + '%';
        document.getElementById('progress-bar-fill').style.width = pct + '%';
        document.getElementById('modal-pending-count').innerText = remaining + ' Pesan';
        document.getElementById('top-badge-pending').innerText = remaining;

        if (remaining <= 0) {
            logMessage('Semua antrean pesan telah selesai diproses!', '#4ade80');
            stopSendingProcess();
            return;
        }

        // Mulai Countdown Jeda 30 Detik
        let secondsLeft = 30;
        document.getElementById('countdown-box').style.display = 'flex';
        document.getElementById('countdown-timer').innerText = secondsLeft + ' detik';
        logMessage(`Menunggu jeda 30 detik anti-spam sebelum pesan berikutnya...`, '#94a3b8');

        countdownInterval = setInterval(() => {
            if (!isRunning || isPaused) {
                clearInterval(countdownInterval);
                return;
            }
            secondsLeft--;
            document.getElementById('countdown-timer').innerText = secondsLeft + ' detik';

            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                document.getElementById('countdown-box').style.display = 'none';
                processNextItem();
            }
        }, 1000);

    } catch(err) {
        logMessage(`Terjadi kendala koneksi: ${err.message}`, '#f87171');
        pauseSendingProcess();
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
