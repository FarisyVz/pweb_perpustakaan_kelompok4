<?php
session_start();
// Pastikan hanya admin yang bisa menghapus
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}

include '../config/database.php';

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query hapus data
    $query = "DELETE FROM mahasiswa WHERE id_mahasiswa = '$id'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='data_anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($koneksi) . "'); window.location='data_anggota.php';</script>";
    }
} else {
    // Jika tidak ada ID yang dikirim
    header("Location: data_anggota.php");
}
?>