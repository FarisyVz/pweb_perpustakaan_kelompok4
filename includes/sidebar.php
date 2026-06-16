<?php
// Memastikan session sudah berjalan untuk mendeteksi role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<style>
    .sidebar { width: 260px; background-color: #1e293b; color: #e2e8f0; padding: 25px 20px; display: flex; flex-direction: column; }
    .sidebar h3 { color: #fff; margin-bottom: 30px; text-align: center; font-size: 20px; border-bottom: 2px solid #334155; padding-bottom: 15px; }
    .sidebar a { color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 6px; margin-bottom: 8px; display: block; font-weight: 500; transition: all 0.3s; }
    .sidebar a:hover, .sidebar a.active { background-color: #334155; color: #fff; }
    .sidebar .menu-role { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 15px 0 5px 15px; font-weight: bold; }
    .sidebar .btn-logout { background-color: #ef4444; color: white; text-align: center; margin-top: auto; }
    .sidebar .btn-logout:hover { background-color: #dc2626; }
</style>

<div class="sidebar">
    <h3>E-Perpustakaan</h3>
    
    <?php if ($role === 'admin'): ?>
        <div class="menu-role">Menu Admin</div>
        <a href="../admin/dashboard.php">📊 Dashboard</a>
        <a href="../admin/data_buku.php">📚 Manajemen Buku</a>
        <a href="../admin/daftar_peminjam.php">🔄 Daftar Peminjam</a>
        <a href="../admin/laporan.php">🖨️ Menu Laporan</a>
        
    <?php elseif ($role === 'mahasiswa'): ?>
        <div class="menu-role">Menu Mahasiswa</div>
        <a href="../mahasiswa/dashboard.php">🏠 Dashboard</a>
        <a href="../mahasiswa/buku_tersedia.php">📖 Buku Tersedia</a>
        <a href="../mahasiswa/pengembalian.php">↩️ Kembalikan Buku</a>
    <?php endif; ?>
    
    <a href="../auth/logout.php" class="btn-logout">Keluar (Logout)</a>
</div>

<div class="main-content">
    <div class="top-bar">
        <span class="user-info">Login Sebagai: <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Tamu'; ?></span>
        <span style="color: #6c757d;"><?php echo date('d F Y'); ?></span>
    </div>