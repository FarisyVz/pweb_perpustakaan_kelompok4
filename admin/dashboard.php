<?php
session_start();
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// 1. Mengambil data count untuk info box statistik
$total_mhs  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa"))['total'];
$total_buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'"))['total'];
$total_user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa"))['total']; 
if(!$total_user) { $total_user = 2; } 
$total_pengguna_sistem = $total_mhs + $total_user;

// Query untuk data grafik (Peminjaman bulan ini)
$bulan_ini = date('m');
$tahun_ini = date('Y');
$query_grafik = "SELECT DAY(tgl_pinjam) as tanggal, COUNT(*) as jumlah 
                 FROM peminjaman 
                 WHERE MONTH(tgl_pinjam) = '$bulan_ini' AND YEAR(tgl_pinjam) = '$tahun_ini' 
                 GROUP BY DAY(tgl_pinjam) ORDER BY tanggal ASC";
$result_grafik = mysqli_query($koneksi, $query_grafik);

$data_tanggal = [];
$data_jumlah = [];
while($row = mysqli_fetch_assoc($result_grafik)) {
    $data_tanggal[] = (int)$row['tanggal'];
    $data_jumlah[] = (int)$row['jumlah'];
}

// 2. QUERY UTAMA (GABUNGAN): Mengambil semua data peminjaman & detail pengembalian jika ada
$query_sirkulasi = "SELECT 
                        peminjaman.id_pinjam,
                        peminjaman.tgl_pinjam,
                        peminjaman.status AS status_pinjam,
                        mahasiswa.nama_mahasiswa,
                        buku.judul,
                        pengembalian.tgl_kembali_aktual,
                        pengembalian.lama_pinjam,
                        pengembalian.hari_terlambat,
                        pengembalian.total_denda
                    FROM peminjaman
                    JOIN mahasiswa ON peminjaman.id_mahasiswa = mahasiswa.id_mahasiswa
                    JOIN buku ON peminjaman.id_buku = buku.id_buku
                    LEFT JOIN pengembalian ON peminjaman.id_pinjam = pengembalian.id_pinjam
                    ORDER BY peminjaman.id_pinjam DESC";
$result_sirkulasi = mysqli_query($koneksi, $query_sirkulasi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Universitas Metamedia Library</title>
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
            <div class="system-title">🛡️ PANEL UTAMA ADMINISTRATOR</div>
            <div>Today : <?php echo date('d-m-Y'); ?> &nbsp; <a href="../auth/logout.php" class="btn-logout">Logout</a></div>
        </div>

        <div class="content-body">
            <div class="welcome-text">Selamat Datang di Halaman Utama Universitas Metamedia Library</div>
            <h1 class="page-title">Admin Metamedia Library</h1>

            <div class="card-row">
                <div class="info-card">
                    <div class="card-icon bg-blue">👥</div>
                    <div class="card-info">
                        <div class="count"><?php echo $total_pengguna_sistem; ?> Pengguna</div>
                        <a href="data_anggota.php" class="detail-link">Anggota: <?php echo $total_mhs; ?> | User: <?php echo $total_user; ?> →</a>
                    </div>
                </div>
                <div class="info-card">
                    <div class="card-icon bg-aquamarine">📚</div>
                    <div class="card-info">
                        <div class="count"><?php echo $total_buku; ?> Buku</div>
                        <a href="data_buku.php" class="detail-link">Lihat Detail Buku →</a>
                    </div>
                </div>
                <div class="info-card">
                    <div class="card-icon bg-skyblue">💳</div>
                    <div class="card-info">
                        <div class="count"><?php echo $total_pinjam; ?> Aktif</div>
                        <a href="daftar_peminjam.php" class="detail-link">Lihat Details Transaksi →</a>
                    </div>
                </div>
            </div>

            <div class="panel-container" style="margin-bottom: 30px;">
    <div class="panel-header">📊 Grafik Peminjaman Buku Bulan <?php echo date('F Y'); ?></div>
    <div class="panel-body">
        <canvas id="grafikPinjam" height="80"></canvas>
    </div>
</div>

            <div class="panel-container">
                <div class="panel-header">📋 Log Transaksi Sirkulasi Buku (Peminjaman & Pengembalian)</div>
                <div class="panel-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">No</th>
                                <th>Nama Mahasiswa</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali Real</th>
                                <th style="text-align: center;">Lama Pinjam</th>
                                <th style="text-align: center;">Keterlambatan</th>
                                <th>Total Denda</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            if(mysqli_num_rows($result_sirkulasi) > 0) {
                                while($row = mysqli_fetch_assoc($result_sirkulasi)) {
                                    
                                    // Memeriksa apakah statusnya sudah dikembalikan
                                    if($row['status_pinjam'] == 'kembali') {
                                        $tgl_kembali  = date('d/m/Y', strtotime($row['tgl_kembali_aktual']));
                                        $lama_pinjam  = $row['lama_pinjam'] . " Hari";
                                        $terlambat    = $row['hari_terlambat'] . " Hari";
                                        
                                        // Format denda rupiah
                                        $total_denda  = ($row['total_denda'] > 0) 
                                            ? '<span class="text-denda">Rp ' . number_format($row['total_denda'], 0, ',', '.') . '</span>' 
                                            : '<span class="text-null">-</span>';
                                        
                                        $status_badge = '<span class="badge badge-kembali">Kembali</span>';
                                    } else {
                                        // Jika status masih 'dipinjam', maka kolom pengembalian dikosongkan/diisi tanda strip
                                        $tgl_kembali  = '<span class="text-null">-</span>';
                                        $lama_pinjam  = '<span class="text-null">-</span>';
                                        $terlambat    = '<span class="text-null">-</span>';
                                        $total_denda  = '<span class="text-null">-</span>';
                                        $status_badge = '<span class="badge badge-dipinjam">Dipinjam</span>';
                                    }
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $no++; ?></td>
                                <td style="font-weight: bold; color: #1e3a8a;"><?php echo $row['nama_mahasiswa']; ?></td>
                                <td style="font-style: italic;"><?php echo $row['judul']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tgl_pinjam'])); ?></td>
                                <td><?php echo $tgl_kembali; ?></td>
                                <td style="text-align: center;"><?php echo $lama_pinjam; ?></td>
                                <td style="text-align: center;"><?php echo $terlambat; ?></td>
                                <td><?php echo $total_denda; ?></td>
                                <td style="text-align: center;"><?php echo $status_badge; ?></td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo "<tr><td colspan='9' style='text-align:center; color:#64748b;'>Belum ada aktivitas sirkulasi perpustakaan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
<script>
    const ctx = document.getElementById('grafikPinjam').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($data_tanggal); ?>,
            datasets: [{
                label: 'Jumlah Buku Dipinjam',
                data: <?php echo json_encode($data_jumlah); ?>,
                backgroundColor: '#38bdf8',
                borderColor: '#0284c7',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
</body>
</html>