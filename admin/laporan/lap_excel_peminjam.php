<?php
session_start();
include '../../config/database.php';

// Ambil parameter dari URL
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

// Header agar langsung terdownload
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Peminjam.xls");

// Logika Query yang SAMA PERSIS dengan di halaman utama
$sql_filter = "";
if (!empty($bulan) && !empty($tahun)) {
    $sql_filter = "WHERE p.status = 'kembali' AND MONTH(p.tgl_pinjam) = '$bulan' AND YEAR(p.tgl_pinjam) = '$tahun'";
} else {
    $sql_filter = "WHERE p.status = 'kembali'";
}

$query = "SELECT p.*, m.nama_mahasiswa, m.nim, b.judul 
          FROM peminjaman p
          JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
          JOIN buku b ON p.id_buku = b.id_buku 
          $sql_filter
          ORDER BY p.tgl_pinjam DESC";

$result = mysqli_query($koneksi, $query);
?>

<table border="1">
    <thead>
        <tr>
            <th>No</th><th>NIM</th><th>Nama</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; while($p = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $p['nim']; ?></td>
            <td><?php echo $p['nama_mahasiswa']; ?></td>
            <td><?php echo $p['judul']; ?></td>
            <td><?php echo date('d/m/Y', strtotime($p['tgl_pinjam'])); ?></td>
            <td>Sudah Kembali</td>
        </tr>
        <?php } ?>
    </tbody>
</table>