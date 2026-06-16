<?php
// Memulai session PHP
session_start();

// Menghubungkan ke konfigurasi database
// Asumsi letak file: perpustakaan-app/config/database.php
include '../config/database.php';

// Menangkap data yang dikirim dari form login
$username_input = mysqli_real_escape_string($koneksi, $_POST['username']);
$password_input = mysqli_real_escape_string($koneksi, $_POST['password']);

// -----------------------------------------------------------
// LANGKAH 1: Pengecekan pada Tabel Admin
// -----------------------------------------------------------
$query_admin = "SELECT * FROM admin WHERE username = '$username_input' AND password = '$password_input'";
$result_admin = mysqli_query($koneksi, $query_admin);

if (mysqli_num_rows($result_admin) > 0) {
    $data_admin = mysqli_fetch_assoc($result_admin);
    
    // Menyimpan data login admin ke dalam session
    $_SESSION['status_login'] = true;
    $_SESSION['role']         = 'admin';
    $_SESSION['id_user']      = $data_admin['id_admin'];
    $_SESSION['nama']         = $data_admin['nama_admin'];
    $_SESSION['username']     = $data_admin['username'];
    
    // Redirect otomatis ke dashboard admin
    header("Location: ../admin/dashboard.php");
    exit();
}

// -----------------------------------------------------------
// LANGKAH 2: Jika di tabel admin tidak ada, cek Tabel Mahasiswa
// -----------------------------------------------------------
$query_mhs = "SELECT * FROM mahasiswa WHERE nim = '$username_input' AND password = '$password_input'";
$result_mhs = mysqli_query($koneksi, $query_mhs);

if (mysqli_num_rows($result_mhs) > 0) {
    $data_mhs = mysqli_fetch_assoc($result_mhs);
    
    // Menyimpan data login mahasiswa ke dalam session
    $_SESSION['status_login'] = true;
    $_SESSION['role']         = 'mahasiswa';
    $_SESSION['id_user']      = $data_mhs['id_mahasiswa'];
    $_SESSION['nama']         = $data_mhs['nama_mahasiswa'];
    $_SESSION['nim']          = $data_mhs['nim'];
    
    // Redirect otomatis ke dashboard mahasiswa
    header("Location: ../mahasiswa/dashboard.php");
    exit();
}

// -----------------------------------------------------------
// LANGKAH 3: Jika data tidak ditemukan di kedua tabel
// -----------------------------------------------------------
header("Location: login.php?pesan=gagal");
exit();
?>