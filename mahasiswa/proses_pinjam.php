<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mahasiswa      = $_SESSION['id_user'];
    $id_buku           = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
    $tgl_pinjam        = mysqli_real_escape_string($koneksi, $_POST['tgl_pinjam']);
    $tgl_wajib_kembali = mysqli_real_escape_string($koneksi, $_POST['tgl_wajib_kembali']);
    
    // Cek kembali ketersediaan stok fisik riil guna mengantisipasi tabrakan transaksi
    $cek_stok = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok_tersedia FROM buku WHERE id_buku = '$id_buku'"))['stok_tersedia'];
    
    if ($cek_stok > 0) {
        // 1. Masukkan data ke tabel peminjaman
        $query_insert = "INSERT INTO peminjaman (id_mahasiswa, id_buku, tgl_pinjam, tgl_wajib_kembali, status) 
                         VALUES ('$id_mahasiswa', '$id_buku', '$tgl_pinjam', '$tgl_wajib_kembali', 'dipinjam')";
        
        if (mysqli_query($koneksi, $query_insert)) {
            // 2. POTONG STOK BUKU OTOMATIS
            mysqli_query($koneksi, "UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id_buku = '$id_buku'");
            
            header("Location: buku_tersedia.php?pesan=sukses_pinjam");
            exit();
        }
    } else {
        echo "<script>alert('Maaf, stok buku mendadak habis!'); window.location='buku_tersedia.php';</script>";
    }
}
?>