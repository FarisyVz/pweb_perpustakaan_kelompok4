<?php
include '../../config/database.php';
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Buku_Terpopuler.xls");

$query = "SELECT b.judul, COUNT(p.id_pinjam) as total_dipinjam 
          FROM peminjaman p 
          JOIN buku b ON p.id_buku = b.id_buku 
          GROUP BY b.id_buku 
          ORDER BY total_dipinjam DESC";
$result = mysqli_query($koneksi, $query);
?>

<table border="1">
    <thead>
        <tr style="background-color: #cccccc;">
            <th>Ranking</th><th>Judul Buku</th><th>Total Dipinjam</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; while($p = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $p['judul']; ?></td>
            <td><?php echo $p['total_dipinjam']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>