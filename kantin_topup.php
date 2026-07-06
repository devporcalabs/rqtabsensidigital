<?php
session_start();
include 'koneksi.php';

// 1. Validasi Login & Role
if (!isset($_SESSION['login'])) {
    header("location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    die("Akses Ditolak! Fitur Top Up Saldo hanya tersedia untuk Administrator.");
}

// Security CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function xss($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// Cek apakah ada parameter NIS dari quick topup data_siswa.php
$preload_nis = trim($_GET['nis'] ?? '');

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Top Up Saldo E-Kantin - <?= xss($data['nama_sekolah'] ?? 'Rumah Quran Temi') ?></title>
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

        .section-title {
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .student-profile-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .student-photo {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            object-fit: cover;
            border: 3px solid #0d6efd;
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.1);
        }

        .student-photo-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border: 1px solid #e2e8f0;
        }

        .topup-presets .preset-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 700;
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            width: 100%;
        }
        .topup-presets .preset-btn:hover {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.15);
        }
        .topup-presets .preset-btn:active {
            transform: translateY(0);
        }

        /* NOMINAL INPUT */
        .nominal-input {
            font-weight: 800;
            font-size: 1.75rem;
            color: #0d6efd;
            text-align: center;
            border-radius: 15px;
            padding: 10px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.2s;
        }
        .nominal-input:focus {
            border-color: #0d6efd;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
            outline: none;
        }
    </style>
