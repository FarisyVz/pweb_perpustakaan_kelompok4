<?php
session_start();
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    header("Location: " . ($_SESSION['role'] == 'admin' ? "../admin/dashboard.php" : "../mahasiswa/dashboard.php"));
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Anggota</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', sans-serif;
        /* Gradasi 3 warna: Biru, Sky, dan Aqua yang lembut */
        background: linear-gradient(135deg, #1e3a8a 0%, #0ea5e9 50%, #14b8a6 100%);
        background-attachment: fixed;
    }

    /* 2. Efek Glassmorphism pada Card */
    .login-card {
        width: 400px;
        background: rgba(255, 255, 255, 0.15); /* Transparan */
        backdrop-filter: blur(15px);           /* Efek kaca buram */
        -webkit-backdrop-filter: blur(15px);
        padding: 40px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        text-align: center;
        color: #ffffff;
    }

    /* 3. Penyesuaian Elemen di dalam Card */
    .form-input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: none;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.9);
        color: #000000;
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: #ffffff; /* Warna tegas untuk tombol */
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-login:hover {
        background: #1a978f;
    }

    .extra-links a, .register-link a {
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9em;
    }

        .logo-top {
            width: 60px;
            margin-bottom: 20px;
        }

        .info-text {
            font-size: 14px;
            color: #ffffff;
            margin-bottom: 25px;
            line-height: 1.4;
        }

        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }

        .label {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 5px;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ffffff;
            border-radius: 2px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .checkbox-group {
            text-align: left;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
            background-color: #0ea5e9; /* Biru cerah sesuai gambar */
            color: white;
            border: none;
            padding: 10px;
            border-radius: 2px;
            font-weight: bold;
            cursor: pointer;
        }

        .extra-links {
            margin-top: 15px;
            font-size: 13px;
            text-align: left;
        }

        .extra-links a { color: #ffffff; text-decoration: none; }
        
        .register-link {
            margin-top: 30px;
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png" class="logo-top" alt="Logo">
        
        <p class="info-text">Silakan masukkan Username/Nim dan Password anda untuk login kembali</p>

        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <label class="label">Username/Nim</label>
                <input type="text" name="username" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="label">Passwrod</label>
                <input type="password" name="password" class="form-input" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="ingat"> <label for="ingat">Ingat Saya</label>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
            
            <div class="extra-links">
                <a href="#">Lupa sandi?</a>
            </div>
        </form>

        <div class="register-link">
            <a href="register.php" style="color:white; text-decoration:none;">Belum punya akun? Daftar sekarang!</a>
        </div>
    </div>

</body>
</html>