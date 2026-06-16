<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Mahasiswa</title>
    <style>
        body { 
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #0ea5e9 50%, #14b8a6 100%);
            font-family: sans-serif; color: white;
        }
        .reg-card { 
            width: 450px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px);
            padding: 30px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 14px; }
        input, select { 
            width: 100%; padding: 10px; border-radius: 8px; border: none; box-sizing: border-box;
        }
        button { 
            width: 100%; padding: 12px; background: #0d9488; color: white; 
            border: none; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px;
        }
        select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background: rgba(255, 255, 255, 0.9); /* Sesuai dengan input lainnya */
    color: #333;
    cursor: pointer;
}
    </style>
</head>
<body>
    <div class="reg-card">
        <h2>Registrasi Mahasiswa</h2>
        <form action="proses_register.php" method="POST">
            <div class="form-group"><label>NIM</label><input type="text" name="nim" required></div>
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <div class="form-group">
    <label>Jurusan</label>
    <select name="jurusan" required>
        <option value="" disabled selected>-- Pilih Jurusan --</option>
        <option value="Sistem Informasi">Sistem Informasi</option>
        <option value="Informatika">Informatika</option>
        <option value="Bisnis Digital">Bisnis Digital</option>
        <option value="Desain Visual Komunikasi">Desain Visual Komunikasi (DVK)</option>
        <option value="Manajemen Ritel">Manajemen Ritel</option>
        <option value="Pendidikan Teknologi Informasi">Pendidikan Teknologi Informasi</option>
    </select>
</div>
            <div class="form-group"><label>Angkatan</label><input type="number" name="angkatan" required></div>
            <div class="form-group"><label>No HP</label><input type="text" name="no_hp" required></div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jk">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <button type="submit">Daftar Sekarang</button>
        </form>
    </div>
</body>
</html>