<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tentang LMS</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: url('images/background.jpg') no-repeat center center fixed;
        background-size: cover;
    }

    .container {
        max-width: 900px;
        margin: 60px auto;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(14px);
        color: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        animation: fadeInUp 1s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
    }

    h2 {
        margin-top: 30px;
        color: #cfe8ff;
    }

    p, li {
        font-size: 15px;
        line-height: 1.7;
    }

    ul {
        margin-left: 20px;
    }

    a.back-btn {
        display: inline-block;
        margin-top: 25px;
        padding: 10px 20px;
        background: #007bff;
        color: #fff;
        border-radius: 10px;
        text-decoration: none;
        transition: 0.3s;
    }

    a.back-btn:hover {
        background: #0056b3;
    }
</style>
</head>
<body>

<div class="container">

    <h1> Apa Itu Learning Management System (LMS)?</h1>
    <p>
        Learning Management System (LMS) adalah sebuah platform digital yang digunakan untuk 
        mengelola proses pembelajaran seperti pengumpulan tugas, ujian, materi kuliah, presensi, 
        komunikasi dosen–mahasiswa, dan evaluasi pembelajaran secara online.
    </p>

    <h2> Fitur-fitur Umum dalam LMS</h2>
    <ul>
        <li>📚 Pengelolaan materi kuliah</li>
        <li>📝 Pengumpulan tugas dan kuis</li>
        <li>📊 Penilaian dan laporan nilai</li>
        <li>💬 Forum diskusi</li>
        <li>🧑‍🏫 Manajemen kelas & pengguna</li>
        <li>📅 Kalender akademik</li>
    </ul>

    <h2> Cara Menggunakan LMS</h2>
    <ul>
        <li>Login menggunakan akun mahasiswa/dosen</li>
        <li>Pilih mata kuliah yang tersedia</li>
        <li>Akses materi, tugas, dan kuis</li>
        <li>Kumpulkan tugas melalui menu yang disediakan</li>
        <li>Ikuti forum diskusi atau pengumuman kelas</li>
        <li>Lihat nilai setelah dosen melakukan penilaian</li>
    </ul>

    <h2> Kelebihan LMS</h2>
    <ul>
        <li>Belajar fleksibel di mana saja</li>
        <li>Efisiensi administrasi kampus</li>
        <li>Materi terorganisir rapi</li>
        <li>Mengurangi penggunaan kertas</li>
        <li>Mempermudah komunikasi</li>
    </ul>

    <h2> Kekurangan LMS</h2>
    <ul>
        <li>Membutuhkan koneksi internet yang stabil</li>
        <li>Beberapa pengguna baru butuh penyesuaian</li>
        <li>Jika server bermasalah, akses terganggu</li>
    </ul>

    <h2>💡 Wawasan Tambahan</h2>
    <p>
        LMS kini menjadi standar di hampir semua institusi pendidikan dan perusahaan.  
        Sistem ini terus berkembang dengan fitur seperti integrasi AI, otomatisasi nilai, 
        video conference, hingga analytic learning yang dapat memprediksi performa siswa.
    </p>

    <a href="index.php" class="back-btn">⬅ Kembali ke Beranda</a>
</div>

</body>
</html>
