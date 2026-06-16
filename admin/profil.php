<?php
session_start();
// 1. Proteksi Halaman
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit();
}
include '../config/database.php';

$id_user = $_SESSION['id_user'];

// 2. Ambil data admin terbaru dari database (Sesuaikan nama tabel & kolom Anda)
// Contoh di bawah berasumsi ada tabel 'users' atau 'admin'
$query = "SELECT * FROM admin WHERE id_admin = '$id_user'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Akun | Universitas Metamedia</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="width: 100%; padding: 40px; position: relative; z-index: 1;">
    <!-- NAVBAR HORIZONTAL DENGAN PROFILE DROPDOWN -->
    <nav>
        <div class="brand">Perpustakaan <span>Metamedia</span></div>
        <div class="menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="data_buku.php">📚 Manajemen Buku</a>
            <a href="daftar_peminjam.php">👥 Daftar Peminjam</a>
            
            <div class="user-profile-dropdown">
                <button onclick="toggleProfileDropdown()" class="profile-trigger" id="profileBtn">
                    👤 <?php echo explode(' ', $_SESSION['nama'])[0]; ?> ▾
                </button>
                <div id="profileMenu" class="profile-dropdown-content">
                    <div class="user-meta">
                        <span class="name"><?php echo $_SESSION['nama']; ?></span>
                        <span class="role">🛡️ Administrator</span>
                    </div>
                    <a href="profil.php" class="active">⚙️ Profil Akun</a>
                    <a href="../auth/logout.php" class="logout-item">🚪 Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <h2>Pengaturan Profil & Keamanan</h2>
    <p style="color: #475569; margin-top: -15px; margin-bottom: 30px;">Kelola informasi data diri dan amankan kredensial akun Anda.</p>

    <!-- NOTIFIKASI JIKA ADA PESAN DARI PROSES UPDATE -->
    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'sukses_update'): ?>
        <div style="padding: 14px 20px; background-color: rgba(16, 185, 129, 0.15); color: #047857; border-radius: 8px; margin-bottom: 25px; font-weight: 600; font-size: 14px; border-left: 4px solid #10b981;">
            ✓ Kredensial keamanan akun berhasil diperbarui!
        </div>
    <?php endif; ?>

    <div class="row" style="gap: 30px; align-items: flex-start;">
        <!-- KARTU INFORMASI DATA DIRI -->
        <div class="form-container" style="flex: 1; background: rgba(255, 255, 255, 0.94); margin-top: 0;">
            <h3 style="color: #0f172a; margin-bottom: 20px; font-weight: 700;">📋 Informasi Akun</h3>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" value="<?php echo $_SESSION['nama']; ?>" readonly style="background-color: #f8fafc; color: #64748b;">
            </div>
            <div class="form-group">
                <label>Username / ID Sistem</label>
                <input type="text" value="<?php echo $data['username'] ?? 'admin_metamedia'; ?>" readonly style="background-color: #f8fafc; color: #64748b;">
            </div>
            <div class="form-group">
                <label>Otoritas Akses (*Role)</label>
                <input type="text" value="Administrator Utama" readonly style="background-color: #f8fafc; color: #10b981; font-weight: 700;">
            </div>
        </div>

        <!-- FORMULIR UPDATE PASSWORD -->
        <div class="form-container" style="flex: 1; background: rgba(255, 255, 255, 0.94); margin-top: 0;">
            <h3 style="color: #0f172a; margin-bottom: 20px; font-weight: 700;">🔒 Perbarui Password</h3>
            <form action="proses_update_password.php" method="POST">
                <div class="form-group">
                    <label for="pass_lama">Password Saat Ini</label>
                    <input type="password" id="pass_lama" name="password_lama" required placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label for="pass_baru">Password Baru</label>
                    <input type="password" id="pass_baru" name="password_baru" required placeholder="Minimal 6 karakter">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; background-color: #0284c7;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleProfileDropdown() {
        document.getElementById("profileMenu").classList.toggle("show-profile");
    }

    window.onclick = function(event) {
        if (!event.target.matches('#profileBtn') && !event.target.parentNode.matches('#profileBtn')) {
            var dropdowns = document.getElementsByClassName("profile-dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show-profile')) {
                    openDropdown.classList.remove('show-profile');
                }
            }
        }
    }
</script>
</body>
</html>