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

$query = "SELECT peminjaman.*, buku.judul 
          FROM peminjaman 
          JOIN buku ON peminjaman.id_buku = buku.id_buku 
          WHERE peminjaman.id_mahasiswa = '$id_mhs' AND peminjaman.status = 'dipinjam'";
$result = mysqli_query($koneksi, $query);

$list_pinjaman = [];
while ($row = mysqli_fetch_assoc($result)) {
    $list_pinjaman[] = $row;
}
$json_pinjaman = json_encode($list_pinjaman);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku | Metamedia Library</title>
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
        
        /* Tampilkan dropdown saat user memindahkan kursor (hover) */
        .user-dropdown:hover .dropdown-menu {
            display: block;
        }
        .user-dropdown:hover .arrow {
            transform: rotate(180deg);
        }

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

        /* --- ALERT NOTIFICATION --- */
        .alert-success {
            padding: 15px 20px;
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 600;
        }

        /* --- LOAN CARD FORM WORKSPACE --- */
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

        /* --- FORM ELEMENTS --- */
        .form-group {
            margin-bottom: 20px;
        }
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
            background-color: #ffffff;
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

        /* --- RESULT BOX CALCULATION --- */
        .result-box {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
        }
        .result-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px dotted #e2e8f0;
            color: #475569;
        }
        .result-line:last-of-type {
            border-bottom: none;
            font-size: 16px;
            padding-top: 12px;
            margin-top: 4px;
            font-weight: bold;
            color: #1e293b;
        }

        /* --- BUTTONS --- */
        .btn-submit {
            background-color: #10b981;
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
        .btn-submit:hover { background-color: #059669; }

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

    <!-- TOPBAR UTAMA DENGAN DROPDOWN PROFILE -->
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

    <!-- MAIN CONTAINER -->
    <div class="container">
        
        <!-- BREADCRUMBS -->
        <div class="breadcrumb-box">
            <span>🏠</span>
            <a href="dashboard.php">Kategori Buku 📋</a>
            <span class="separator">›</span>
            <a href="buku_tersedia.php">Rak Buku 📚</a>
            <span class="separator">›</span>
            <span class="current">Pengembalian Buku ↩️</span>
        </div>

        <!-- NOTIFIKASI SUKSES -->
        <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
            <div class="alert-success">
                📚 Buku berhasil dikembalikan! Data di dashboard sistem telah diperbarui secara langsung.
            </div>
        <?php endif; ?>

        <!-- FORM CARD WORKSPACE -->
        <div class="loan-card">
            <div class="section-header">↩️ Transaksi Pengembalian Buku Mandiri</div>
            <div class="section-subtitle">Sistem otomatis menghitung kalkulasi durasi serta akumulasi denda keterlambatan (Rp 1.000 / Hari).</div>

            <form action="proses_pengembalian.php" method="POST">
                
                <div class="form-group">
                    <label for="id_pinjam">📖 Pilih Judul Buku yang Dipinjam</label>
                    <select name="id_pinjam" id="id_pinjam" class="form-control" onchange="jalankanKalkulatorDenda()" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php foreach($list_pinjaman as $pinjam): ?>
                            <option value="<?php echo $pinjam['id_pinjam']; ?>"><?php echo $pinjam['judul']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>📅 Tanggal Pengembalian Hari Ini</label>
                    <input type="date" name="tgl_kembali" id="tgl_kembali" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>

                <!-- Input Hidden untuk Pengiriman ke Script PHP -->
                <input type="hidden" name="denda" id="input_denda" value="0">
                <input type="hidden" name="terlambat" id="input_terlambat" value="0">

                <!-- Elemen Penampung Sementara Data JS (Hidden) -->
                <div style="display: none;">
                    <span id="text_tgl_pinjam"></span>
                    <span id="text_tgl_wajib"></span>
                    <span id="text_lama_pinjam"></span>
                    <span id="text_terlambat"></span>
                    <span id="text_denda"></span>
                </div>

                <!-- Box Tampilan Rincian Hasil Kalkulasi -->
                <div id="box-kalkulasi" class="result-box" style="display: none;">
                    <div class="result-line">
                        <span>📅 Durasi Peminjaman</span>
                        <span id="view_lama_pinjam" style="font-weight: bold; color: #4f46e5;">0 Hari</span>
                    </div>
                    <div class="result-line">
                        <span>⚠️ Masa Keterlambatan</span>
                        <span id="view_terlambat" style="font-weight: bold; color: #dc2626;">0 Hari</span>
                    </div>
                    <div class="result-line">
                        <span>💰 Total Denda Administrasi</span>
                        <span id="view_denda" style="color: #dc2626;">Rp 0</span>
                    </div>
                </div>
                
                <button type="submit" id="btn-submit-form" class="btn-submit">✔️ Proses Pengembalian Buku</button>
            </form>
        </div>

    </div>

    <!-- FOOTER UTAMA -->
    <div class="footer">
        <div>Sekolah © 2026</div>
        <div>Crafted with ❤️ by Perpustakaan</div>
    </div>

    <!-- SCRIPT LIBRARY PERHITUNGAN -->
    <script src="../assets/js/hitung_denda.js"></script>
    <script>
        const dataPinjaman = <?php echo $json_pinjaman; ?>;

        function jalankanKalkulatorDenda() {
            const idPinjamTerpilih = document.getElementById('id_pinjam').value;
            const tglKembaliAktual = document.getElementById('tgl_kembali').value;
            
            if(idPinjamTerpilih === "") {
                document.getElementById('box-kalkulasi').style.display = 'none';
                return;
            }

            // Memanggil aset fungsi kalkulasi eksternal bawaan
            prosesHitungDenda(idPinjamTerpilih, dataPinjaman, tglKembaliAktual);
            
            // Mengambil data dari element jembatan text menuju layout visual baru
            setTimeout(() => {
                const txtDenda = document.getElementById('text_denda').innerText || "Rp 0";
                const txtTerlambat = document.getElementById('text_terlambat').innerText || "0";
                const txtLama = document.getElementById('text_lama_pinjam').innerText || "0";

                // Menempelkan nilai ke panel informasi visual
                document.getElementById('view_lama_pinjam').innerText = txtLama + " Hari";
                document.getElementById('view_terlambat').innerText = txtTerlambat + " Hari";
                document.getElementById('view_denda').innerText = txtDenda;

                // Memisahkan karakter non-angka untuk dimasukkan ke variabel element form hidden
                const angkaDenda = txtDenda.replace(/[^0-9]/g, '');
                document.getElementById('input_denda').value = angkaDenda || 0;
                document.getElementById('input_terlambat').value = txtTerlambat || 0;

                // Tampilkan box rincian kalkulasi denda
                document.getElementById('box-kalkulasi').style.display = 'block';
            }, 80);
        }
    </script>
</body>
</html>