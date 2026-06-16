<?php
session_start();
// Memastikan hanya mahasiswa yang bisa mengakses halaman ini
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

$id_mhs = $_SESSION['id_user'];
$query_mhs = "SELECT nama_mahasiswa FROM mahasiswa WHERE id_mahasiswa = '$id_mhs'";
$result_mhs = mysqli_query($koneksi, $query_mhs);
$data_mhs = mysqli_fetch_assoc($result_mhs);
$nama_tampil = $data_mhs['nama_mahasiswa'] ?? 'Mahasiswa';

if (!isset($_GET['id_buku'])) {
    header("Location: buku_tersedia.php");
    exit();
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['id_buku']);
$query   = "SELECT * FROM buku WHERE id_buku = '$id_buku'";
$result = mysqli_query($koneksi, $query);
$buku   = mysqli_fetch_assoc($result);

if (!$buku) {
    echo "<script>alert('Data buku tidak ditemukan!'); window.location='buku_tersedia.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Peminjaman | Metamedia Library</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; padding: 0; background-color: #f1f5f9; color: #1e293b; }

        /* --- TOPBAR / NAVIGATION --- */
        .topbar {
            background-color: #4f46e5; 
            color: #ffffff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 100;
        }
        .topbar .brand { font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
        
        /* Dropdown Container */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        .topbar .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .topbar .user-profile:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .topbar .user-profile .icon-avatar { font-size: 16px; }
        .topbar .user-profile .arrow { font-size: 10px; margin-left: 4px; transition: transform 0.2s; }
        
        /* Dropdown Content Menu */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 110%;
            background-color: #ffffff;
            min-width: 160px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .dropdown-menu a {
            color: #334155;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background-color 0.2s, color 0.2s;
        }
        .dropdown-menu a:hover {
            background-color: #f1f5f9;
            color: #4f46e5;
        }
        .dropdown-menu .menu-logout {
            border-top: 1px solid #e2e8f0;
            color: #dc2626;
        }
        .dropdown-menu .menu-logout:hover {
            background-color: #fef2f2;
            color: #b91c1c;
        }
        
        /* Tampilkan dropdown saat hover */
        .user-dropdown:hover .dropdown-menu { display: block; }
        .user-dropdown:hover .arrow { transform: rotate(180deg); }

        /* --- MAIN WORKSPACE --- */
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }

        /* --- BREADCRUMB NAVIGATION --- */
        .breadcrumb-box {
            background-color: #ffffff;
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            font-size: 14px;
            color: #64748b;
            align-items: center;
        }
        .breadcrumb-box a { color: #4f46e5; text-decoration: none; font-weight: 500; }
        .breadcrumb-box .separator { color: #cbd5e1; }
        .breadcrumb-box .current { font-weight: bold; color: #1e293b; }

        /* --- SPLIT LAYOUT CARD --- */
        .loan-card {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            padding: 30px;
        }

        .section-header {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .section-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 25px;
        }

        /* --- DETAIL TABLE STYLE --- */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background-color: #f8fafc;
            border-radius: 6px;
            overflow: hidden;
        }
        .info-table td {
            padding: 14px 20px;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-table tr:last-child td { border-bottom: none; }
        .info-table td.label {
            width: 30%;
            font-weight: bold;
            color: #475569;
        }
        .info-table td.value {
            color: #1e3a8a;
            font-weight: 600;
        }

        /* --- FORM ELEMENTS --- */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border 0.2s;
            color: #334155;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .form-control[readonly] {
            background-color: #e2e8f0;
            color: #64748b;
            cursor: not-allowed;
        }

        /* --- BUTTONS --- */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 30px;
        }
        .btn-submit {
            background-color: #16a34a;
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background-color 0.2s;
        }
        .btn-submit:hover { background-color: #15803d; }
        
        .btn-cancel {
            background-color: #64748b;
            color: #ffffff;
            text-decoration: none;
            padding: 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            width: 100%;
            text-align: center;
            transition: background-color 0.2s;
        }
        .btn-cancel:hover { background-color: #475569; }

        /* --- FOOTER BANNER --- */
        .footer {
            margin-top: 60px;
            border-top: 1px solid #e2e8f0;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #64748b;
            background-color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="brand">Perpustakaan Sekolah</div>
        <div class="user-dropdown">
            <div class="user-profile">
                <span class="icon-avatar">👤</span> 
                <span><?php echo htmlspecialchars($nama_tampil); ?></span>
                <span class="arrow">▼</span>
            </div>
         <div class="dropdown-menu">
    <a href="profil.php">👤 Profil Saya</a>
    <a href="pengaturan.php">⚙️ Pengaturan</a> 
    <a href="../auth/logout.php" class="menu-logout">🚪 Logout</a>
</div>
        </div>
    </div>

    <div class="container">
        
        <div class="breadcrumb-box">
            <span>🏠</span>
            <a href="dashboard.php">Kategori Buku 📋</a>
            <span class="separator">›</span>
            <a href="buku_tersedia.php">Rak Buku Tersedia 📚</a>
            <span class="separator">›</span>
            <a href="pengembalian.php">Pengembalian Buku ↩️</a>
            <span class="separator">›</span>
            <span class="current">Form Peminjaman 📝</span>
        </div>

        <div class="loan-card">
            <div class="section-header">📋 Konfirmasi Pengajuan Peminjaman</div>
            <div class="section-subtitle">Periksa kembali detail buku pilihan Anda sebelum menekan tombol konfirmasi di bawah.</div>

            <table class="info-table">
                <tr>
                    <td class="label">Judul Buku</td>
                    <td class="value">: <?php echo $buku['judul']; ?></td>
                </tr>
                <tr>
                    <td class="label">Penulis</td>
                    <td class="value">: <?php echo $buku['penulis']; ?></td>
                </tr>
                <tr>
                    <td class="label">Penerbit</td>
                    <td class="value">: <?php echo $buku['penerbit']; ?></td>
                </tr>
            </table>

            <form action="proses_pinjam.php" method="POST">
                <input type="hidden" name="id_buku" value="<?php echo $buku['id_buku']; ?>">
                
                <div class="form-group">
                    <label>📅 Tanggal Peminjaman</label>
                    <input type="date" name="tgl_pinjam" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>⚠️ Tanggal Batas Wajib Kembali</label>
                    <input type="date" name="tgl_wajib_kembali" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn-submit">✅ Ambil & Pinjam Buku</button>
                    <a href="buku_tersedia.php" class="btn-cancel">❌ Batalkan Proses</a>
                </div>
            </form>
        </div>

    </div>

    <div class="footer">
        <div>Sekolah © 2026</div>
        <div>Crafted with ❤️ by Perpustakaan</div>
    </div>

</body>
</html>