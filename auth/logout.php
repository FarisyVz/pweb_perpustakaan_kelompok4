<?php
// Memulai session
session_start();

// Menghapus semua variabel session
$_SESSION = array();

// Menghancurkan session yang berjalan
session_destroy();

// Mengarahkan kembali ke halaman login dengan pesan sukses logout
header("Location: login.php?pesan=logout");
exit();
?>