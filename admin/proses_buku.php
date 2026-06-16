<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data form dan amankan dari SQL Injection
    $judul      = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis    = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit   = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $stok_total = intval($_POST['stok_total']);
    
    // Saat buku baru diinput, stok_tersedia nilainya sama dengan stok_total awal
    $stok_tersedia = $stok_total;

    $query = "INSERT INTO buku (judul, penulis, penerbit, stok_total, stok_tersedia) 
              VALUES ('$judul', '$penulis', '$penerbit', '$stok_total', '$stok_tersedia')";

    if (mysqli_query($koneksi, $query)) {
        // Alihkan kembali ke data_buku jika sukses
        header("Location: data_buku.php?pesan=sukses_tambah");
        exit();
    } else {
        echo "Gagal menambahkan data buku: " . mysqli_error($koneksi);
    }
} else {
    header("Location: data_buku.php");
    exit();
}
?>