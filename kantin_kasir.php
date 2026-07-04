<?php
session_start();
include 'koneksi.php';

// 1. Validasi Sesi Strict
if (!isset($_SESSION['login'])) {
    header("location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin' && $role !== 'kantin') {
    die("Akses Ditolak! Anda tidak memiliki izin untuk mengakses modul E-Kantin.");
}

// Security CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir E-Kantin - <?= xss($data['nama_sekolah'] ?? 'Rumah Quran Temi') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f6fc;
            background-image: radial-gradient(at 0% 0%, rgba(13, 110, 253, 0.05) 0px, transparent 50%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.03);
        }

        .canteen-title {
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        /* DISPLAY NOMINAL */
        .nominal-display {
            background: #0f172a;
            color: #10b981;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            font-size: 2.5rem;
            text-align: right;
            border-radius: 18px;
            padding: 15px 25px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
            letter-spacing: 1px;
            border: 2px solid #1e293b;
        }

        /* KEYPAD */
        .key-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 15px;
            padding: 18px 0;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        }
        .key-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }
        .key-btn:active {
            transform: translateY(0);
        }
        .key-btn.btn-action {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }
        .key-btn.btn-action:hover {
            background: #dc2626;
        }
        .key-btn.btn-success-action {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        .key-btn.btn-success-action:hover {
            background: #0b5ed7;
        }

        /* STATUS TAP */
        .tap-card {
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            color: white;
            border-radius: 20px;
            border: none;
            padding: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
        }
        .tap-card::before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        .pulse-icon {
            font-size: 3rem;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 1; text-shadow: 0 0 15px rgba(255,255,255,0.6); }
            100% { transform: scale(1); opacity: 0.8; }
        }

        /* TRANSACTION FEED */
        .feed-item {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 18px;
            padding: 12px 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }
        .feed-item:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02);
        }
        .feed-avatar {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        .feed-avatar-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* HIDDEN RFID FIELD */
        #rfid_field {
            position: absolute;
            opacity: 0;
            top: 0; left: 0;
            width: 1px; height: 1px;
        }
    </style>
