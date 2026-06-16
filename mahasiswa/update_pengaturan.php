<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}

$id_mhs = $_SESSION['id_user'];
$pass_lama = $_POST['pass_lama'];
$pass_baru = $_POST['pass_baru'];

// 1. Ambil password lama dari database
$query_cek = "SELECT password FROM mahasiswa WHERE id_mahasiswa = '$id_mhs'";
$result_cek = mysqli_query($koneksi, $query_cek);
$data = mysqli_fetch_assoc($result_cek);

// 2. Proses update hanya jika password diisi
if (!empty($pass_lama) && !empty($pass_baru)) {
    // Verifikasi password lama
    if ($pass_lama == $data['password']) {
        
        $query_update = "UPDATE mahasiswa SET password = '$pass_baru' WHERE id_mahasiswa = '$id_mhs'";
        
        if (mysqli_query($koneksi, $query_update)) {
            echo "<script>alert('Password berhasil diperbarui!'); window.location='pengaturan.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui password!'); window.location='pengaturan.php';</script>";
        }
    } else {
        echo "<script>alert('Password lama salah!'); window.location='pengaturan.php';</script>";
    }
} else {
    echo "<script>alert('Harap isi password lama dan baru!'); window.location='pengaturan.php';</script>";
}
?>