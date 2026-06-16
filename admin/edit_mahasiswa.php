<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// Ambil ID dari URL
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id_mahasiswa = '$id'");
$row = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa - Admin Metamedia</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 0; background-color: #ded8d8; display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR STYLE --- */
        .sidebar { 
            width: 240px; 
            background: linear-gradient(180deg, #1e3a8a 0%, #0d9488 100%); 
            color: #ffffff; 
            display: flex; 
            flex-direction: column; 
            height: 100%; 
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar-brand { 
            padding: 20px; font-size: 18px; font-weight: bold; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.2); text-align: center; 
            background-color: rgba(0, 0, 0, 0.15); letter-spacing: 0.5px;
        }
        .sidebar-profile { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-profile img { width: 90px; height: 90px; border-radius: 50%; background-color: #ffffff; object-fit: cover; margin-bottom: 10px; border: 3px solid #7dd3fc; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 15px 20px; color: #e0f2fe; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 14px; transition: all 0.3s ease; }
        .sidebar-menu li a:hover, .sidebar-menu li.active a { background-color: rgba(255, 255, 255, 0.2); color: #ffffff; font-weight: bold; padding-left: 25px; }
        .sidebar-menu li a .icon { margin-right: 15px; font-size: 18px; }

        /* --- MAIN WORKSPACE --- */
        .main-container { flex: 1; display: flex; flex-direction: column; height: 100%; overflow-y: auto; }
        .topbar { background-color: #38bdf8; color: #0f172a; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .topbar .btn-logout { background-color: #ef4444; color: white; border: none; padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; }

        .content-body { padding: 30px; }
        .welcome-text { font-size: 14px; color: #0369a1; text-align: center; margin-bottom: 25px; font-style: italic; font-weight: 600; }
        .page-title { font-size: 28px; color: #1e3a8a; margin: 0 0 30px 0; font-weight: bold; }

        /* --- STATISTIK CARDS --- */
        .card-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .info-card { background: #ffffff; border-radius: 8px; padding: 20px; display: flex; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .card-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; font-size: 26px; margin-right: 15px; }
        .bg-blue { background-color: #2563eb; }
        .bg-aquamarine { background-color: #14b8a6; }
        .bg-skyblue { background-color: #0ea5e9; }
        .card-info { flex: 1; }
        .card-info .count { font-size: 20px; font-weight: bold; color: #1e293b; margin: 0; }
        .card-info .detail-link { font-size: 12px; color: #0ea5e9; text-decoration: none; display: block; margin-top: 5px; font-weight: 600; }

        /* --- PANEL TABEL GABUNGAN --- */
        .panel-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .panel-header { background-color: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-size: 15px; font-weight: bold; color: #1e3a8a; }
        .panel-body { padding: 20px; }

        /* --- DATATABLE STYLE --- */
        .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #e2e8f0; vertical-align: middle; }
        .data-table th { background-color: #f1f5f9; color: #1e3a8a; font-weight: bold; }
        .data-table tr:hover { background-color: #f8fafc; }
        
        /* Badges Status */
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; display: inline-block; text-align: center; }
        .badge-dipinjam { background-color: #fee2e2; color: #ef4444; } /* Merah */
        .badge-kembali { background-color: #dcfce7; color: #16a34a; }  /* Hijau */
        
        .text-denda { color: #b45309; font-weight: bold; }
        .text-null { color: #94a3b8; font-style: italic; }

        
        /* (Salin CSS dari dashboard.php Anda di sini agar sidebar dan layout konsisten) */
        /* Fokus pada styling panel */
        .panel-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .form-control { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 4px; box-sizing: border-box; }
        .btn-simpan { background-color: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-batal { background-color: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    
    </style>
</head>
<body>

   <div class="sidebar">
        <div class="sidebar-brand">🌌 Metamedia Library</div>
        <div class="sidebar-profile">
            <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Admin Avatar">
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="data_anggota.php"><span class="icon">👥</span> Data Anggota</a></li>
            <li><a href="data_buku.php"><span class="icon">📚</span> Data Buku</a></li>
            <li><a href="daftar_peminjam.php"><span class="icon">💾</span> Riwayat Peminjam</a></li>
        </ul>
    </div>

    <div class="main-container">
        <div class="topbar">
            <div>🛡️ PANEL ADMINISTRATOR</div>
            <div><?php echo date('d-m-Y'); ?></div>
        </div>

        <div class="content-body">
            <h1 class="page-title">Edit Data Mahasiswa</h1>
            
            <div class="panel-container">
                <div class="panel-header">Formulir Perubahan Data</div>
                <form action="proses_edit.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id_mahasiswa']; ?>">
                    
                    <label>NIM</label>
                    <input type="text" name="nim" class="form-control" value="<?php echo $row['nim']; ?>" required>
                    
                    <label>Nama Mahasiswa</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo $row['nama_mahasiswa']; ?>" required>
                    
                    <label>Jurusan</label>
                    <select name="jurusan" class="form-control">
                        <?php 
                        $jurusan_list = ["Sistem Informasi", "Informatika", "Bisnis Digital", "Desain Visual Komunikasi", "Manajemen Ritel", "Pendidikan Teknologi Informasi"];
                        foreach($jurusan_list as $j) {
                            $selected = ($row['jurusan'] == $j) ? 'selected' : '';
                            echo "<option value='$j' $selected>$j</option>";
                        }
                        ?>
                    </select>

                    <label>Angkatan</label>
                    <input type="number" name="angkatan" class="form-control" value="<?php echo $row['angkatan']; ?>" required>
                    
                    <label>No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?php echo $row['no_hp']; ?>" required>
                    
                    <label>Jenis Kelamin</label>
                    <select name="jk" class="form-control">
                        <option value="L" <?php echo ($row['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo ($row['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>

                    <button type="submit" class="btn-simpan">Simpan Perubahan</button>
                    
                </form>
                <br>
                <a href="data_anggota.php" class="btn-batal">Batal</a>
            </div>
        </div>
    </div>
</body>
</html>