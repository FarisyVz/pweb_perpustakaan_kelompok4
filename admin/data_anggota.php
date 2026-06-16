<?php
session_start();
// Proteksi halaman admin
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

// Ambil data dari tabel mahasiswa/anggota
$query = "SELECT * FROM mahasiswa ORDER BY id_mahasiswa DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Metamedia Library</title>
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
        .sidebar-profile img { 
            width: 90px; height: 90px; border-radius: 50%; background-color: #ffffff; 
            object-fit: cover; margin-bottom: 10px; border: 3px solid #7dd3fc; 
        }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { 
            display: flex; align-items: center; padding: 15px 20px; 
            color: #e0f2fe; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, 0.05); 
            font-size: 14px; transition: all 0.3s ease;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a { 
            background-color: rgba(255, 255, 255, 0.2); color: #ffffff; font-weight: bold; padding-left: 25px; 
        }
        .sidebar-menu li a .icon { margin-right: 15px; font-size: 18px; }

        /* --- MAIN CONTENT AREA (KANAN) --- */
        .main-container { flex: 1; display: flex; flex-direction: column; height: 100%; overflow-y: auto; }
        
        /* Top Utility Bar - SKY BLUE */
        .topbar { 
            background-color: #38bdf8; color: #0f172a; padding: 15px 30px; 
            display: flex; justify-content: space-between; align-items: center; 
            font-size: 14px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .topbar .system-title { color: #1e3a8a; font-weight: bold; }
        .topbar .btn-logout { 
            background-color: #ef4444; color: white; border: none; padding: 6px 16px; 
            border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold;
        }
        .topbar .btn-logout:hover { background-color: #dc2626; }

        /* Content Body Workspace */
        .content-body { padding: 30px; }

        /* --- TOMBOL TAMBAH DATA (BIRU CERAH) --- */
        .btn-tambah {
            display: inline-flex; align-items: center; background-color: #0ea5e9; 
            color: #ffffff; text-decoration: none; padding: 10px 16px; 
            font-size: 14px; font-weight: bold; border-radius: 6px; 
            margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background 0.2s;
        }
        .btn-tambah:hover { background-color: #0284c7; }

        /* --- PANEL CONTAINER TABEL --- */
        .panel-container { 
            background: #ffffff; border: 1px solid #e2e8f0; 
            border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; 
        }
        .panel-header { 
            background-color: #f8fafc; padding: 14px 20px; 
            border-bottom: 1px solid #e2e8f0; font-size: 15px; 
            font-weight: bold; color: #1e3a8a; 
        }
        .panel-body { padding: 20px; }

        /* --- UTALITAS FILTER & SEARCH --- */
        .table-utility { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 15px; font-size: 14px; color: #475569; 
        }
        .table-utility select, .table-utility input { 
            padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 4px; outline: none; 
        }
        .table-utility input:focus { border-color: #0ea5e9; }

        /* --- TABEL UTAMA DATA ANGGOTA --- */
        .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #e2e8f0; vertical-align: middle; }
        .data-table th { background-color: #f1f5f9; color: #1e3a8a; font-weight: bold; }
        .data-table tr:hover { background-color: #f8fafc; }

        /* Pas Foto Thumbnail */
        .img-foto { width: 50px; height: 65px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1; display: block; margin: 0 auto; }

        /* --- STYLING TOMBOL AKSI (GRID WARNA SESUAI KEBUTUHAN) --- */
        .action-group { display: flex; gap: 5px; }
        .btn-action { 
            padding: 6px 12px; border-radius: 4px; font-size: 12px; 
            font-weight: bold; color: white; text-decoration: none; display: inline-block; 
        }
        .btn-details { background-color: #22c55e; } /* Hijau */
        .btn-details:hover { background-color: #16a34a; }
        
        .btn-edit { background-color: #38bdf8; }    /* Sky Blue */
        .btn-edit:hover { background-color: #0ea5e9; }
        
        .btn-hapus { background-color: #ef4444; }   /* Merah */
        .btn-hapus:hover { background-color: #dc2626; }
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
            <div class="system-title">🛡️ PANEL MANAJEMEN DATA ANGGOTA</div>
           <div>Today : <?php echo date('d-m-Y'); ?> &nbsp; <a href="../auth/logout.php" class="btn-logout">Logout</a></div>
           </div>

        <div class="content-body">
            
            <a href="input_mahasiswa.php" class="btn-tambah">➕ Tambah Data</a>

            <div class="panel-container">
                <div class="panel-header">Tabel Anggota</div>
                <div class="panel-body">
                    
                    <div class="table-utility">
                        <div>
                            Show 
                            <select>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select> 
                            records per page
                        </div>
                        <div>
                            Search: <input type="text" placeholder="Cari nama/NIM...">
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Program Studi</th>
                                <th>Angkatan</th>
                               
                                <th style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if(mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    // Penyesuaian gender agar rapi
                                    $jk = ($row['jenis_kelamin'] == 'L' || $row['jenis_kelamin'] == 'Laki-laki') ? 'Laki-laki' : 'Perempuan';
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $no++; ?></td>
                                <td style="font-weight: bold; color: #1e3a8a;"><?php echo $row['nim']; ?></td>
                                <td><?php echo $row['nama_mahasiswa']; ?></td>
                                <td><?php echo $jk; ?></td>
                                <td><?php echo $row['jurusan']; ?></td>
                                <td><?php echo $row['angkatan']; ?></td>
                                
                                <td>
                                    <div class="action-group">
                                       
                                        <a href="edit_mahasiswa.php?id=<?php echo $row['id_mahasiswa']; ?>" class="btn-action btn-edit">Edit 📝</a>
                                        <a href="hapus_mahasiswa.php?id=<?php echo $row['id_mahasiswa']; ?>" class="btn-action btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">Hapus 🗑</a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
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
  