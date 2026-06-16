<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Buku Baru</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f6f9; }
        .form-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-simpan { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        .btn-kembali { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Data Buku Baru</h2>
    <form action="proses_buku.php" method="POST">
        <div class="form-group">
            <label>Judul Buku</label>
            <input type="text" name="judul" required placeholder="Contoh: Pemrograman Web PHP">
        </div>
        <div class="form-group">
            <label>Penulis / Pengarang</label>
            <input type="text" name="penulis" required placeholder="Contoh: Prof. Hermawan">
        </div>
        <div class="form-group">
            <label>Penerbit</label>
            <input type="text" name="penerbit" required placeholder="Contoh: Andi Offset">
        </div>
        <div class="form-group">
            <label>Jumlah Stok Awal</label>
            <input type="number" name="stok_total" min="1" required placeholder="Contoh: 10">
        </div>
        <button type="submit" class="btn-simpan">Simpan ke Database</button>
        <a href="data_buku.php" class="btn-kembali">← Kembali</a>
    </form>
</div>

</body>
</html>