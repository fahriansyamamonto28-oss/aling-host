<?php
include "db.php";
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Cek apakah username sudah digunakan
    $check = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check, "s", $username);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);
    if (mysqli_num_rows($result) > 0) {
        $message = "<span style='color:#ff6b6b;'>❌ Username sudah digunakan.</span>";
    } else {
        // Simpan user baru
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role, foto) VALUES (?, ?, ?, ?)");
        $default_foto = "default-avatar.png";
        mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $role, $default_foto);
        mysqli_stmt_execute($stmt);
        $message = "<span style='color:#28a745;'>✅ Registrasi berhasil! Silakan login.</span>";
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Akun Baru</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: url('images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        overflow: hidden;
    }

    .register-container {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(14px);
        padding: 40px;
        border-radius: 20px;
        width: 360px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        text-align: center;
        color: white;
        animation: fadeInUp 1s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ★★ LOGO ESTETIK ★★ */
    .logo-estetik {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;

        /* Glow lembut */
        box-shadow:
            0 0 15px rgba(255,255,255,0.5),
            0 0 40px rgba(0,123,255,0.4);

        /* Lingkaran luar */
        padding: 6px;
        background: linear-gradient(135deg, #ffffff 0%, #dce6ff 50%, #6aa8ff 100%);

        /* Animasi masuk */
        animation: popIn 1.1s ease;
        margin-bottom: 12px;
    }

    @keyframes popIn {
        0% { opacity: 0; transform: scale(0.7); }
        100% { opacity: 1; transform: scale(1); }
    }

    h2 {
        margin-bottom: 10px;
        color: #fff;
        font-weight: 600;
        font-size: 23px;
    }

    .msg {
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 500;
    }

    input, select, button {
        width: 100%;
        padding: 11px;
        margin: 8px 0;
        border-radius: 10px;
        border: none;
        outline: none;
        font-size: 14px;
    }

    input, select {
        background: rgba(255, 255, 255, 0.9);
        color: #111;
    }

    button {
        background-color: #007bff;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    a {
        color: #eee;
        text-decoration: none;
        font-size: 13px;
        margin-top: 12px;
        display: block;
        transition: 0.3s;
    }

    a:hover {
        text-decoration: underline;
        color: #fff;
    }
</style>
</head>
<body>

<div class="register-container">

    <!-- LOGO ESTETIK BARU -->
    <img src="images/logo.png" class="logo-estetik">

    <h2>Daftar Akun Baru</h2>

    <div class="msg"><?= $message ?></div>

    <form method="POST">
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <input type="password" name="password" placeholder="Masukkan Password" required>

        <select name="role" required>
            <option value="">-- Pilih Peran --</option>
            <option value="mahasiswa">Mahasiswa</option>
            <option value="dosen">Dosen</option>
        </select>

        <button type="submit">Daftar</button>

        <a href="index.php">Sudah punya akun? Login</a>
    </form>
</div>

</body>
</html>
