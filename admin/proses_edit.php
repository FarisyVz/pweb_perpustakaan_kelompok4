<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = $_POST['id'];
    $nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan  = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $angkatan = mysqli_real_escape_string($koneksi, $_POST['angkatan']);
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $jk       = mysqli_real_escape_string($koneksi, $_POST['jk']);

    $query = "UPDATE mahasiswa SET 
                nim='$nim', 
                nama_mahasiswa='$nama', 
                jurusan='$jurusan', 
                angkatan='$angkatan', 
                no_hp='$no_hp', 
                jenis_kelamin='$jk' 
              WHERE id_mahasiswa='$id'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='data_anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
}
?>