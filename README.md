# pweb_perpustakaan_kelompok3
📚 Metamedia Library Management System
Metamedia Library adalah sistem informasi manajemen perpustakaan berbasis web yang dibangun untuk mempermudah administrasi perpustakaan, mulai dari pengelolaan data anggota, inventaris buku, hingga pencatatan transaksi peminjaman dan pengembalian yang terintegrasi.

🚀 Fitur Utama
Sistem ini dilengkapi dengan berbagai fitur untuk mendukung operasional perpustakaan:

Dashboard Admin: Ringkasan statistik perpustakaan secara real-time.

Manajemen Anggota: CRUD data mahasiswa/anggota perpustakaan.

Inventaris Buku: Pengelolaan data buku dengan kategori yang terorganisir.

Sistem Transaksi:

Peminjaman buku yang efisien.

Pengembalian buku dengan pencatatan tanggal aktual.

Perhitungan denda otomatis untuk keterlambatan.

Laporan Komprehensif:

Laporan data peminjaman (bulanan/tahunan).

Laporan pengembalian buku.

Laporan pendapatan denda.

Statistik buku terpopuler berdasarkan frekuensi peminjaman.

Export Data: Kemudahan ekspor laporan ke dalam format Microsoft Excel.

🛠️ Teknologi yang Digunakan
Backend: PHP (Native)

Database: MySQL

Frontend: HTML5, CSS3 (Custom Responsive Design)

Server: XAMPP (Apache & MySQL)

🏗️ Struktur Proyek
Plaintext
/admin
  ├── daftar_peminjam.php      # Laporan Peminjaman
  ├── lap_kembali.php          # Laporan Pengembalian
  ├── lap_denda.php            # Laporan Denda
  ├── lap_buku_terpopuler.php  # Statistik Buku
  └── ... (file lainnya)
/config
  └── database.php             # Konfigurasi koneksi ke database
/auth
  └── login.php                # Sistem otentikasi admin
⚙️ Cara Instalasi
Clone repositori ini:

Bash
git clone https://github.com/username/metamedia-library.git
Siapkan Database:

Buka phpMyAdmin.

Buat database baru (misal: db_perpustakaan).

Impor file .sql yang ada di dalam folder proyek.

Konfigurasi Koneksi:

Buka file config/database.php.

Sesuaikan hostname, username, password, dan database sesuai dengan environment lokal Anda.

Jalankan:

Pindahkan folder proyek ke dalam direktori htdocs di XAMPP Anda.

Jalankan Apache dan MySQL melalui XAMPP Control Panel.

Akses via browser di https://github.com/FarisyVz/pweb_perpustakaan_kelompok3.

🤝 Kontribusi
Proyek ini dikembangkan untuk kebutuhan akademik/perpustakaan. Saran dan perbaikan kode sangat diterima. Silakan fork repositori ini dan buat pull request untuk kontribusi Anda.

📝 Lisensi
Proyek ini bersifat Open Source dan dapat digunakan untuk kepentingan pembelajaran.
