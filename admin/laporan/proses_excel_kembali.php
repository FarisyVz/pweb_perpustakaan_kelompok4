<?php
session_start();
include '../../config/database.php';

// Ambil parameter dari URL (Sama dengan filter di lap_kembali.php)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : "";
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : "";

// Header agar file terdownload sebagai Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Pengembalian_" . date('Y-m-d') . ".xls");

// Logika Query
if (!empty($bulan) && !empty($tahun)) {
    $bulan_safe = mysqli_real_escape_string($koneksi, $bulan);
    $tahun_safe = mysqli_real_escape_string($koneksi, $tahun);
    
    $query = "SELECT p.*, m.nama_mahasiswa, m.nim, b.judul, k.tgl_kembali_aktual 
              FROM peminjaman p
              JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
              JOIN buku b ON p.id_buku = b.id_buku 
              JOIN pengembalian k ON p.id_pinjam = k.id_pinjam 
              WHERE MONTH(k.tgl_kembali_aktual) = '$bulan_safe' 
              AND YEAR(k.tgl_kembali_aktual) = '$tahun_safe'
              ORDER BY k.tgl_kembali_aktual DESC";
} else {
    $query = "SELECT p.*, m.nama_mahasiswa, m.nim, b.judul, k.tgl_kembali_aktual 
              FROM peminjaman p
              JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
              JOIN buku b ON p.id_buku = b.id_buku 
              JOIN pengembalian k ON p.id_pinjam = k.id_pinjam 
              ORDER BY k.tgl_kembali_aktual DESC";
}

$result = mysqli_query($koneksi, $query);
?>

<table border="1">
    <thead>
        <tr style="background-color: #cccccc;">
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Judul Buku</th>
            <th>Tgl Kembali Aktual</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        if(mysqli_num_rows($result) > 0) {
            while($p = mysqli_fetch_assoc($result)) { 
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $p['nim']; ?></td>
            <td><?php echo $p['nama_mahasiswa']; ?></td>
            <td><?php echo $p['judul']; ?></td>
            <td><?php echo date('d/m/Y', strtotime($p['tgl_kembali_aktual'])); ?></td>
            <td>Selesai</td>
        </tr>
        <?php } } else { ?>
        <tr><td colspan="6">Data tidak ditemukan.</td></tr>
        <?php } ?>
    </tbody>
</table>