</head>
<body>

    <!-- Input tersembunyi untuk scan RFID secara otomatis -->
    <input type="text" id="rfid_field" autofocus autocomplete="off" inputmode="none">

    <div class="container py-4" style="margin-top: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="canteen-title mb-1"><i class="bi bi-shop me-2 text-primary"></i>KASIR E-KANTIN</h3>
                <p class="text-muted small mb-0">Kelola pembayaran jajan siswa dengan tap kartu RFID secara instan</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button id="sound-btn" class="btn btn-outline-secondary rounded-pill px-3 fw-bold btn-sm">
                    <i id="sound-icon" class="bi bi-volume-up-fill me-1"></i> Suara On
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- PANEL KASIR (INPUT & KEYPAD) -->
            <div class="col-lg-7">
                <div class="glass-card p-4">
                    <div class="mb-4">
                        <label class="small fw-bold text-muted mb-2"><i class="bi bi-calculator me-1"></i> NOMINAL PEMBAYARAN (Rp)</label>
                        <div id="display-nominal" class="nominal-display">0</div>
                    </div>

                    <!-- KEYPAD GRID -->
                    <div class="row g-2 mb-4">
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('1')">1</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('2')">2</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('3')">3</button></div>
                        
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('4')">4</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('5')">5</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('6')">6</button></div>
                        
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('7')">7</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('8')">8</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('9')">9</button></div>
                        
                        <div class="col-4"><button class="btn key-btn w-100 btn-action" onclick="clearNominal()">C</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('0')">0</button></div>
                        <div class="col-4"><button class="btn key-btn w-100" onclick="pressKey('000')">.000</button></div>
                    </div>

                    <!-- RFID TAP AREA -->
                    <div class="tap-card">
                        <div class="mb-2"><i class="bi bi-wifi pulse-icon"></i></div>
                        <h5 class="fw-bold mb-1">SIAP PINDAI KARTU PELAJAR</h5>
                        <p class="small opacity-75 mb-0" id="tap-instruction">Ketik nominal di atas lalu tap kartu RFID siswa pada pembaca kartu</p>
                    </div>
                </div>
            </div>

            <!-- PANEL RIWAYAT TRANSAKSI TERAKHIR -->
            <div class="col-lg-5">
                <div class="glass-card p-4 h-100 d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-clock-history text-primary me-2"></i> 5 TRANSAKSI TERAKHIR</h5>
                    <div id="kantin-feed-container" class="flex-grow-1" style="min-height: 350px;">
                        <!-- Akan diisi otomatis oleh AJAX -->
                        <div class="text-center py-5 text-muted small">Memuat riwayat transaksi...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL STATUS TRANSAKSI -->
    <div class="modal fade" id="modalResult" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 25px; overflow:hidden;">
                <div id="modal-header-color" class="p-3 text-center fw-bold text-white fs-5">STATUS TRANSAKSI</div>
                <div class="modal-body text-center p-5">
                    <div id="modal-photo-box" class="mx-auto mb-3" style="width:140px; height:140px; border-radius:20px; overflow:hidden; border:4px solid #eee; display:none;">
                         <img id="modal-photo" src="" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div id="modal-icon-placeholder" class="mb-3 text-muted">
                        <i class="bi bi-person-circle" style="font-size: 80px;"></i>
                    </div>

                    <h3 id="modal-nama" class="fw-bold text-primary">NAMA SISWA</h3>
                    <p id="modal-kelas" class="fw-bold text-muted small mb-4">KELAS SISWA</p>
                    
                    <div id="modal-pesan" class="fw-bold fs-4 p-3 rounded-4 bg-light text-dark mb-3">Pesan status transaksi</div>
                    
                    <div class="row g-2">
                        <div class="col-6 bg-light rounded-3 p-2">
                            <small class="text-muted d-block small">Belanja</small>
                            <span class="fw-bold text-danger fs-5" id="modal-belanja">Rp 0</span>
                        </div>
                        <div class="col-6 bg-light rounded-3 p-2">
                            <small class="text-muted d-block small">Sisa Saldo</small>
                            <span class="fw-bold text-success fs-5" id="modal-saldo">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AUDIO FILES -->
    <audio id="sound-success" src="https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3"></audio>
    <audio id="sound-error" src="https://assets.mixkit.co/sfx/preview/mixkit-wrong-answer-fail-notification-946.mp3"></audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalResult = new bootstrap.Modal(document.getElementById('modalResult'));
        const rfidInput = document.getElementById('rfid_field');
        let currentNominal = "";
        let isProcessing = false;
        let isSoundEnabled = localStorage.getItem('kantin-sound') !== 'off';

        // --- SOUND MANAGER ---
        const soundBtn = document.getElementById('sound-btn');
        const soundIcon = document.getElementById('sound-icon');
        
        function updateSoundUI() {
            soundIcon.className = isSoundEnabled ? 'bi bi-volume-up-fill me-1' : 'bi bi-volume-mute-fill me-1';
            soundBtn.textContent = isSoundEnabled ? "Suara On" : "Suara Off";
            soundBtn.prepend(soundIcon);
            isSoundEnabled ? soundBtn.classList.add('btn-primary') : soundBtn.classList.remove('btn-primary');
        }

        soundBtn.addEventListener('click', () => {
            isSoundEnabled = !isSoundEnabled;
            localStorage.setItem('kantin-sound', isSoundEnabled ? 'on' : 'off');
            updateSoundUI();
        });
        updateSoundUI();

        // Pancing browser untuk memuat list suara di awal
        if (typeof window.speechSynthesis !== 'undefined') {
            window.speechSynthesis.getVoices();
            if (window.speechSynthesis.onvoiceschanged !== undefined) {
                window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
            }
        }

        function suaraPemberitahuan(teks) {
            if (!isSoundEnabled) return;
            window.speechSynthesis.cancel();
            const ucapan = new SpeechSynthesisUtterance(teks);
            ucapan.lang = 'en-US';
            
            // Cari suara bahasa Inggris
            const voices = window.speechSynthesis.getVoices();
            const suaraEng = voices.find(v => v.lang === 'en-US' || v.lang.startsWith('en-') || v.lang.startsWith('en_') || v.name.toLowerCase().includes('english'));
            if (suaraEng) {
                ucapan.voice = suaraEng;
            }
            
            window.speechSynthesis.speak(ucapan);
        }

        // --- KEYPAD ENGINE ---
        function updateDisplay() {
            const nominal = parseInt(currentNominal || "0");
            document.getElementById('display-nominal').textContent = formatRupiah(nominal);
        }

        // Handle numeric key presses
        function pressKey(key) {
            if (currentNominal.length >= 8) return; // Batasan nominal belanja max Rp 99.999.999
            currentNominal += key;
            updateDisplay();
        }

        function clearNominal() {
            currentNominal = "";
            updateDisplay();
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        }

        // --- AUTOFOCUS ENGINE ---
        function focusRFID() {
            if (!$('#modalResult').hasClass('show')) {
                rfidInput.focus();
            }
        }

        // --- TRANSAKSI PROSES ---
        function prosesTransaksi(rfid_uid) {
            const nominal = parseInt(currentNominal || "0");
            if (nominal <= 0) {
                suaraPemberitahuan("Please enter the purchase amount first.");
                alert("Nominal belanja harus lebih dari Rp 0!");
                focusRFID();
                return;
            }

            isProcessing = true;
            document.getElementById('tap-instruction').textContent = "Sedang memproses transaksi...";
            
            $.post('kantin_proses.php', {
                rfid_uid: rfid_uid,
                nominal: nominal,
                csrf_token: '<?= $_SESSION['csrf_token'] ?>'
            }, function(res) {
                let d;
                if (typeof res === 'object') {
                    d = res;
                } else {
                    try {
                        d = JSON.parse(res);
                    } catch(e) {
                        d = { status: 'error', pesan: 'Gagal membaca respons server!' };
                    }
                }

                // Tampilkan data ke modal
                $('#modal-nama').text(d.nama || "Kartu Tidak Dikenal");
                $('#modal-kelas').text(d.kelas || "-");
                $('#modal-pesan').text(d.pesan);
                $('#modal-belanja').text(formatRupiah(nominal));
                $('#modal-saldo').text(formatRupiah(d.saldo_akhir || 0));

                if (d.foto) {
                    $('#modal-photo').attr('src', 'img/siswa/' + d.foto);
                    $('#modal-photo-box').show();
                    $('#modal-icon-placeholder').hide();
                } else {
                    $('#modal-photo-box').hide();
                    $('#modal-icon-placeholder').show();
                }

                if (d.status === 'success') {
                    $('#modal-header-color').text("TRANSAKSI BERHASIL").css('background', '#10b981');
                    document.getElementById('sound-success').play().catch(() => {});
                    suaraPemberitahuan("Payment successful. Thank you, " + d.nama);
                    clearNominal();
                    updateFeed();
                } else {
                    $('#modal-header-color').text("TRANSAKSI GAGAL").css('background', '#ef4444');
                    document.getElementById('sound-error').play().catch(() => {});
                    
                    let errorMsg = "Transaction failed.";
                    if (d.pesan && (d.pesan.toLowerCase().includes("saldo") || d.pesan.toLowerCase().includes("mencukupi"))) {
                        errorMsg = "Transaction failed. Insufficient balance.";
                    } else if (d.pesan && d.pesan.toLowerCase().includes("tidak terdaftar")) {
                        errorMsg = "Transaction failed. Card not registered.";
                    }
                    suaraPemberitahuan(errorMsg);
                }

                modalResult.show();
                
                // Tutup modal otomatis setelah 3 detik
                setTimeout(() => {
                    modalResult.hide();
                    isProcessing = false;
                    document.getElementById('tap-instruction').textContent = "Ketik nominal di atas lalu tap kartu RFID siswa pada pembaca kartu";
                    focusRFID();
                }, 3000);
            }).fail(function() {
                alert("Gagal menghubungi server.");
                isProcessing = false;
                document.getElementById('tap-instruction').textContent = "Ketik nominal di atas lalu tap kartu RFID siswa pada pembaca kartu";
                focusRFID();
            });
        }

        // --- UPDATE FEED ---
        function updateFeed() {
            $.get('kantin_feed.php', function(data) {
                $('#kantin-feed-container').html(data);
            });
        }

        $(document).ready(() => {
            updateDisplay();
            updateFeed();
            setInterval(focusRFID, 1000);

            // Fokus rfid_field saat halaman di klik di mana saja
            document.addEventListener('click', () => {
                focusRFID();
            });

            rfidInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !isProcessing) {
                    const code = rfidInput.value.trim();
                    rfidInput.value = '';
                    if (code !== '') {
                        prosesTransaksi(code);
                    }
                }
            });
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>
