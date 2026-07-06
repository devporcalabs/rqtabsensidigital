<?php
session_start();
include 'koneksi.php';

// Proteksi halaman
if(!isset($_SESSION['login'])){ header("location: login.php"); exit; }

/**
 * FUNGSI KOMPRESI & RESIZE GAMBAR
 */
function compressAndResize($source, $destination, $quality) {
    $info = getimagesize($source);
    
    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source);
    else return false;

    list($width, $height) = $info;
    $max_dim = 500;
    if ($width > $max_dim || $height > $max_dim) {
        $ratio = $width / $height;
        if ($ratio > 1) {
            $new_width = $max_dim;
            $new_height = $max_dim / $ratio;
        } else {
            $new_width = $max_dim * $ratio;
            $new_height = $max_dim;
        }
        $target = imagecreatetruecolor($new_width, $new_height);
        
        imagealphablending($target, false);
        imagesavealpha($target, true);
        
        imagecopyresampled($target, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        $image = $target;
    }

    return imagejpeg($image, $destination, $quality);
}

if(isset($_POST['tambah'])){
    // Tangkap data
    $nip     = $_POST['nip'];
    $rfid    = $_POST['rfid_uid'];
    $nama    = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $no_hp   = $_POST['no_hp'];
    $email   = $_POST['email'] ?? '';

    // --- 1. PENCEGAT DUPLIKAT (Agar Tidak Fatal Error) ---
    $stmt_cek = $conn->prepare("SELECT id FROM guru WHERE nip = ? OR rfid_uid = ?");
    $stmt_cek->bind_param("ss", $nip, $rfid);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();

    if($res_cek->num_rows > 0) {
        echo "<script>alert('Gagal! NIP atau Nomor RFID sudah terdaftar.'); window.history.back();</script>";
        exit;
    }

    // --- 2. PENANGANAN FOTO ---
    $foto_name = "default.jpg"; 
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = "guru_" . $nip . "_" . time() . ".jpg";
        $target_path = "img/guru/" . $foto_name;
        
        $upload = compressAndResize($_FILES['foto']['tmp_name'], $target_path, 75);
        
        if(!$upload) {
            echo "<script>alert('Gagal memproses gambar!'); window.history.back();</script>";
            exit;
        }
    }

    // --- 3. SIMPAN DATA ---
    $stmt_ins = $conn->prepare("INSERT INTO guru (nip, rfid_uid, nama, jabatan, no_hp, email, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_ins->bind_param("sssssss", $nip, $rfid, $nama, $jabatan, $no_hp, $email, $foto_name);

    if($stmt_ins->execute()){
        echo "<script>alert('Data Guru Berhasil Disimpan!'); window.location='data_guru.php';</script>";
    } else {
        echo "<script>alert('Gagal simpan ke database!'); window.history.back();</script>";
    }
}
?>
