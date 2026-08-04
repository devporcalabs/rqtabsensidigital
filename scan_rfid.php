<?php
session_start();
include 'koneksi.php';

// --- SECURITY ENGINE ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

if(!isset($_SESSION['login'])){ header("location: login.php"); exit; }

// Ambil Pengaturan
$stmt_set = $conn->prepare("SELECT * FROM pengaturan WHERE id = 1");
$stmt_set->execute();
$sett = $stmt_set->get_result()->fetch_assoc();

$nama_sekolah = $sett['nama_sekolah'] ?? "Porcalabs School";
$logo = $sett['logo_sekolah'] ?? "porcalabs.ico";
$timezone_aktif = $sett['timezone'] ?? 'Asia/Jakarta';

include 'header.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi RFID - <?= xss($nama_sekolah) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Hide global Left Sidebar menu and reset body margins for kiosk view */
        body {
            margin-left: 0 !important;
            padding: 1.5rem !important;
            background-color: #f8fafc !important;
            overflow: hidden !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .sidebar-left {
            display: none !important;
        }

        /* Responsive Kiosk Cards */
        .kiosk-card {
            min-height: 480px;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        @media (min-width: 768px) {
            .kiosk-card {
                height: calc(100vh - 200px);
            }
        }
        @media (max-width: 767.98px) {
            body {
                overflow-y: auto !important;
            }
            .kiosk-card {
                min-height: 400px;
                height: auto !important;
            }
        }

        /* Responsive Clock Box */
        .clock-box {
            width: 90px;
            height: 90px;
        }
        @media (max-width: 991.98px) {
            .navbar-custom {
                display: none !important;
            }
            body {
                padding: 1rem !important;
            }
        }
        @media (max-width: 575.98px) {
            .clock-box {
                width: 70px !important;
                height: 70px !important;
            }
            .clock-box span {
                font-size: 2.25rem !important;
            }
        }

        /* Kiosk RFID Box wave styling */
        .rfid-box-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .rfid-wave-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        .rfid-wave-circle i {
            font-size: 2.2rem;
            color: #ffffff;
            z-index: 2;
        }
        .rfid-wave-circle .wave {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #3b82f6;
            opacity: 0.4;
            animation: rfid-pulse 2s infinite;
            z-index: 1;
        }
        @keyframes rfid-pulse {
            100% {
                transform: scale(1.6);
                opacity: 0;
            }
        }

        /* Custom scrollbar for latest logs */
        #log-container::-webkit-scrollbar {
            width: 0px;
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <!-- Top Header Card -->
    <div class="glass-card p-4 mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h3 class="fw-extrabold text-dark mb-1" style="font-weight: 800; letter-spacing: -0.5px;">Absensi RFID</h3>
            <p class="text-muted small mb-0">Tempelkan kartu siswa pada RFID reader untuk mencatat kehadiran.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 btn-sm fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
            <button id="toggle-suara" class="btn btn-outline-primary rounded-pill px-4 py-2 btn-sm fw-bold">
                <i id="icon-suara" class="bi bi-volume-up-fill me-1"></i> Suara Aktif
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="row g-4">
        <!-- Left Column: Kiosk Time & RFID Wave -->
        <div class="col-md-7">
            <div class="glass-card p-5 kiosk-card justify-content-center align-items-center">
                <!-- School Logo & Name -->
                <div class="mb-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="p-3 bg-light rounded-4 mb-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <img src="img/<?= xss($logo) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        <h6 class="fw-bold text-muted text-uppercase mb-0" style="letter-spacing: 2px; font-size: 0.8rem;"><?= xss($nama_sekolah) ?></h6>
                    </div>
                </div>

                <!-- Clock Section -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-dark text-white rounded-4 d-flex align-items-center justify-content-center shadow-lg clock-box">
                        <span class="fw-bold" id="h" style="font-size: 2.75rem;">00</span>
                    </div>
                    <span class="fs-2 fw-bold text-muted">:</span>
                    <div class="bg-dark text-white rounded-4 d-flex align-items-center justify-content-center shadow-lg clock-box">
                        <span class="fw-bold" id="m" style="font-size: 2.75rem;">00</span>
                    </div>
                    <span class="fs-2 fw-bold text-muted">:</span>
                    <div class="bg-dark text-white rounded-4 d-flex align-items-center justify-content-center shadow-lg clock-box">
                        <span class="fw-bold" id="s" style="font-size: 2.75rem;">00</span>
                    </div>
                </div>

                <!-- Date display -->
                <div id="digital-date" class="fw-bold text-muted text-uppercase small mb-5" style="letter-spacing: 1.5px;">Memuat...</div>

                <!-- RFID Pulse Wave -->
                <div class="rfid-box-container mb-3">
                    <div class="rfid-wave-circle">
                        <i class="bi bi-broadcast"></i>
                        <div class="wave"></div>
                    </div>
                </div>

                <h4 class="fw-extrabold mt-3 text-dark mb-1" style="font-weight: 800;">Silakan Tap Kartu</h4>
                <p class="text-muted small fw-semibold">Scan RFID System • Secure Attendance</p>

                <!-- Hidden Input form for scanner -->
                <form id="form-rfid" class="m-0">
                    <input type="text" id="rfid-input" name="nis" autofocus autocomplete="off" style="position: absolute; opacity: 0; pointer-events: none;">
                </form>
            </div>
        </div>

        <!-- Right Column: 5 Latest Logs -->
        <div class="col-md-5">
            <div class="glass-card p-4 kiosk-card align-items-stretch" style="justify-content: flex-start;">
                <div class="mb-4 text-start">
                    <h5 class="fw-bold text-dark mb-1">5 Absen Terakhir</h5>
                    <p class="text-muted small mb-0">Aktivitas kehadiran terbaru hari ini.</p>
                </div>

                <div id="log-container" class="flex-grow-1 overflow-y-auto" style="scrollbar-width: none;">
                    <!-- Loaded dynamically via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Processing Modal -->
<div class="modal fade" id="modalAbsen" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 32px;">
            <div class="modal-body p-5 text-center">
                <div id="loading-spinner">
                    <div class="spinner-border text-primary mb-3" style="width: 3.5rem; height: 3.5rem;"></div>
                    <h5 class="fw-bold">MEMPROSES...</h5>
                </div>
                <div id="result-content" style="display:none;">
                    <div id="m-foto-container" class="mb-4"></div>
                    <h2 id="m-nama" class="fw-bold mb-1"></h2>
                    <div id="m-kelas" class="badge bg-primary px-4 py-2 rounded-pill mb-4 fw-bold"></div>
                    <div class="alert py-3 fw-bold rounded-4" id="m-pesan"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<audio id="snd-success" src="https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3"></audio>
<audio id="snd-fail" src="https://assets.mixkit.co/sfx/preview/mixkit-wrong-answer-fail-notification-946.mp3"></audio>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // --- SUARA SPEECH SYNTHESIS SETTINGS ---
    let isSoundOn = localStorage.getItem('kiosk-sound') !== 'off';
    const btnSuara = document.getElementById('toggle-suara');
    const iconSuara = document.getElementById('icon-suara');

    function updateSoundUI() {
        if(isSoundOn) {
            btnSuara.classList.add('btn-primary');
            btnSuara.classList.remove('btn-outline-primary');
            btnSuara.innerHTML = '<i id="icon-suara" class="bi bi-volume-up-fill me-1"></i> Suara Aktif';
        } else {
            btnSuara.classList.remove('btn-primary');
            btnSuara.classList.add('btn-outline-primary');
            btnSuara.innerHTML = '<i id="icon-suara" class="bi bi-volume-mute-fill me-1"></i> Suara Mati';
        }
    }
    updateSoundUI();

    btnSuara.addEventListener('click', () => {
        isSoundOn = !isSoundOn;
        localStorage.setItem('kiosk-sound', isSoundOn ? 'on' : 'off');
        updateSoundUI();
    });

    // Pancing list suara bahasa indonesia
    if (typeof window.speechSynthesis !== 'undefined') {
        window.speechSynthesis.getVoices();
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
        }
    }

    function getNamaBelakang(nama) {
        if (!nama) return "";
        let parts = nama.trim().split(/\s+/);
        return parts[parts.length - 1];
    }

    function bicara(teks) {
        if (!isSoundOn) return;
        window.speechSynthesis.cancel();
        const msg = new SpeechSynthesisUtterance(teks);
        msg.rate = 1.0;
        
        const voices = window.speechSynthesis.getVoices();
        const voiceEn = voices.find(v => v.lang.startsWith('en-') || v.lang.startsWith('en_') || v.name.toLowerCase().includes('english'));
        const voiceId = voices.find(v => v.lang.startsWith('id-') || v.lang.startsWith('id_') || v.name.toLowerCase().includes('indonesia'));
        
        if (voiceEn) {
            msg.voice = voiceEn;
            msg.lang = voiceEn.lang;
        } else if (voiceId) {
            msg.voice = voiceId;
            msg.lang = voiceId.lang;
        } else {
            msg.lang = 'en-US';
        }
        window.speechSynthesis.speak(msg);
    }

    // --- LOGIKA JAM & TANGGAL ---
    function updateClock() {
        const tz = '<?= $timezone_aktif ?>';
        const now = new Date();
        const options = { timeZone: tz, hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        const parts = new Intl.DateTimeFormat('id-ID', options).formatToParts(now);
        let h, m, s;
        parts.forEach(p => { 
            if(p.type==='hour') h=p.value; 
            if(p.type==='minute') m=p.value; 
            if(p.type==='second') s=p.value; 
        });
        $('#h').text(h); $('#m').text(m); $('#s').text(s);
        $('#digital-date').text(now.toLocaleDateString('id-ID', { timeZone: tz, weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }));
    }
    setInterval(updateClock, 1000); updateClock();

    function loadLastAbsensi() {
        $.get('get_las_absensi_rfid.php', (data) => $('#log-container').html(data));
    }
    loadLastAbsensi();

    // Auto Focus RFID Scanner Input
    const rfidInput = document.getElementById('rfid-input');
    const modalAbsen = new bootstrap.Modal(document.getElementById('modalAbsen'));
    let isProcessing = false;

    document.addEventListener('click', () => { if(!isProcessing) rfidInput.focus(); });
    setInterval(() => { if(!$('#modalAbsen').hasClass('show') && !isProcessing) rfidInput.focus(); }, 1000);

    // Form submit AJAX processing
    $('#form-rfid').on('submit', function(e) {
        e.preventDefault();
        const val = rfidInput.value.trim();
        if(val == "" || isProcessing) return;

        isProcessing = true;
        $('#loading-spinner').show(); $('#result-content').hide(); modalAbsen.show();

        $.ajax({
            url: 'proses_absen.php',
            type: 'POST',
            data: { nis: val },
            success: function(response) {
                try {
                    const d = typeof response === 'object' ? response : JSON.parse(response);
                    $('#m-nama').text(d.nama || "Siswa");
                    $('#m-kelas').text(d.kelas || "-");
                    
                    if(d.foto && d.foto !== ""){
                        $('#m-foto-container').html('<img src="img/siswa/'+d.foto+'" class="rounded-circle border border-5 border-white shadow-lg" style="width:160px; height:160px; object-fit:cover;">');
                    } else {
                        $('#m-foto-container').html('<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow-lg" style="width:160px; height:160px; font-size: 5rem; color: #cbd5e1;"><i class="bi bi-person-fill"></i></div>');
                    }

                    $('#m-pesan').text(d.pesan).removeClass('alert-success alert-danger alert-warning');
                    
                    const namaBelakang = getNamaBelakang(d.nama);

                    if(d.status === 'success') {
                        $('#m-pesan').addClass('alert-success');
                        if(isSoundOn) document.getElementById('snd-success').play();
                        if (d.tipe_absen === 'pulang') {
                            bicara("Goodbye " + namaBelakang);
                        } else if (d.status_telat === 'Terlambat') {
                            bicara("Your Late " + namaBelakang);
                        } else {
                            bicara("Welcome " + namaBelakang);
                        }
                    } else if(d.status === 'warning') {
                        $('#m-pesan').addClass('alert-warning');
                        if(isSoundOn) document.getElementById('snd-fail').play();
                        bicara("You Already Checkin");
                    } else {
                        $('#m-pesan').addClass('alert-danger');
                        if(isSoundOn) document.getElementById('snd-fail').play();
                        bicara("Attendance Failed");
                    }

                    $('#loading-spinner').hide(); $('#result-content').fadeIn();
                    loadLastAbsensi();

                    setTimeout(() => { 
                        modalAbsen.hide(); 
                        rfidInput.value = ""; 
                        isProcessing = false; 
                    }, 2000);

                } catch (err) { modalAbsen.hide(); isProcessing = false; rfidInput.value = ""; }
            },
            error: function() { modalAbsen.hide(); isProcessing = false; rfidInput.value = ""; }
        });
    });
</script>

</body>
</html>