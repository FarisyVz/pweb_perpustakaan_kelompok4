<?php
session_start();
// Proteksi halaman mahasiswa
if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menangkap data dari form pengembalian.php
    $id_pinjam          = mysqli_real_escape_string($koneksi, $_POST['id_pinjam']);
    $tgl_kembali_aktual = mysqli_real_escape_string($koneksi, $_POST['tgl_kembali']);
    $total_denda        = mysqli_real_escape_string($koneksi, $_POST['denda']);
    $hari_terlambat     = mysqli_real_escape_string($koneksi, $_POST['terlambat']);
    
    if (!empty($id_pinjam)) {
        
        // 🛑 VALIDASI UTAMA: Cek apakah id_pinjam ini sudah pernah dikembalikan sebelumnya
        $cek_log = mysqli_query($koneksi, "SELECT * FROM pengembalian WHERE id_pinjam = '$id_pinjam'");
        if (mysqli_num_rows($cek_log) > 0) {
            // Jika sudah ada di tabel pengembalian, langsung alihkan tanpa melakukan INSERT ulang
            header("Location: pengembalian.php?status=sukses");
            exit();
        }

        // 1. Ambil data tgl_pinjam dan id_buku dari tabel peminjaman
        $query_cari = "SELECT tgl_pinjam, id_buku FROM peminjaman WHERE id_pinjam = '$id_pinjam'";
        $res_cari   = mysqli_query($koneksi, $query_cari);
        $data_pinjam = mysqli_fetch_assoc($res_cari);
        
        $tgl_pinjam = $data_pinjam['tgl_pinjam'];
        $id_buku    = $data_pinjam['id_buku'];

        // Hitung selisih hari untuk kolom lama_pinjam
        $tgl1 = new DateTime($tgl_pinjam);
        $tgl2 = new DateTime($tgl_kembali_aktual);
        $jarak = $tgl2->diff($tgl1);
        $lama_pinjam = $jarak->days;

        // 2. INSERT data baru ke tabel pengembalian
        $query_insert_kembali = "INSERT INTO pengembalian (id_pinjam, tgl_kembali_aktual, lama_pinjam, hari_terlambat, total_denda) 
                                 VALUES ('$id_pinjam', '$tgl_kembali_aktual', '$lama_pinjam', '$hari_terlambat', '$total_denda')";
        
        if (mysqli_query($koneksi, $query_insert_kembali)) {
            
            // 3. UPDATE status di tabel peminjaman menjadi 'kembali'
            mysqli_query($koneksi, "UPDATE peminjaman SET status = 'kembali' WHERE id_pinjam = '$id_pinjam'");
            
            // 4. Menambahkan kembali stock_tersedia (+1) di tabel buku
            mysqli_query($koneksi, "UPDATE buku SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = '$id_buku'");
            
            // Berhasil! Kembalikan ke halaman form dengan status sukses
            header("Location: pengembalian.php?status=sukses");
            exit();
        } else {
            echo "Gagal menyimpan ke tabel pengembalian: " . mysqli_error($koneksi);
        }
    }
} else {
    header("Location: pengembalian.php");
    exit();
}
?>