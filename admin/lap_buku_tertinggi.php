<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// Query untuk menghitung jumlah peminjaman per buku
$query = "SELECT b.judul, COUNT(p.id_pinjam) as total_dipinjam 
          FROM peminjaman p 
          JOIN buku b ON p.id_buku = b.id_buku 
          GROUP BY b.id_buku 
          ORDER BY total_dipinjam DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Terpopuler | Metamedia Library</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 0; background-color: #ded8d8; display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar & Menu */
        .sidebar { width: 240px; background: linear-gradient(180deg, #1e3a8a 0%, #0d9488 100%); color: #ffffff; display: flex; flex-direction: column; height: 100%; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 20px; font-size: 18px; font-weight: bold; border-bottom: 1px solid rgba(255, 255, 255, 0.2); text-align: center; background-color: rgba(0, 0, 0, 0.15); }
        .sidebar-profile { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-profile img { width: 90px; height: 90px; border-radius: 50%; background-color: #ffffff; object-fit: cover; margin-bottom: 10px; border: 3px solid #7dd3fc; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 15px 20px; color: #e0f2fe; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 14px; }
        .sidebar-menu li a:hover { background-color: rgba(255, 255, 255, 0.2); color: #ffffff; font-weight: bold; }
        .sidebar-menu li a .icon { margin-right: 15px; font-size: 18px; }

        /* Main Content */
        .main-container { flex: 1; display: flex; flex-direction: column; height: 100%; overflow-y: auto; }
        .topbar { background-color: #38bdf8; color: #0f172a; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
        .content-body { padding: 30px; }
        .page-title { font-size: 28px; color: #1e3a8a; margin: 0 0 20px 0; font-weight: bold; }

        /* Panel */
        .panel-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px; }
        .panel-header { background-color: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: bold; color: #1e3a8a; }
        .panel-body { padding: 20px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #e2e8f0; }
        .btn-excel { background: #16a34a; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🌌 Metamedia Library</div>
        <div class="sidebar-profile"><img src="https://www.w3schools.com/howto/img_avatar.png" alt="Admin"></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="data_anggota.php"><span class="icon">👥</span> Data Anggota</a></li>
            <li><a href="data_buku.php"><span class="icon">📚</span> Data Buku</a></li>
            <li class="has-dropdown">
                <a href="#" onclick="document.getElementById('laporanSubmenu').style.display=(this.parentElement.querySelector('ul').style.display=='block'?'none':'block')">
                    <span class="icon">📄</span> Laporan <span style="margin-left:auto; font-size:10px;">▼</span>
                </a>
                <ul id="laporanSubmenu" style="display:none; list-style:none; background: rgba(0,0,0,0.15); padding: 5px 0;">
                    <li><a href="daftar_peminjam.php" style="padding-left:40px; font-size:12px;">- Lap. Peminjaman</a></li>
                    <li><a href="lap_kembali.php" style="padding-left:40px; font-size:12px;">- Lap. Pengembalian</a></li>
                    <li><a href="lap_denda.php" style="padding-left:40px; font-size:12px;">- Lap. Denda</a></li>
                    <li><a href="lap_buku_tertinggi.php" style="padding-left:40px; font-size:12px;">- Buku Terpopuler</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="main-container">
        <div class="topbar">
            <div>📊 LAPORAN BUKU TERPOPULER</div>
            <a href="../auth/logout.php" style="color:white; background:#ef4444; padding:6px 16px; border-radius:4px; text-decoration:none; font-size:13px;">Logout</a>
        </div>

        <div class="content-body">
            <h1 class="page-title">Ranking Buku Paling Sering Dipinjam</h1>
            <div class="panel-container">
                <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>📋 Daftar Buku Berdasarkan Popularitas</span>
                    <a href="laporan/proses_excel_populer.php" class="btn-excel">📥 Download Excel</a>
                </div>
                <div class="panel-body">
                    <table class="data-table">
                        <thead><tr><th>Ranking</th><th>Judul Buku</th><th>Total Dipinjam</th></tr></thead>
                        <tbody>
                            <?php $no=1; while($p = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $p['judul']; ?></td>
                                <td><?php echo $p['total_dipinjam']; ?> kali</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>