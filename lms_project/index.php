<?php 
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT password, role FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_store_result($stmt); // ⬅ WAJIB agar fetch aman

    mysqli_stmt_bind_result($stmt, $db_pass, $role);

    if (mysqli_stmt_num_rows($stmt) > 0) {

        mysqli_stmt_fetch($stmt);

        $match = false;

        if (substr($db_pass, 0, 4) === '$2y$') {
            if (password_verify($password, $db_pass)) {
                $match = true;
            }
        } else {
            if ($password === $db_pass) {
                $match = true;
            }
        }

        if ($match) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $role;

            header("Location: welcome.php");
            exit;
        } else {
            $error = 'Username atau Password salah!';
        }

    } else {
        $error = "Username atau Password salah!";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>LMS Kelompok 10</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    padding: 0;
    background: url('images/background.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    transition: opacity 0.6s ease;
}

/* TRANSISI HALAMAN */
body.fade-out {
    opacity: 0;
}

/* NAVBAR */
.navbar {
    width: 100%;
    padding: 15px 40px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.navbar-title {
    font-size: 20px;
    font-weight: 600;
}

/* BUTTON */
.open-login-btn {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    border: none;
    font-size: 16px;
    font-weight: 600;
}

/* LOGIN POPUP */
.login-modal {
    position: fixed;
    inset: 0;
    backdrop-filter: blur(10px);
    background: rgba(0,0,0,0.4);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: modalFade 0.5s ease forwards;
}

/* CARD LOGIN */
.login-card {
    width: 380px;
    padding: 30px;
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    text-align: center;
    color: white;
    animation: cardPop 0.55s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

/* LOGO ESTETIK + ANIMASI FLOAT */
.logo-estetik {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    padding: 8px;
    background: radial-gradient(circle, #ffffff, #dcdcdc);
    box-shadow:
        0 0 20px rgba(255,255,255,0.6),
        0 10px 25px rgba(0,0,0,0.4),
        inset 0 0 12px rgba(255,255,255,0.5);
    margin-bottom: 10px;
    animation: floating 3s infinite ease-in-out;
}

/* ANIMASI */
@keyframes modalFade {
    from { opacity: 0; backdrop-filter: blur(0px); }
    to { opacity: 1; backdrop-filter: blur(10px); }
}

@keyframes cardPop {
    0% { transform: scale(0.85) translateY(20px); opacity: 0; }
    60% { transform: scale(1.03) translateY(-4px); opacity: 1; }
    100% { transform: scale(1) translateY(0); }
}

@keyframes floating {
    0% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0); }
}

.input-field {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 10px;
    border: none;
    outline: none;
}

/* GLOW INPUT */
.input-field:focus {
    box-shadow: 0 0 12px rgba(0, 174, 255, 0.6);
    transition: 0.3s;
}

/* TOMBOL LOGIN */
.login-btn {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
}

.login-btn:active {
    transform: scale(0.95);
    filter: brightness(1.2);
}

.error {
    background: rgba(255,0,0,0.2);
    color: #ff4b4b;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 10px;
}

/* HERO */
.hero {
    text-align: center;
    padding: 140px 20px 80px 20px;
    color: white;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 8px;
}

.hero p {
    font-size: 20px;
    margin-bottom: 25px;
}

.learn-btn {
    display: inline-block;
    padding: 12px 28px;
    background: #1e90ff;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.3s ease;     /* animasi halus */
    box-shadow: 0px 4px 12px rgba(30,144,255,0.3);
}

.learn-btn:hover {
    background: #0b74e4;
    padding: 12px 32px;            /* efek melebar sedikit */
    box-shadow: 0px 6px 18px rgba(30,144,255,0.5);
    transform: translateY(-2px);   /* sedikit naik */
}

</style>
</head>

<body>

<div class="navbar">
    <div class="navbar-title">LMS Kelompok 10</div>
    <button id="openLoginBtn" class="open-login-btn">Login</button>
</div>

<!-- LOGIN MODAL -->
<div id="loginModal" class="login-modal">
    <div class="login-card">

        <img src="images/logo.png" class="logo-estetik">

        <h2>Login LMS</h2>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="text" name="username" class="input-field" placeholder="Masukkan Username" required>
            <input type="password" name="password" class="input-field" placeholder="Masukkan Password" required>
            <button type="submit" class="login-btn">Masuk</button>
        </form>

        <p style="margin-top:10px;">Belum punya akun? 
            <a href="register.php" style="color:#00c3ff;font-weight:600;">Daftar di sini</a>
        </p>
    </div>
</div>

<div class="hero">
   <h1>LMS Kelompok 10</h1>
<p>Learning Management System</p>

<a href="about_lms.php" class="learn-btn">Learn more</a>
</div>


<script>
document.getElementById("openLoginBtn").onclick = function() {
    document.getElementById("loginModal").style.display = "flex";
};

// Klik area luar = tutup modal
document.getElementById("loginModal").onclick = function(e) {
    if (e.target === this) {
        this.style.display = "none";
    }
};

// Efek loading pada tombol login
document.querySelector("form").addEventListener("submit", function() {
    const btn = document.querySelector(".login-btn");
    btn.innerHTML = "Memproses...";
    btn.style.opacity = "0.7";
    btn.style.pointerEvents = "none";
});
</script>

</body>
</html>
