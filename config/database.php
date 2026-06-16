<?php
// Pengaturan Konfigurasi Database Perpustakaan
$host     = "localhost";     // Nama host server database (default: localhost)
$username = "root";          // Username database default XAMPP/AppServ
$password = "";              // Password database default XAMPP (biasanya kosong)
$database = "perpustakaan_db"; // Nama database yang disesuaikan dengan skrip SQL sebelumnya

// Membuat koneksi ke database menggunakan fungsi mysqli_connect
$koneksi = mysqli_connect($host, $username, $password, $database);

// Melakukan pengecekan apakah koneksi berhasil atau gagal
if (!$koneksi) {
    // Jika koneksi gagal, hentikan program dan tampilkan pesan error-nya
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Mengatur charset bawaan menjadi utf8mb4 agar mendukung penyimpanan karakter yang luas
mysqli_set_charset($koneksi, "utf8mb4");

// Variabel $koneksi inilah yang akan dipanggil di file lain menggunakan fungsi 'include' atau 'require'
?>