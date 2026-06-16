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

// Ambil keyword pencarian jika ada
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $query_buku = "SELECT * FROM buku WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%' ORDER BY id_buku DESC";
} else {
    $query_buku = "SELECT * FROM buku ORDER BY id_buku DESC";
}
$result_buku = mysqli_query($koneksi, $query_buku);
$total_buku  = mysqli_num_rows($result_buku);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa | Metamedia Library</title>
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
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

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

        /* --- SEARCH BAR SECTION --- */
        .search-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .search-form { display: flex; gap: 10px; }
        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border 0.2s;
        }
        .search-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .btn-search {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 0 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            color: #334155;
            transition: all 0.2s;
        }
        .btn-search:hover { background-color: #e2e8f0; }

        /* --- SECTION TITLE --- */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        /* --- GRID CARDS CATALOGUE --- */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .book-card {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .book-cover-wrapper {
            background: #e2e8f0;
            height: 280px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #e2e8f0;
        }
        .book-cover-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .rak-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background-color: rgba(15, 23, 42, 0.75);
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            backdrop-filter: blur(4px);
        }

        .book-details { padding: 18px; flex: 1; display: flex; flex-direction: column; text-align: center; }
        .book-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e3a8a;
            margin: 0 0 6px 0;
            line-height: 1.4;
            min-height: 42px;
        }
        .book-subtitle { font-size: 12px; color: #64748b; margin: 0 0 15px 0; font-weight: 500; }

        .btn-pinjam {
            display: block;
            background-color: #4f46e5;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            margin-top: auto;
            transition: background-color 0.2s;
        }
        .btn-pinjam:hover { background-color: #3730a3; }
        .btn-habis {
            display: block;
            background-color: #cbd5e1;
            color: #64748b;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            margin-top: auto;
            cursor: not-allowed;
        }

        .book-meta-footer {
            border-top: 1px solid #f1f5f9;
            padding: 12px 18px;
            display: flex;
            justify-content: space-around;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            background-color: #fafafa;
        }
        .meta-item { display: flex; align-items: center; gap: 6px; }
        .meta-item.love-count { color: #94a3b8; }
        .meta-item.stock-count { color: #475569; }

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
            <span class="current">Kategori Buku 📋</span>
            <span class="separator">›</span>
            <a href="buku_tersedia.php">Rak Buku 📚</a>
            <span class="separator">›</span>
            <a href="pengembalian.php">Pengembalian Buku ↩️</a>
        </div>

        <div class="search-container">
            <form action="" method="GET" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Cari Judul Buku atau Penulis yang Anda inginkan..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">🔍</button>
            </form>
        </div>

        <div class="section-header">
            <span>📖</span> Buku (<?php echo $total_buku; ?>)
        </div>

        <div class="books-grid">
            <?php 
            if ($total_buku > 0) {
                while($buku = mysqli_fetch_assoc($result_buku)) {
                    $rak_dummy = ($buku['id_buku'] % 2 == 0) ? 'R-01' : 'R-02';
            ?>
            <div class="book-card">
                <div class="book-cover-wrapper">
                    <div class="rak-badge"><?php echo $rak_dummy; ?></div>
                    <img src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?auto=format&fit=crop&q=80&w=400" alt="Cover Buku">
                </div>

                <div class="book-details">
                    <h3 class="book-title"><?php echo $buku['judul']; ?></h3>
                    <div class="book-subtitle">Penunjang Pelajaran / Karya <?php echo $buku['penulis']; ?></div>
                    
                    <?php if($buku['stok_tersedia'] > 0): ?>
                        <a href="input_pinjam.php?id_buku=<?php echo $buku['id_buku']; ?>" class="btn-pinjam">📖 Pinjam Buku</a>
                    <?php else: ?>
                        <span class="btn-habis">❌ Stok Habis</span>
                    <?php endif; ?>
                </div>

                <div class="book-meta-footer">
                    <div class="meta-item love-count">
                        <span>❤️</span> <?php echo ($buku['id_buku'] * 2) % 7; ?>
                    </div>
                    <div class="meta-item stock-count">
                        <span>📇</span> Sisa Stok: <?php echo $buku['stok_tersedia']; ?>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<div style='grid-column: 1/-1; text-align:center; padding: 40px; color:#64748b;'>Katalog buku dengan kata kunci tersebut tidak ditemukan.</div>";
            }
            ?>
        </div>

    </div>

    <div class="footer">
        <div>Sekolah © 2026</div>
        <div>Crafted with ❤️ by Perpustakaan</div>
    </div>

</body>
</html>