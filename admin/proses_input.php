<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $jurusan  = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $angkatan = mysqli_real_escape_string($koneksi, $_POST['angkatan']);
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $jk       = mysqli_real_escape_string($koneksi, $_POST['jk']);

    // Cek duplikat NIM
    $cek = mysqli_query($koneksi, "SELECT nim FROM mahasiswa WHERE nim = '$nim'");
    if(mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIM sudah terdaftar!'); window.history.back();</script>";
    } else {
        $query = "INSERT INTO mahasiswa (nim, nama_mahasiswa, password, jurusan, angkatan, no_hp, jenis_kelamin) 
                  VALUES ('$nim', '$nama', '$password', '$jurusan', '$angkatan', '$no_hp', '$jk')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data mahasiswa berhasil ditambahkan!'); window.location='data_anggota.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
        }
    }
}
?>