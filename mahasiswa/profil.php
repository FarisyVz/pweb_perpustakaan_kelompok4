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

$id_mhs = $_SESSION['id_user'];

// Mengambil data profil dari tabel mahasiswa (sesuaikan nama tabel/field jika perlu)
$query = "SELECT * FROM mahasiswa WHERE id_mahasiswa = '$id_mhs'";
$result = mysqli_query($koneksi, $query);
$mhs = mysqli_fetch_assoc($result);

// Fallback jika data belum lengkap di tabel
$nama = $_SESSION['nama'];
$nim = $mhs['nim'] ?? 'Belum Diatur';
$angkatan = $mhs['angkatan'] ?? 'Belum Diatur';
$status = $mhs['status'] ?? 'Aktif';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | Metamedia Library</title>
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
        
        .user-dropdown { position: relative; display: inline-block; }
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
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 110%;
            background-color: #ffffff;
            min-width: 160px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .dropdown-menu a { color: #334155; padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; }
        .dropdown-menu a:hover { background-color: #f1f5f9; color: #4f46e5; }
        .user-dropdown:hover .dropdown-menu { display: block; }

        /* --- MAIN CONTENT --- */
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        
        .profile-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .avatar-circle {
            width: 100px;
            height: 100px;
            background-color: #e0e7ff;
            color: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px auto;
        }
        
        .profile-table { width: 100%; margin-top: 30px; text-align: left; }
        .profile-table td { padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; font-size: 14px; width: 40%; }
        .value { color: #1e293b; font-weight: 600; font-size: 15px; }

        .btn-back {
            display: inline-block;
            margin-top: 30px;
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="brand">Perpustakaan Sekolah</div>
        <div class="user-dropdown">
            <div class="user-profile">
                <span>👤<?php echo htmlspecialchars($nama_tampil); ?></span>
            </div>
           <div class="dropdown-menu">
    <a href="profil.php">👤 Profil Saya</a>
    <a href="pengaturan.php">⚙️ Pengaturan</a> 
    <a href="../auth/logout.php" class="menu-logout">🚪 Logout</a>
</div>
        </div>
    </div>

    <div class="container">
        <div class="profile-card">
            <div class="avatar-circle">👤</div>
            <h2 style="margin-bottom: 5px;"><?php echo $nama; ?></h2>
            <p style="color: #64748b; font-size: 14px;">Mahasiswa Perpustakaan</p>

            <table class="profile-table">
                <tr>
                    <td class="label">Nama Mahasiswa</td>
                    <td class="value">: <?php echo htmlspecialchars($nama_tampil); ?></td>
                </tr>
                <tr>
                    <td class="label">NIM</td>
                    <td class="value">: <?php echo $nim; ?></td>
                </tr>
                <tr>
                    <td class="label">Angkatan</td>
                    <td class="value">: <?php echo $angkatan; ?></td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td class="value">: 
                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            <?php echo $status; ?>
                        </span>
                    </td>
                </tr>
            </table>

            <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>
        </div>
    </div>

</body>
</html>