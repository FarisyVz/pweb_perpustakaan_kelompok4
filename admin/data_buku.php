<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// Mengambil data buku terupdate
$query = "SELECT * FROM buku ORDER BY id_buku DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku | Metamedia Library</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 0; background-color: #ded8d8; display: flex; height: 100vh; overflow: hidden; }

        /* --- SIDEBAR STYLE (KIRI) - GRADIEN BIRU KE AQUAMARINE --- */
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
        .sidebar-menu li a { 
            display: flex; align-items: center; padding: 15px 20px; color: #e0f2fe; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 14px; transition: all 0.3s ease;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a { background-color: rgba(255, 255, 255, 0.2); color: #ffffff; font-weight: bold; padding-left: 25px; }
        .sidebar-menu li a .icon { margin-right: 15px; font-size: 18px; }

        /* --- MAIN CONTENT AREA (KANAN) --- */
        .main-container { flex: 1; display: flex; flex-direction: column; height: 100%; overflow-y: auto; }
        
        /* Top Utility Bar */
        .topbar { 
            background-color: #38bdf8; color: #0f172a; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .topbar .btn-logout { background-color: #ef4444; color: white; border: none; padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; }

        /* Workspace Inner Content */
        .content-body { padding: 30px; }
        .page-title { font-size: 28px; color: #1e3a8a; margin: 0 0 5px 0; font-weight: bold; }
        .page-subtitle { color: #64748b; font-size: 14px; margin-bottom: 30px; }

        /* Tombol Tambah Buku */
        .btn-add {
            display: inline-block;
            background-color: #10b981;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: background 0.2s;
        }
        .btn-add:hover { background-color: #059669; }

        /* Panel Container & Table */
        .panel-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .panel-header { background-color: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-size: 15px; font-weight: bold; color: #1e3a8a; }
        .panel-body { padding: 20px; }

        .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #e2e8f0; vertical-align: middle; }
        .data-table th { background-color: #f1f5f9; color: #1e3a8a; font-weight: bold; }
        .data-table tr:hover { background-color: #f8fafc; }

        /* Stock Status Badge */
        .stock-badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .stock-available { background-color: #dcfce7; color: #16a34a; }
        .stock-empty { background-color: #fee2e2; color: #ef4444; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🌌 Metamedia Library</div>
        <div class="sidebar-profile">
            <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Admin Avatar">
        </div>
       <ul class="sidebar-menu">
    <li><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
    <li><a href="data_anggota.php"><span class="icon">👥</span> Data Anggota</a></li>
    <li><a href="data_buku.php"><span class="icon">📚</span> Data Buku</a></li>
    
    <li class="has-dropdown">
        <a href="#" onclick="toggleLaporan()">
            <span class="icon">📄</span> Laporan <span style="float:right; font-size: 10px;">▼</span>
        </a>
        <ul id="laporanSubmenu" style="display:none; list-style:none; background: rgba(0,0,0,0.15); padding: 5px 0;">
            <li><a href="daftar_peminjam.php" style="padding-left:40px; font-size: 12px;">- Laporan Peminjaman</a></li>
                    <li><a href="lap_pengembalian_buku.php" style="padding-left:40px; font-size: 12px;">- Laporan Pengembalian Buku</a></li>
                    <li><a href="lap_denda.php" style="padding-left:40px; font-size: 12px;">- Laporan Denda</a></li>
                    <li><a href="lap_buku_tertinggi.php" style="padding-left:40px; font-size: 12px;">- Buku Terpopuler</a></li>
        </ul>
    </li>
</ul>

<script>
function toggleLaporan() {
    var sub = document.getElementById("laporanSubmenu");
    sub.style.display = (sub.style.display === "block") ? "none" : "block";
}
</script>
    </div>

    <div class="main-container">
        <div class="topbar">
            <div class="system-title">🛡️ MANAGEMENT KATALOG & INVENTARIS BUKU</div>
            <div>Today : <?php echo date('d-m-Y'); ?> &nbsp; <a href="../auth/logout.php" class="btn-logout">Logout</a></div>
        </div>

        <div class="content-body">
            <h1 class="page-title">Manajemen Data Inventaris Buku</h1>
            <div class="page-subtitle">Kelola sirkulasi jumlah total, judul stok, serta katalog buku Universitas Metamedia.</div>

            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses_tambah'): ?>
                <div style="padding: 14px 20px; background-color: #dcfce7; color: #155724; border: 1px solid #b1f2c2; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
                    ✓ Data buku baru berhasil disinkronkan ke dalam sistem perpustakaan!
                </div>
            <?php endif; ?>

            <a href="input_buku.php" class="btn-add">+ Tambah Buku Baru</a>

            <div class="panel-container">
                <div class="panel-header">📋 Daftar Buku Perpustakaan</div>
                <div class="panel-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Judul Buku</th>
                                <th>Nama Penulis</th>
                                <th>Penerbit Perpustakaan</th>
                                <th style="width: 120px; text-align: center;">Stok Koleksi</th>
                                <th style="width: 120px; text-align: center;">Stok Ready</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if(mysqli_num_rows($result) > 0) {
                                while($buku = mysqli_fetch_assoc($result)) { 
                                    // Set warna kondisional berdasarkan ketersediaan buku di rak
                                    $status_stok_kelas = ($buku['stok_tersedia'] > 0) ? 'stock-available' : 'stock-empty';
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $no++; ?></td>
                                <td><strong style="color: #1e3a8a; font-size: 14px;"><?php echo $buku['judul']; ?></strong></td>
                                <td style="font-weight: 500; color: #334155;"><?php echo $buku['penulis']; ?></td>
                                <td style="color: #475569;"><?php echo $buku['penerbit']; ?></td>
                                <td style="text-align: center; font-weight: bold; color: #1e293b;"><?php echo $buku['stok_total']; ?> Eks</td>
                                <td style="text-align: center;">
                                    <span class="stock-badge <?php echo $status_stok_kelas; ?>">
                                        <?php echo $buku['stok_tersedia']; ?> Eks
                                    </span>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center; color:#64748b;'>Belum ada data buku di database.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>