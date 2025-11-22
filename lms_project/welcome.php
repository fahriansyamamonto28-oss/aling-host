<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['user'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Selamat Datang di LMS</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        height: 100vh;
        font-family: 'Poppins', sans-serif;
        background: url('images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        color: #fff;
    }

    .welcome-box {
        text-align: center;
        background: rgba(255, 255, 255, 0.08);
        padding: 50px 40px;
        border-radius: 25px;
        backdrop-filter: blur(15px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        width: 480px;
        animation: fadeIn 1.2s ease-in-out;
    }

    h1 {
        font-size: 30px;
        margin-bottom: 10px;
        animation: fadeSlide 0.8s ease-in-out;
    }

    p {
        font-size: 17px;
        color: #e0e0e0;
        margin-bottom: 30px;
        animation: fadeSlide 0.8s ease-in-out;
    }

    .btn-next, .btn-dashboard {
        background: #007bff;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-next:hover, .btn-dashboard:hover {
        background: #0056b3;
        transform: scale(1.05);
        box-shadow: 0 0 12px rgba(0,123,255,0.6);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeSlide {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-out {
        opacity: 0;
        transform: translateY(-15px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
</style>
</head>

<body>
    <div class="welcome-box" id="welcomeBox">
        <h1 id="title">Selamat Datang, <?= htmlspecialchars($user); ?> 👋</h1>
        <p id="text">Terima kasih telah bergabung di <b>LMS Sederhana</b>. Mari mulai perjalanan belajarmu hari ini!</p>
        <button class="btn-next" id="nextBtn" onclick="nextMessage()">Lanjut</button>
    </div>

    <script>
        const messages = [
            {
                title: "Selamat Datang, <?= htmlspecialchars($user); ?> 👋",
                text: "Terima kasih telah bergabung di <b>LMS Sederhana</b>. Mari mulai perjalanan belajarmu hari ini!"
            },
            {
                title: "📚 Platform Belajar Cerdas",
                text: "Di sini kamu bisa mengakses materi, kuis, dan aktivitas pembelajaran secara online dengan mudah."
            },
            {
                title: "🌟 Ayo Mulai Sekarang!",
                text: "Jelajahi fitur-fitur menarik di dashboard dan tingkatkan semangat belajarmu!"
            }
        ];

        let current = 0;
        const title = document.getElementById("title");
        const text = document.getElementById("text");
        const nextBtn = document.getElementById("nextBtn");
        const box = document.getElementById("welcomeBox");

        function nextMessage() {
            box.classList.add("fade-out");
            setTimeout(() => {
                current++;
                if (current < messages.length) {
                    title.innerHTML = messages[current].title;
                    text.innerHTML = messages[current].text;
                    box.classList.remove("fade-out");
                } else {
                    title.innerHTML = "🎉 Siap Belajar!";
                    text.innerHTML = "Kamu akan diarahkan ke dashboard utama.";
                    nextBtn.innerText = "Masuk ke Dashboard";
                    nextBtn.onclick = goToDashboard;
                    box.classList.remove("fade-out");
                }
            }, 600);
        }

        function goToDashboard() {
            box.classList.add("fade-out");
            setTimeout(() => { window.location.href = 'dashboard.php'; }, 1000);
        }
    </script>
</body>
</html>
