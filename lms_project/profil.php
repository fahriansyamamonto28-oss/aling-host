<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['user'];
$role = $_SESSION['role'];

// Ambil data pengguna
$stmt = mysqli_prepare($conn, "SELECT username, password, foto FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $user, $hashed_password, $foto);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$foto_path = !empty($foto) ? "uploads/foto/" . $foto : "uploads/foto/default-avatar.png";
$message = "";

// Proses update profil
if (isset($_POST['update'])) {
    $new_username = trim($_POST['username']);
    $old_password_input = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $foto_name = $foto;

    // Cek password lama sebelum ubah
    if (!password_verify($old_password_input, $hashed_password)) {
        $message = "<span style='color:#ff4d4d;'>❌ Password lama salah. Perubahan dibatalkan.</span>";
    } else {
        // Upload foto baru jika ada
        if (!empty($_FILES['foto']['name'])) {
            $target_dir = "uploads/foto/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            
            $foto_name = time() . "_" . basename($_FILES["foto"]["name"]);
            $target_file = $target_dir . $foto_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (in_array($imageFileType, $allowed)) {
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    // Hapus foto lama jika bukan default
                    if ($foto && file_exists($target_dir . $foto) && $foto != "default-avatar.png") {
                        unlink($target_dir . $foto);
                    }
                }
            }
        }

        // Hash password baru jika diisi
        if (!empty($new_password)) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        } else {
            $hashed_new_password = $hashed_password;
        }

        // Update ke database
        $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, password=?, foto=? WHERE username=?");
        mysqli_stmt_bind_param($stmt, "ssss", $new_username, $hashed_new_password, $foto_name, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update session
        $_SESSION['user'] = $new_username;

        $message = "<span style='color:#00ff99;'>✅ Profil berhasil diperbarui!</span>";
        header("Refresh:1");
    }
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Pengguna</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: url('images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .profil-container {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        padding: 40px;
        border-radius: 20px;
        width: 400px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        text-align: center;
    }

    img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid #fff;
        object-fit: cover;
    }

    h2 {
        margin: 15px 0 10px;
        font-size: 24px;
    }

    .info {
        margin-bottom: 15px;
        color: #ddd;
    }

    input[type="text"],
    input[type="password"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 10px;
        border: none;
        outline: none;
    }

    button {
        background: #007bff;
        border: none;
        padding: 10px 20px;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        margin: 8px 5px;
        transition: 0.3s;
        font-weight: 600;
    }

    button:hover {
        background: #0056b3;
    }

    .logout-btn {
        background: #dc3545;
    }

    .logout-btn:hover {
        background: #b02a37;
    }

    .message {
        margin: 10px 0;
        font-weight: 500;
    }

    a {
        color: #fff;
        text-decoration: none;
    }
</style>
</head>
<body>

<div class="profil-container">
    <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto Profil">
    <h2><?= htmlspecialchars($user); ?></h2>
    <p class="info">Sebagai : <?= htmlspecialchars($role); ?></p>

    <div class="message"><?= $message ?></div>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="username" value="<?= htmlspecialchars($user); ?>" placeholder="Username baru" required>
        <input type="password" name="old_password" placeholder="Masukkan password lama" required>
        <input type="password" name="new_password" placeholder="Password baru (opsional)">
        <input type="file" name="foto" accept="image/*">
        <button type="submit" name="update">💾 Simpan Perubahan</button>
        <a href="dashboard.php"><button type="button">⬅️ Kembali</button></a>
    </form>

    <form method="POST" style="margin-top:15px;">
        <button type="submit" name="logout" class="logout-btn">🚪 Logout</button>
    </form>
</div>

</body>
</html>