</head>
<body>

    <div class="container py-4" style="margin-top: 20px;">
        <div class="mb-4">
            <h3 class="section-title mb-1"><i class="bi bi-wallet2 me-2 text-primary"></i>TOP UP SALDO E-KANTIN</h3>
            <p class="text-muted small mb-0">Isi ulang saldo kartu jajan siswa dengan mudah & aman</p>
        </div>

        <div class="row g-4">
            <!-- PENCARIAN SISWA & SCAN RFID -->
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-search text-primary me-2"></i> 1. CARI DATA SISWA</h5>
                    <p class="small text-muted mb-4">Tempelkan kartu pelajar RFID pada scanner atau cari berdasarkan NIS siswa</p>
                    
                    <!-- Form Pencarian -->
                    <div class="mb-4">
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-person-vcard text-primary fs-4"></i></span>
                            <input type="text" id="search-input" class="form-control border-0" placeholder="Pindai RFID / Ketik NIS Siswa..." value="<?= xss($preload_nis) ?>">
                            <button id="search-btn" class="btn btn-primary px-4 fw-bold" type="button"><i class="bi bi-arrow-right-short fs-4"></i></button>
                        </div>
                    </div>

                    <!-- CARD PROFILE HASIL CARI -->
                    <div id="student-result-container">
                        <div class="text-center py-5 text-muted small">
                            <i class="bi bi-person-circle fs-1 mb-2 d-block opacity-50"></i>
                            Silakan masukkan NIS atau tap kartu siswa untuk memuat profil.
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL ISI ULANG SALDO -->
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-4"><i class="bi bi-cash-stack text-primary me-2"></i> 2. NOMINAL ISI SALDO</h5>

                    <form id="topup-form" method="POST" action="kantin_topup_proses.php" style="display: none;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="nis" id="topup-nis">

                        <!-- PRESETS NOMINAL -->
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-2">PILIH NOMINAL CEPAT</label>
                            <div class="row g-2 topup-presets">
                                <div class="col-4"><button type="button" class="btn preset-btn" onclick="setPreset(10000)">10.000</button></div>
                                <div class="col-4"><button type="button" class="btn preset-btn" onclick="setPreset(20000)">20.000</button></div>
                                <div class="col-4"><button type="button" class="btn preset-btn" onclick="setPreset(50000)">50.000</button></div>
                                <div class="col-4"><button type="button" class="btn preset-btn" onclick="setPreset(100000)">100.000</button></div>
                                <div class="col-4"><button type="button" class="btn preset-btn" onclick="setPreset(150000)">150.000</button></div>
                                <div class="col-4"><button type="button" class="btn preset-btn" onclick="setPreset(200000)">200.000</button></div>
                            </div>
                        </div>

                        <!-- INPUT NOMINAL BEBAS -->
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-2">NOMINAL LAINNYA (Rp)</label>
                            <input type="number" name="nominal" id="nominal-custom" class="form-control nominal-input" placeholder="Masukkan Nominal..." min="1000" max="1000000" required>
                            <div class="form-text small text-muted text-center mt-2">Minimal pengisian saldo adalah Rp 1.000. Maksimal Rp 1.000.000 per transaksi.</div>
                        </div>

                        <!-- KETERANGAN / MEMO -->
                        <div class="mb-4">
                            <label class="small fw-bold text-muted mb-2">KETERANGAN (OPSIONAL)</label>
                            <input type="text" name="keterangan" class="form-control form-control-lg rounded-3" placeholder="Contoh: Isi saldo awal, Top up cash, dll." maxlength="100">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 rounded-4 shadow"><i class="bi bi-check2-circle me-2"></i>PROSES TOP UP SALDO</button>
                    </form>

                    <div id="form-placeholder" class="text-center py-5 text-muted small h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="bi bi-wallet2 fs-1 mb-2 opacity-50"></i>
                        Formulir pengisian akan aktif setelah profil siswa berhasil dimuat.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set preset nominal
        function setPreset(amount) {
            document.getElementById('nominal-custom').value = amount;
        }

        // Cari profil siswa
        function cariSiswa() {
            const val = $('#search-input').val().trim();
            if (val === '') return;

            $('#student-result-container').html('<div class="text-center py-5 text-muted small"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data...</div>');

            $.get('kantin_cari_siswa.php', { q: val }, function(res) {
                let d;
                if (typeof res === 'object') {
                    d = res;
                } else {
                    try {
                        d = JSON.parse(res);
                    } catch(e) {
                        d = { status: 'error' };
                    }
                }

                if (d.status === 'success') {
                    // Update profil visual
                    const fotoHtml = d.foto ? `<img src="img/siswa/${d.foto}" class="student-photo">` : `<div class="student-photo-placeholder"><i class="bi bi-person"></i></div>`;
                    const profileHtml = `
                        <div class="student-profile-card shadow-sm mt-3">
                            <div class="d-flex align-items-center gap-3">
                                ${fotoHtml}
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">${d.nama}</h5>
                                    <div class="text-muted small mb-2">${d.nis} • <b>Kelas ${d.kelas}</b></div>
                                    <div class="fs-5 fw-bold text-success">
                                        <small class="text-muted text-xs d-block" style="font-size:0.7rem;">Saldo Saat Ini</small>
                                        Rp ${new Intl.NumberFormat('id-ID').format(d.saldo)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#student-result-container').html(profileHtml);
                    
                    // Set parameter form & tampilkan form
                    $('#topup-nis').val(d.nis);
                    $('#form-placeholder').hide();
                    $('#topup-form').slideDown();
                    $('#nominal-custom').focus();
                } else {
                    const pesanError = d.pesan || 'Siswa tidak ditemukan atau kartu RFID tidak terdaftar!';
                    $('#student-result-container').html(`
                        <div class="alert alert-danger rounded-4 mt-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> ${pesanError}
                        </div>
                    `);
                    $('#topup-form').hide();
                    $('#form-placeholder').show();
                }
            });
        }

        $(document).ready(() => {
            // Trigger pencarian jika ada preloaded nis
            if ($('#search-input').val().trim() !== '') {
                cariSiswa();
            }

            $('#search-btn').click(cariSiswa);
            
            // Masukan pencarian dengan enter
            $('#search-input').keypress(function(e) {
                if (e.key === 'Enter') {
                    cariSiswa();
                }
            });

            // Handle pengiriman formulir topup
            $('#topup-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const nominal = parseInt($('#nominal-custom').val() || "0");

                if (nominal < 1000) {
                    alert("Nominal minimal top up adalah Rp 1.000");
                    return;
                }

                if(!confirm("Yakin ingin menambah saldo sebesar " + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(nominal) + "?")) {
                    return;
                }

                $.post(form.attr('action'), form.serialize(), function(res) {
                    let d;
                    if (typeof res === 'object') {
                        d = res;
                    } else {
                        try { d = JSON.parse(res); } catch(e) { d = { status: 'error', pesan: 'Gagal membaca respon!' }; }
                    }

                    if (d.status === 'success') {
                        alert(d.pesan);
                        // Reset form dan reload data siswa
                        $('#nominal-custom').val('');
                        $('input[name="keterangan"]').val('');
                        cariSiswa(); // Reload profile & saldo
                    } else {
                        alert("Gagal: " + d.pesan);
                    }
                }).fail(function(xhr) {
                    alert("Koneksi gagal atau terjadi kesalahan server! Status: " + xhr.status);
                });
            });
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>
