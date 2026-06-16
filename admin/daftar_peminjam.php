<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// --- TAMBAHKAN INISIALISASI INI ---
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : "";
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : "";
// ----------------------------------

$is_filtered = !empty($bulan) && !empty($tahun);

if ($is_filtered) {
    $bulan_safe = mysqli_real_escape_string($koneksi, $bulan);
    $tahun_safe = mysqli_real_escape_string($koneksi, $tahun);
    
    $query = "SELECT p.*, m.nama_mahasiswa, m.nim, b.judul 
              FROM peminjaman p
              JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
              JOIN buku b ON p.id_buku = b.id_buku 
              WHERE p.status = 'kembali'
              AND MONTH(p.tgl_pinjam) = '$bulan_safe' 
              AND YEAR(p.tgl_pinjam) = '$tahun_safe'    
              ORDER BY p.tgl_pinjam DESC";
} else {
    $query = "SELECT p.*, m.nama_mahasiswa, m.nim, b.judul 
              FROM peminjaman p
              JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
              JOIN buku b ON p.id_buku = b.id_buku 
              WHERE p.status = 'kembali'
              ORDER BY p.tgl_pinjam DESC";
}
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Peminjam Selesai | Metamedia Library</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 0; background-color: #ded8d8; display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
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
        .btn-logout { background-color: #ef4444; color: white; border: none; padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; }
        .content-body { padding: 30px; }
        .page-title { font-size: 28px; color: #1e3a8a; margin: 0 0 5px 0; font-weight: bold; }

        /* Panel & Table */
        .panel-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .panel-header { background-color: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: bold; color: #1e3a8a; }
        .panel-body { padding: 20px; }
        .form-control { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #e2e8f0; }
        .badge-kembali { background-color: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">🌌 Metamedia Library</div>
        <div class="sidebar-profile">
            <img src="https://www.w3schools.com/howto/img_avatar.png" alt="Admin">
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
            <div>🛡️ ARSIP PEMINJAMAN</div>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="content-body">
            <h1 class="page-title">Arsip Peminjaman Buku</h1>

            <div class="panel-container">
                <div class="panel-header">🔍 Filter Data</div>
                <div class="panel-body">
                    <form method="GET" style="display:flex; gap:15px; align-items:flex-end;">
                        <div><label>Bulan</label><br>
                            <select name="bulan" class="form-control">
    <option value="">-- Semua Bulan --</option>
    <?php 
    $bln = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
    for($i=1; $i<=12; $i++){ 
        $sel = ($bulan == $i) ? 'selected' : ''; // Menggunakan variabel $bulan yang baru kita buat
        echo "<option value='$i' $sel>".$bln[$i-1]."</option>"; 
    }
    ?>
</select>

<input type="number" name="tahun" class="form-control" value="<?php echo $tahun; ?>">
                        </div>
                        <button type="submit" style="padding: 8px 15px; background:#1e3a8a; color:white; border:none; cursor:pointer;">Filter</button>
                        <a href="daftar_peminjam.php" style="padding:8px 15px; background:#64748b; color:white; text-decoration:none;">Reset</a>
                        <a href="laporan/lap_excel_peminjam.php?bulan=<?php echo $_GET['bulan'] ?? ''; ?>&tahun=<?php echo $_GET['tahun'] ?? ''; ?>" 
   class="btn-excel" style="background: #16a34a; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; margin-left: 5px;">
   📥 Download Excel
</a>
                    </form>
                </div>
            </div>

            <div class="panel-container">
                <div class="panel-header">📋 Daftar Peminjam Selesai</div>
                <div class="panel-body">
                    <table class="data-table">
                        <thead><tr><th>No</th><th>NIM</th><th>Nama</th><th>Judul</th><th>Tgl Pinjam</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php $no=1; if(mysqli_num_rows($result) > 0) { while($p = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $p['nim']; ?></td>
                                <td><?php echo $p['nama_mahasiswa']; ?></td>
                                <td><?php echo $p['judul']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['tgl_pinjam'])); ?></td>
                                <td><span class="badge-kembali">Sudah Kembali</span></td>
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