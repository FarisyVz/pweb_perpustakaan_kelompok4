<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Mengambil data dari form
    $nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    // Password langsung disimpan apa adanya
    $password = mysqli_real_escape_string($koneksi, $_POST['password']); 
    $jurusan  = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $angkatan = mysqli_real_escape_string($koneksi, $_POST['angkatan']);
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $jk       = mysqli_real_escape_string($koneksi, $_POST['jk']);

    // 2. Validasi NIM duplikat
    $check_nim = mysqli_query($koneksi, "SELECT nim FROM mahasiswa WHERE nim = '$nim'");
    
    if (mysqli_num_rows($check_nim) > 0) {
        echo "<script>alert('Gagal! NIM tersebut sudah terdaftar.'); window.history.back();</script>";
        exit();
    }

    // 3. Memasukkan data ke database tanpa hash
    $query = "INSERT INTO mahasiswa (nim, nama_mahasiswa, password, jurusan, angkatan, no_hp, jenis_kelamin) 
              VALUES ('$nim', '$nama', '$password', '$jurusan', '$angkatan', '$no_hp', '$jk')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
} else {
    header("Location: register.php");
    exit();
}
?>