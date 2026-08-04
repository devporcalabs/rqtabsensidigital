# 📖 DOKUMENTASI LENGKAP APLIKASI
## **Sistem Absensi Digital (RFID & AI Face Recognition) & Kantin Digital**
### **Rumah Qur'an Temi**

---

## 🛠️ 1. TECH STACK & TEKNOLOGI YANG DIGUNAKAN

Aplikasi ini dibangun menggunakan arsitektur web modern yang mengutamakan kecepatan, kompatibilitas perangkat keras, serta responsivitas antarmuka.

### **Backend & Server-Side:**
- **Language**: PHP 8.x (Native / MySQLi dengan *Prepared Statements* untuk keamanan dari SQL Injection).
- **Web Server**: Apache / Nginx (Kompatibel dengan Laragon, cPanel, aaPanel, dan VPS Linux).
- **Timezone Management**: Otomatisasi sinkronisasi timezone PHP (`Asia/Jakarta`) dengan MySQL `time_zone` offset.

### **Database & Storage:**
- **Database Management System**: MySQL 8.0 / MariaDB 10.4.
- **Security & Constraints**: Penggunaan *Unique Index* (NIP, NIS, RFID UID), *ENUM Types* (Sesi, Kehadiran), dan kompresi penyimpanan foto.

### **Frontend & UI/UX Design:**
- **Structure**: HTML5 Semantic Elements.
- **Styling & Theme**: Bootstrap 5 + Custom Modern Dark Mode CSS (Warna Tailored, Glassmorphism, Micro-animations, dan Responsive Grid).
- **Icons**: Bootstrap Icons Library.

### **AI & Computer Vision (Absensi Wajah):**
- **Artificial Intelligence Engine**: `face-api.js` (TensorFlow.js versi Client-Side).
- **Metode Pengenalan**: Evaluasi *68 Point Face Landmarks* & komparasi *Face Descriptor Vector (128-dimensional embedding)* secara langsung di browser tanpa membebani server.

### **IoT & Hardware Integration:**
- **RFID Scanner**: Kompatibel dengan semua USB RFID Reader / Modul RC522 (Metode Keyboard Emulation / HID Input tanpa perlu driver khusus).

### **Integrasi Notifikasi:**
- **WhatsApp Gateway API**: Pengiriman notifikasi otomatis via HTTP REST API (JSON Payload) saat siswa menempelkan kartu RFID / scan wajah.
- **Telegram Bot API**: Integrasi *Chat ID* ortu untuk notifikasi pesan masuk/pulang.

---

## 🌟 2. DAFTAR FITUR UTAMA APLIKASI (SPESIFIKASI DETAIL)

### 🔹 **1. Modul Absensi Digital Multi-Metode**
- **Absensi RFID Card (Scan Kartu)**:
  - Pembacaan kartu RFID secara instan (*real-time*).
  - Penanganan otomatis status **Masuk** dan **Pulang** dalam satu scan kartu.
  - Feedback suara/audio dan visual animasi saat scan berhasil/gagal.
  - Penyesuaian waktu terlambat otomatis berdasarkan **Sesi Belajar** (Sesi 1 Pagi, Sesi 2 Siang, Sesi 3 Pagi/Sore).
- **Absensi Face Recognition (AI Wajah)**:
  - Deteksi wajah secara live melalui Webcam/Kamera HP.
  - Pencocokan vektor wajah siswa secara cepat menggunakan AI di sisi browser.
- **Absensi Manual (Input Operator/Guru)**:
  - Input absensi susulan oleh Admin/Guru untuk status: *Hadir, Sakit, Izin, Alpa*.

---

### 🔹 **2. Modul Manajemen Siswa & Pembatasan Kuota System**
- **Pengelolaan Data Siswa (CRUD)**:
  - Data lengkap: NIS, Nama, Kelas (TKQ, SDIT, MDTU, TR), Sesi Belajar, No. HP Orang Tua, Telegram Chat ID, Email, Foto, dan UID Kartu RFID.
- **Validasi NIS & Format Nomor HP Otomatis**:
  - Penyesuaian prefix NIS otomatis per kelas (misal: Prefix `4` untuk kelas TR).
  - Format nomor HP ortu otomatis diubah dari diawali `0` menjadi kode negara `62` (misal: `0878...` → `62878...`) untuk kompatibilitas WhatsApp API.
- **Pembatasan Kuota Maksimal Siswa**:
  - Batas sistem dikunci maksimal **150 Siswa** (mencegah penambahan data jika kuota penuh).
- **Import Data Massal**:
  - Fitur Import Siswa via file Excel/CSV lengkap dengan fitur *Fuzzy Matching* untuk foto dan RFID.
- **Pengolahan & Kompresi Foto Siswa**:
  - Otomatis melakukan *compress & resize* gambar (Support JPEG, PNG, GIF, WebP) ke dimensi standar (500px) untuk menghemat penyimpanan server.

