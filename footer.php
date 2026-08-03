<?php
// Pastikan koneksi dan data tersedia
include_once 'koneksi.php';
if (!isset($data)) {
    $query_set = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
    $data = mysqli_fetch_assoc($query_set);
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>