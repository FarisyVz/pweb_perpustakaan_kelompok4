<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

$id_mhs = $_SESSION['id_user'];
$query = "SELECT nama_mahasiswa, nim FROM mahasiswa WHERE id_mahasiswa = '$id_mhs'";
$result = mysqli_query($koneksi, $query);
$mhs = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun | Metamedia Library</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f1f5f9; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        h2 { color: #4f46e5; font-size: 22px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #475569; }
        input { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; }
        .readonly { background-color: #f8fafc; cursor: not-allowed; color: #64748b; }
        .btn-save { background: #4f46e5; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; cursor: pointer; font-weight: bold; margin-top: 15px; }
        .btn-save:hover { background: #4338ca; }
        .btn-back { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>⚙️ Pengaturan Akun</h2>
    <form action="update_pengaturan.php" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="readonly" value="<?php echo htmlspecialchars($mhs['nama_mahasiswa']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>NIM</label>
            <input type="text" class="readonly" value="<?php echo htmlspecialchars($mhs['nim']); ?>" readonly>
        </div>
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;">
        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="pass_lama" placeholder="Masukkan password saat ini" required>
        </div>
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="pass_baru" placeholder="Masukkan password baru" required>
        </div>
        <button type="submit" class="btn-save">Simpan Password Baru</button>
    </form>
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
</div>

</body>
</html>