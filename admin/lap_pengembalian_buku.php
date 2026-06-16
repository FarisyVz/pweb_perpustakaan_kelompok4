<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// Inisialisasi variabel
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : "";
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : "";

// Logika Filter
$is_filtered = !empty($bulan) && !empty($tahun);

if ($is_filtered) {
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengembalian | Metamedia Library</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; background-color: #ded8d8; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar & Menu */
        .sidebar { width: 240px; background: linear-gradient(180deg, #1e3a8a 0%, #0d9488 100%); color: #ffffff; display: flex; flex-direction: column; height: 100%; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 20px; font-size: 18px; font-weight: bold; border-bottom: 1px solid rgba(255, 255, 255, 0.2); text-align: center; background-color: rgba(0, 0, 0, 0.15); }
        .sidebar-profile { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .sidebar-profile img { width: 90px; height: 90px; border-radius: 50%; background: #ffffff; border: 3px solid #7dd3fc; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 15px 20px; color: #e0f2fe; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 14px; }
        .sidebar-menu li a:hover { background-color: rgba(255, 255, 255, 0.2); color: #ffffff; font-weight: bold; }
        .sidebar-menu li a .icon { margin-right: 15px; font-size: 18px; }

        .main-container { flex: 1; display: flex; flex-direction: column; height: 100%; overflow-y: auto; }
        .topbar { background-color: #38bdf8; color: #0f172a; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
        .content-body { padding: 30px; }
        
        /* Panel & Table */
        .panel-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px; }
        .panel-header { background-color: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: bold; color: #1e3a8a; }
        .panel-body { padding: 20px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #e2e8f0; }
        .btn-excel { background: #16a34a; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🌌 Metamedia Library</div>
        <div class="sidebar-profile"><img src="https://www.w3schools.com/howto/img_avatar.png"></div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a></li>
            <li><a href="data_anggota.php"><span class="icon">👥</span> Data Anggota</a></li>
            <li><a href="data_buku.php"><span class="icon">📚</span> Data Buku</a></li>
            <li class="has-dropdown">
                <a href="#" onclick="document.getElementById('laporanSubmenu').style.display=(this.parentElement.querySelector('ul').style.display=='block'?'none':'block')">
                    <span class="icon">📄</span> Laporan <span style="margin-left:auto; font-size:10px;">▼</span>
                </a>
                <ul id="laporanSubmenu" style="display:none; list-style:none; background: rgba(0,0,0,0.15); padding: 5px 0;">
                    <li><a href="daftar_peminjam.php" style="padding-left:40px; font-size: 12px;">- Laporan Peminjaman</a></li>
                    <li><a href="lap_pengembalian_buku.php" style="padding-left:40px; font-size: 12px;">- Laporan Pengembalian Buku</a></li>
                    <li><a href="lap_denda.php" style="padding-left:40px; font-size: 12px;">- Laporan Denda</a></li>
                    <li><a href="lap_buku_tertinggi.php" style="padding-left:40px; font-size: 12px;">- Buku Terpopuler</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="main-container">
        <div class="topbar">
            <div>🛡️ LAPORAN PENGEMBALIAN</div>
            <a href="../auth/logout.php" style="color:red; background:white; padding:5px 10px; border-radius:4px; text-decoration:none;">Logout</a>
        </div>

        <div class="content-body">
            <h1 class="page-title">Arsip Pengembalian Buku</h1>
            <div class="panel-container">
                <div class="panel-header">🔍 Filter Laporan</div>
                <div class="panel-body">
                    <form method="GET" style="display:flex; gap:10px; align-items:flex-end;">
                        <div><label>Bulan</label><br>
                            <select name="bulan" class="form-control" style="padding:8px;">
                                <option value="">-- Semua Bulan --</option>
                                <?php $bln = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
                                for($i=1; $i<=12; $i++){ $sel = ($bulan == $i) ? 'selected' : ''; echo "<option value='$i' $sel>".$bln[$i-1]."</option>"; } ?>
                            </select>
                        </div>
                        <div><label>Tahun</label><br>
                            <input type="number" name="tahun" class="form-control" style="padding:8px;" value="<?php echo $tahun; ?>">
                        </div>
                        <button type="submit" style="padding:8px 15px; background:#1e3a8a; color:white; border:none; cursor:pointer;">Filter</button>
                        <a href="lap_kembali.php" style="padding:8px 15px; background:#64748b; color:white; text-decoration:none;">Reset</a>
                        <a href="laporan/proses_excel_kembali.php?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>" class="btn-excel">📥 Download Excel</a>
                    </form>
                </div>
            </div>

            <div class="panel-container">
                <div class="panel-header">📋 Daftar Pengembalian Aktual</div>
                <div class="panel-body">
                    <table class="data-table">
                        <thead><tr><th>No</th><th>NIM</th><th>Nama</th><th>Judul Buku</th><th>Tgl Kembali Aktual</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php $no=1; if(mysqli_num_rows($result) > 0) { while($p = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $p['nim']; ?></td>
                                <td><?php echo $p['nama_mahasiswa']; ?></td>
                                <td><?php echo $p['judul']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['tgl_kembali_aktual'])); ?></td>
                                <td><span style="background:#dcfce7; padding:4px; color:#16a34a; font-weight:bold;">Selesai</span></td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='6'>Data tidak ditemukan.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>