---

### 🔹 **3. Modul Manajemen Guru & Staf (User Management)**
- **Pengelolaan Data Guru & Pegawai**:
  - Data: NIP, Nama, Jabatan, Lembaga (Yayasan/TKQ/SDIT/MDTU), No. HP, Email, Foto, dan RFID.
- **Pembatasan Kuota Pengguna Sistem**:
  - Kuota pengguna (Guru/Admin/Operator) dibatasi maksimal **25 User**.
- **Role & Hak Akses User**:
  - Pengaturan peran sistem (Administrator, Wali Kelas, Guru, Kasir Kantin, Operator).

---

### 🔹 **4. Modul Notifikasi Otomatis Orang Tua (WhatsApp & Telegram)**
- **Real-Time WhatsApp Alert**:
  - Notifikasi langsung dikirim ke WhatsApp Orang Tua ketika anak melakukan scan kartu/wajah saat **Tiba di Sekolah (Masuk)** maupun **Selesai Sekolah (Pulang)**.
  - Format Pesan Menarik: Berisi Nama Siswa, Jam Masuk/Pulang, Status Kehadiran (Tepat Waktu/Terlambat), serta pesan doa/salam.
- **Real-Time Telegram Notification**:
  - Pilihan alternatif notifikasi via Bot Telegram.

---

### 🔹 **5. Modul Kantin Digital (E-Money / Digital Wallet)**
- **Sistem Top-Up Saldo Siswa**:
  - Pengisian saldo dompet digital siswa menggunakan kartu RFID.
- **Kasir Kantin Digital**:
  - Pembayaran jajanan di kantin sekolah hanya dengan menempelkan kartu RFID siswa.
  - Otomatis memotong saldo siswa dan menghentikan transaksi jika saldo tidak cukup.
- **Laporan Transaksi Kantin**:
  - Catatan riwayat transaksi kantin per hari, per siswa, dan total omzet kantin.

---

### 🔹 **6. Modul Keuangan & Pembayaran SPP (School Payment Management)**
- **Dashboard Analytics SPP**: Widget real-time total target, terbayar, sisa tunggakan, pembayaran hari ini, dan transaksi pending.
- **Master & Generate Tagihan**: Pembuatan tagihan massal per kelas / per siswa dengan token tautan unik ortu.
- **Portal Orang Tua (Tanpa Login)**: Tautan khusus token untuk melihat tagihan, konfirmasi pembayaran, dan upload bukti transfer/QRIS statis.
- **Verifikasi Pembayaran & Kwitansi Digital**: Verifikasi bukti transfer oleh Bendahara serta pengeluaran kwitansi resmi ber-QR Code.
- **Laporan SPP & Export Excel**: Laporan rekap harian, bulanan, rekap per kelas, dan daftar tunggakan lengkap dengan fitur Export Excel.

---

### 🔹 **7. Modul Laporan, Rekapitulasi & Cetak**
- **Dashboard Analytics**:
  - Ringkasan grafik & statistik harian: Total Siswa Hadir, Sakit, Izin, Terlambat, dan Alpa.
- **Rekap Bulanan & Harian**:
  - Filter laporan berdasarkan Kelas, Sesi, dan Rentang Tanggal.
- **Cetak Laporan & Kartu**:
  - Ekspor laporan ke PDF / Cetak langsung.
  - Fitur cetak daftar siswa per kelas.

---

### 🔹 **8. Modul Pengaturan Sistem (System Settings)**
- **Konfigurasi Sekolah**: Nama Lembaga, Alamat, Logo Sekolah.
- **Pengaturan Timezone**: Opsi pengaturan zona waktu (`Asia/Jakarta`, `Asia/Makassar`, `Asia/Jayapura`).
- **Penyimpanan Multi-Environment**: Support koneksi otomatis untuk environment Localhost, Demo Server, dan Production Server (`rqt.porcalabs.my.id`).

---

## 📊 3. SUMMARY TABEL TECH STACK

| Komponen | Teknologi / Library | Deskripsi Fungsi |
|---|---|---|
| **Programming Language** | PHP 8.x | Core Logic, Routing, Prepared Statements |
| **Database** | MySQL 8.0 / MariaDB | Relational Database & Transaction Storage |
| **User Interface (UI)** | Bootstrap 5 + Custom CSS | Glassmorphism, Modern Dark Mode, Responsive |
| **Icons** | Bootstrap Icons | Visual Iconography |
| **AI Vision Engine** | face-api.js (TensorFlow.js) | Client-Side Real-Time Face Recognition |
| **Hardware Integration** | USB RFID Reader (HID) | Automatic RFID Card Scan Input |
| **Notification Gateway** | WhatsApp REST API & Telegram API | Real-time Parent Attendance Alerts |
| **Image Processor** | GD Library (PHP) | Image Auto Compression & Resizing |
