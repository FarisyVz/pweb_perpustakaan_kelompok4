<?php
session_start();
include '../../config/database.php';

// Ambil parameter dari URL
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : "";
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : "";

// Header untuk menginstruksikan browser mendownload file sebagai Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Total_Denda.xls");

// Logika query dengan filter
$where = "WHERE k.total_denda > 0";
if(!empty($bulan) && !empty($tahun)) {
    $where .= " AND MONTH(k.tgl_kembali_aktual) = '$bulan' AND YEAR(k.tgl_kembali_aktual) = '$tahun'";
}

$query = "SELECT p.*, m.nama_mahasiswa, m.nim, b.judul, k.tgl_kembali_aktual, k.total_denda 
          FROM peminjaman p
          JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
          JOIN buku b ON p.id_buku = b.id_buku 
          JOIN pengembalian k ON p.id_pinjam = k.id_pinjam 
          $where ORDER BY k.tgl_kembali_aktual DESC";

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
            <th>Total Denda</th>
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
            <td><?php echo $p['total_denda']; ?></td>
        </tr>
        <?php } } else { ?>
        <tr><td colspan="6">Data tidak ditemukan.</td></tr>
        <?php } ?>
    </tbody>
</table>