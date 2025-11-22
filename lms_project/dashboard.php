<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
include "db.php";

// === Ambil data user untuk profil mini ===
$username = $_SESSION['user'];
$stmt = mysqli_prepare($conn, "SELECT foto FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $foto);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$foto_path = (!empty($foto) && file_exists("uploads/foto/" . $foto))
    ? "uploads/foto/" . htmlspecialchars($foto)
    : "uploads/foto/default-avatar.png";

$maxFileSize = 5 * 1024 * 1024; // 5 MB
$allowed_ext = ['pdf', 'ppt', 'pptx', 'doc', 'docx'];
$messages = [];

// === Proses Upload File (khusus dosen) ===
if (isset($_POST['upload'])) {
    if ($_SESSION['role'] !== 'dosen') {
        $messages[] = "❌ Akses upload ditolak: hanya dosen yang boleh upload.";
    } else {
        if (!isset($_FILES['materi']) || $_FILES['materi']['error'] !== UPLOAD_ERR_OK) {
            $messages[] = "⚠️ Tidak ada file yang dipilih atau terjadi error upload.";
        } else {
            $file = $_FILES['materi'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                $messages[] = "❌ Format file tidak diizinkan! (PDF, PPT, DOC, DOCX)";
            } elseif ($file['size'] > $maxFileSize) {
                $messages[] = "🚫 Ukuran file terlalu besar! Maksimal 5 MB.";
            } else {
                $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($file['name']));
                $newname = time() . "_" . $safeName;
                $target_dir = "uploads/";
                $target_file = $target_dir . $newname;

                // Pastikan folder upload ada
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $uploader = $_SESSION['user'];
                    $tanggal = date('Y-m-d H:i:s');

                    // Simpan ke tabel materi
                    $stmt = mysqli_prepare($conn, "INSERT INTO materi (nama_file, uploader, tanggal_upload) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "sss", $newname, $uploader, $tanggal);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Catat ke log aktivitas
                    $aktivitas = "Mengupload file: " . $newname;
                    $stmt2 = mysqli_prepare($conn, "INSERT INTO log_activity (username, aktivitas, waktu) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt2, "sss", $uploader, $aktivitas, $tanggal);
                    mysqli_stmt_execute($stmt2);
                    mysqli_stmt_close($stmt2);

                    $messages[] = "✅ File <b>{$safeName}</b> berhasil diupload!";
                } else {
                    $messages[] = "❌ Gagal memindahkan file ke folder upload.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard LMS</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
   
/* Semua container presensi */
.container {
    position: relative;
    z-index: 1;
}

/* Bagian tugas harus berada di atas presensi */
#tugas-container,
#assignments-section {
    position: relative;
    z-index: 10;
}

/* Perbaiki container presensi (supaya tidak menutupi bawah) */
.presensi-box {
    height: fit-content !important;
    overflow: visible !important;
}


    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: url('images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        min-height: 100vh;
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0, 0, 0, 0.55);
        padding: 12px 30px;
        backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .header-left h2 {
        margin: 0;
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .profile-mini {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,0.12);
        padding: 6px 14px;
        border-radius: 40px;
        transition: 0.3s;
        text-decoration: none;
        color: #fff;
    }

    .profile-mini:hover {
        background: rgba(255,255,255,0.25);
    }

    .profile-mini img {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
    }

    .container {
        margin: 60px auto;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(18px);
        border-radius: 20px;
        padding: 45px;
        width: 90%;
        max-width: 850px;
        box-shadow: 0 12px 35px rgba(0,0,0,0.35);
        text-align: center;
        transition: 0.5s;
    }

    h1 {
        font-size: 28px;
        margin-bottom: 5px;
        text-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    p {
        color: #e0e0e0;
        font-size: 16px;
        margin-bottom: 25px;
    }

    .msg {
        background: rgba(255,255,255,0.85);
        color: #111;
        margin: 10px 0;
        padding: 10px;
        border-radius: 8px;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        animation: fadeIn 0.5s ease;
    }

    .form-box {
        background: rgba(255,255,255,0.18);
        border-radius: 15px;
        padding: 25px;
        margin-top: 25px;
        color: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    input[type="file"] {
        margin: 12px 0;
        padding: 10px;
        border-radius: 8px;
        background: rgba(255,255,255,0.9);
        border: none;
        width: 80%;
        color: #111;
        font-size: 14px;
    }

    button {
        background: linear-gradient(90deg, #007bff, #56ccf2);
        color: white;
        border: none;
        padding: 10px 22px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,123,255,0.3);
    }

    button:hover {
        transform: scale(1.05);
        background: linear-gradient(90deg, #0056b3, #2f80ed);
    }

    .file-list {
        margin-top: 35px;
        text-align: left;
        background: rgba(255,255,255,0.18);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        animation: fadeIn 1s ease;
    }

    .file-list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .file-list li {
        background: rgba(255,255,255,0.85);
        color: #111;
        margin-bottom: 12px;
        padding: 12px 15px;
        border-radius: 10px;
        transition: 0.3s;
    }

    .file-list li:hover {
        transform: translateX(4px);
        background: rgba(245,245,245,1);
    }

    .file-list a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .file-list a:hover {
        text-decoration: underline;
    }

    .btn-logout {
        display: inline-block;
        background: #dc3545;
        color: white;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 8px;
        margin-top: 25px;
        transition: 0.3s;
    }

    .btn-logout:hover {
        background: #b02a37;
    }
</style>
</head>

<body>
<header>
    <div class="header-left">
        <h2>LMS Dashboard</h2>
    </div>
    <a href="profil.php" class="profile-mini">
        <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto Profil">
        <span><?= htmlspecialchars($_SESSION['user']); ?></span>
    </a>
</header>

<div class="container">
    <h1>Halo, <?= htmlspecialchars($_SESSION['user']); ?> 👋</h1>
    <p>Selamat datang di sistem pembelajaran LMS!<br>Anda login sebagai <b><?= htmlspecialchars($_SESSION['role']); ?></b>.</p>

    <?php foreach ($messages as $m): ?>
        <div class="msg"><?= $m; ?></div>
    <?php endforeach; ?>

    <?php if ($_SESSION['role'] === 'dosen'): ?>
    <div class="form-box">
        <h3>📘 Upload Materi</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="materi" required>
            <br>
            <button type="submit" name="upload">Upload</button>
        </form>
        <small>Format: PDF, PPT, DOC, DOCX — Maks 5 MB</small>
    </div>
    <?php endif; ?>

    <div class="file-list">
        <h3>📂 Daftar Materi</h3>
        <ul>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM materi ORDER BY tanggal_upload DESC");
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $id = $row['id'];
                    $fname = htmlspecialchars($row['nama_file']);
                    $uploader = htmlspecialchars($row['uploader']);
                    $waktu = htmlspecialchars($row['tanggal_upload']);

                    echo "<li>";
                    echo "<a href='uploads/{$fname}' target='_blank'>{$fname}</a><br>";
                    echo "<small>Diupload oleh: {$uploader} pada {$waktu}</small>";

                    if ($_SESSION['role'] === 'dosen') {
                        echo " | <a href='hapus.php?id={$id}' style='color:red;' onclick='return confirm(\"Hapus file ini?\")'>Hapus</a>";
                    }
                    echo "</li>";
                }
            } else {
                echo "<li><i>Belum ada materi yang diupload.</i></li>";
            }
            ?>
        </ul>
    </div>
</div>
<!-- === PRESENSI UNTUK MAHASISWA === -->
<?php if ($_SESSION['role'] === 'mahasiswa') { ?>

<div class="container presensi-box" style="margin-top:25px;">
    <h3>📍 Presensi Kehadiran</h3>

    <form method="POST" action="">
        <label>Pilih Mata Kuliah:</label><br>
        <select name="mata_kuliah" required style="padding: 10px; border-radius: 8px; width:80%; margin-bottom:10px;">
            <option value="">-- Pilih Mata Kuliah --</option>
            <option value="Pemrograman Web">Pemrograman Web</option>
            <option value="Basis Data">Basis Data</option>
            <option value="Jaringan Komputer">Jaringan Komputer</option>
            <option value="Algoritma & Struktur Data">Algoritma & Struktur Data</option>
            <option value="Sistem Operasi">Sistem Operasi</option>
            <option value="Kecerdasan Buatan">Kecerdasan Buatan</option>
            <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
        </select><br>

        <label>Pilih Status Kehadiran:</label><br>
        <select name="status" required style="padding: 10px; border-radius:8px; width:80%; margin-bottom:10px;">
            <option value="Hadir">Hadir</option>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
            <option value="Alpa">Alpa</option>
        </select><br>

        <button type="submit" name="absen">Simpan Presensi</button>
    </form>
</div>

<?php } ?> <!-- TUTUP BLOK MAHASISWA -->

<?php
// === PROSES POST PRESENSI ===
if (isset($_POST['absen']) && $_SESSION['role'] === 'mahasiswa') {

    $user = $_SESSION['user'];
    $tanggal = date('Y-m-d');
    $matkul = $_POST['mata_kuliah'];
    $status = $_POST['status'];

    // Cek apakah sudah absen untuk hari ini & matkul ini
    $cek = mysqli_query($conn, 
        "SELECT * FROM presensi 
        WHERE username='$user' AND tanggal='$tanggal' AND mata_kuliah='$matkul'"
    );

    if (mysqli_num_rows($cek) > 0) {
        echo "<div class='msg'>⚠️ Anda sudah mengisi presensi untuk mata kuliah ini hari ini.</div>";
    } else {
        mysqli_query($conn, 
            "INSERT INTO presensi (username, mata_kuliah, tanggal, status) 
            VALUES ('$user', '$matkul', '$tanggal', '$status')"
        );
        echo "<div class='msg'>✅ Presensi berhasil disimpan.</div>";
    }
}
?>

<!-- === KONTROL PRESENSI UNTUK DOSEN === -->
<?php if ($_SESSION['role'] === 'dosen') { ?>
<div class="container" style="margin-top:25px;">
    <h3>📊 Rekap Presensi Mahasiswa</h3>

    <table border="1" width="100%" cellpadding="8" style="background:white;color:black;border-radius:10px;">
        <tr style="font-weight:bold;background:#007bff;color:white;">
            <td>No</td>
            <td>Mahasiswa</td>
            <td>Mata Kuliah</td>
            <td>Tanggal</td>
            <td>Status</td>
        </tr>

        <?php
        $no = 1;
        $q = mysqli_query($conn, "SELECT * FROM presensi ORDER BY tanggal DESC");
        
        if (mysqli_num_rows($q) > 0) {
            while ($d = mysqli_fetch_assoc($q)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$d['username']}</td>
                        <td>{$d['mata_kuliah']}</td>
                        <td>{$d['tanggal']}</td>
                        <td>{$d['status']}</td>
                    </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='5'><i>Belum ada data presensi.</i></td></tr>";
        }
        ?>
    </table>
</div>
<?php } ?>

<!-- ========== SISTEM TUGAS / ASSIGNMENT ========== -->
<?php
// Konfigurasi upload
$maxTugasSize = 10 * 1024 * 1024; // 10 MB
$allowed_tugas_ext = ['pdf','doc','docx','zip','rar','ppt','pptx'];

// ====== PROSES: buat tugas (dosen) ======
if (isset($_POST['create_assignment']) && $_SESSION['role'] === 'dosen') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $mata_kuliah = trim($_POST['mata_kuliah'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');

    if ($judul === '' || $mata_kuliah === '' || $deadline === '') {
        echo "<div class='msg'>⚠️ Lengkapi semua field tugas (judul, mata kuliah, deadline).</div>";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO assignments (judul, deskripsi, mata_kuliah, deadline, uploader) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $judul, $deskripsi, $mata_kuliah, $deadline, $_SESSION['user']);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='msg'>✅ Tugas berhasil dibuat.</div>";
        } else {
            echo "<div class='msg'>❌ Gagal membuat tugas.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// ====== PROSES: submit tugas (mahasiswa) ======
if (isset($_POST['submit_assignment']) && $_SESSION['role'] === 'mahasiswa') {
    $assignment_id = intval($_POST['assignment_id'] ?? 0);
    if ($assignment_id <= 0) {
        echo "<div class='msg'>⚠️ Pilih tugas yang valid.</div>";
    } else {
        if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
            echo "<div class='msg'>⚠️ Tidak ada file yang dipilih atau terjadi error upload.</div>";
        } else {
            $file = $_FILES['submission_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_tugas_ext)) {
                echo "<div class='msg'>❌ Format file tidak diizinkan.</div>";
            } elseif ($file['size'] > $maxTugasSize) {
                echo "<div class='msg'>🚫 Ukuran file terlalu besar (max 10 MB).</div>";
            } else {
                $safe = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($file['name']));
                $newname = time() . "_" . rand(100,999) . "_" . $safe;
                $target_dir = "uploads/tugas/submissions/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

                if (move_uploaded_file($file['tmp_name'], $target_dir . $newname)) {
                    $stmt = mysqli_prepare($conn, "INSERT INTO submissions (assignment_id, username, file_name, original_name) VALUES (?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "isss", $assignment_id, $_SESSION['user'], $newname, $file['name']);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div class='msg'>✅ Berhasil mengumpulkan tugas.</div>";
                    } else {
                        // rollback file jika perlu
                        @unlink($target_dir . $newname);
                        echo "<div class='msg'>❌ Gagal menyimpan data submission.</div>";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<div class='msg'>❌ Gagal memindahkan file upload.</div>";
                }
            }
        }
    }
}

// ====== PROSES: beri nilai / feedback (dosen) ======
if (isset($_POST['grade_submission']) && $_SESSION['role'] === 'dosen') {
    $sub_id = intval($_POST['sub_id'] ?? 0);
    $grade = trim($_POST['grade'] ?? '');
    $feedback = trim($_POST['feedback'] ?? '');

    if ($sub_id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE submissions SET grade=?, feedback=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $grade, $feedback, $sub_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='msg'>✅ Nilai/feedback berhasil disimpan.</div>";
        } else {
            echo "<div class='msg'>❌ Gagal menyimpan nilai.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// ====== PROSES: hapus tugas (dosen) optional ======
if (isset($_GET['delete_assignment']) && $_SESSION['role'] === 'dosen') {
    $aid = intval($_GET['delete_assignment']);
    if ($aid > 0) {
        // Menghapus assignment; submissions akan terhapus otomatis karena FK ON DELETE CASCADE
        $stmt = mysqli_prepare($conn, "DELETE FROM assignments WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $aid);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='msg'>✅ Tugas dihapus.</div>";
        } else {
            echo "<div class='msg'>❌ Gagal menghapus tugas.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// ====== PROSES: download file (umum) ======
// Link download akan mengarah ke file di folder uploads/tugas/submissions/
?>

<!-- ==== UI: Buat tugas (dosen) ==== -->
<?php if ($_SESSION['role'] === 'dosen'): ?>
<div class="container" style="margin-top:25px;">
    <h3>📝 Buat / Kelola Tugas</h3>
    <form method="POST">
        <input type="text" name="judul" placeholder="Judul Tugas" style="width:80%;padding:10px;border-radius:8px" required><br><br>
        <select name="mata_kuliah" required style="padding:10px;border-radius:8px;width:80%;margin-bottom:10px;">
            <option value="">-- Pilih Mata Kuliah --</option>
            <option value="Pemrograman Web">Pemrograman Web</option>
            <option value="Basis Data">Basis Data</option>
            <option value="Jaringan Komputer">Jaringan Komputer</option>
            <option value="Algoritma & Struktur Data">Algoritma & Struktur Data</option>
            <option value="Sistem Operasi">Sistem Operasi</option>
        </select><br>
        <textarea name="deskripsi" placeholder="Deskripsi tugas (opsional)" style="width:80%;height:90px;padding:10px;border-radius:8px"></textarea><br><br>
        <label>Deadline:</label><br>
        <input type="datetime-local" name="deadline" style="padding:10px;border-radius:8px;width:80%" required><br><br>

        <button type="submit" name="create_assignment">Buat Tugas</button>
    </form>

    <hr style="margin:20px 0;">

    <h4>Daftar Tugas</h4>
    <table border="1" width="100%" cellpadding="8" style="background:white;color:black;border-radius:10px;">
        <tr style="font-weight:bold;background:#007bff;color:white;">
            <td>No</td><td>Judul</td><td>Mata Kuliah</td><td>Deadline</td><td>Dibuat Oleh</td><td>Aksi</td>
        </tr>
        <?php
        $q = mysqli_query($conn, "SELECT * FROM assignments ORDER BY created_at DESC");
        $no = 1;
        if (mysqli_num_rows($q) > 0) {
            while ($r = mysqli_fetch_assoc($q)) {
                $aid = $r['id'];
                $judul = htmlspecialchars($r['judul']);
                $matkul = htmlspecialchars($r['mata_kuliah']);
                $dl = htmlspecialchars($r['deadline']);
                $up = htmlspecialchars($r['uploader']);
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$judul}</td>
                        <td>{$matkul}</td>
                        <td>{$dl}</td>
                        <td>{$up}</td>
                        <td>
                          <a href='?view_submissions={$aid}'>Lihat Submisi</a> |
                          <a href='?delete_assignment={$aid}' style='color:red;' onclick='return confirm(\"Hapus tugas ini? Semua submisi juga akan ikut terhapus.\")'>Hapus</a>
                        </td>
                      </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='6'><i>Belum ada tugas.</i></td></tr>";
        }
        ?>
    </table>
</div>
<?php endif; ?>


<!-- ==== UI: Daftar tugas (mahasiswa) + form submit ==== -->

<div class="container" style="margin-top:25px;">
    <div id="assignments-section">
        <h3>📚 Tugas Mahasiswa</h3>

        <table border="1" width="100%" cellpadding="8" style="background:white;color:black;border-radius:10px;">
            <tr style="font-weight:bold;background:#007bff;color:white;">
                <td>No</td><td>Judul</td><td>Mata Kuliah</td><td>Deadline</td><td>Status</td><td>Aksi</td>
            </tr>

            <?php
            $q2 = mysqli_query($conn, "SELECT * FROM assignments ORDER BY deadline ASC");
            $no2 = 1;

            if (mysqli_num_rows($q2) > 0) {
                while ($a = mysqli_fetch_assoc($q2)) {
                    $aid = $a['id'];
                    $judul = htmlspecialchars($a['judul']);
                    $matkul = htmlspecialchars($a['mata_kuliah']);
                    $dl_disp = htmlspecialchars($a['deadline']);

                    // cek apakah user sudah submit tugas ini
                    $stmt = mysqli_prepare($conn, "SELECT id, grade FROM submissions WHERE assignment_id = ? AND username = ?");
                    mysqli_stmt_bind_param($stmt, "is", $aid, $_SESSION['user']);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $sub_id, $sub_grade);
                    $submitted = mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);

                    $status = $submitted ? "Sudah dikumpulkan" . ($sub_grade ? " (Nilai: {$sub_grade})" : "") : "Belum dikumpulkan";

                    echo "
                    <tr>
                        <td>{$no2}</td>
                        <td>{$judul}</td>
                        <td>{$matkul}</td>
                        <td>{$dl_disp}</td>
                        <td>{$status}</td>
                        <td>";

                    if (!$submitted) {
                        echo "<button onclick='toggleForm({$aid})'>Kumpulkan</button>";
                    } else {
                        echo "<i>Menunggu penilaian</i>";
                    }

                    echo "</td></tr>";

                    // Form upload
                    echo "
                    <tr id='formrow-{$aid}' style='display:none;'>
                        <td colspan='6'>
                            <div id='form-{$aid}' style='padding:15px; background:white; color:#111; border-radius:10px; display:none;'>
                                <form method='POST' enctype='multipart/form-data'>
                                    <input type='hidden' name='assignment_id' value='{$aid}'>
                                    <label>Upload File Tugas:</label><br>
                                    <input type='file' name='submission_file' required><br><br>

                                    <button type='submit' name='submit_assignment'>Kumpulkan Tugas</button>
                                    <button type='button' onclick='toggleForm({$aid})'>Batal</button>
                                </form>
                            </div>
                        </td>
                    </tr>";

                    $no2++;
                }
            } else {
                echo "<tr><td colspan='6'><i>Belum ada tugas yang dibuat dosen.</i></td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<script>
function toggleForm(id) {
    let row = document.getElementById("formrow-" + id);
    let box = document.getElementById("form-" + id);

    if (row.style.display === "none") {
        row.style.display = "table-row";
        box.style.display = "block";
    } else {
        row.style.display = "none";
        box.style.display = "none";
    }
}
</script>


<script>
function toggleForm(id){
    let row = document.getElementById('formrow-' + id);
    let box = document.getElementById('form-' + id);

    if(row.style.display === 'none'){
        row.style.display = 'table-row';
        box.style.display = 'block';
    } else {
        row.style.display = 'none';
        box.style.display = 'none';
    }
}
</script>


<!-- ==== UI: Lihat submisi per tugas (untuk dosen) ==== -->
<?php
if (isset($_GET['view_submissions']) && $_SESSION['role'] === 'dosen') {
    $view_id = intval($_GET['view_submissions']);
    $stmt = mysqli_prepare($conn, "SELECT judul, mata_kuliah FROM assignments WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $view_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $vjudul, $vmatkul);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    ?>
    <div class="container" style="margin-top:25px;">
        <h3>📥 Submisi: <?= htmlspecialchars($vjudul ?? ''); ?> (<?= htmlspecialchars($vmatkul ?? ''); ?>)</h3>

        <table border="1" width="100%" cellpadding="8" style="background:white;color:black;border-radius:10px;">
            <tr style="font-weight:bold;background:#007bff;color:white;">
                <td>No</td><td>Mahasiswa</td><td>File</td><td>Waktu</td><td>Nilai</td><td>Aksi</td>
            </tr>
            <?php
            $s = mysqli_prepare($conn, "SELECT id, username, file_name, original_name, uploaded_at, grade FROM submissions WHERE assignment_id = ? ORDER BY uploaded_at ASC");
            mysqli_stmt_bind_param($s, "i", $view_id);
            mysqli_stmt_execute($s);
            mysqli_stmt_bind_result($s, $sid, $suser, $sfile, $sorig, $suploaded, $sgrade);
            $no=1;
            $has=false;
            while (mysqli_stmt_fetch($s)) {
                $has=true;
                $download_path = "uploads/tugas/submissions/" . $sfile;
                echo "<tr>
                        <td>{$no}</td>
                        <td>".htmlspecialchars($suser)."</td>
                        <td><a href='{$download_path}' target='_blank'>".htmlspecialchars($sorig)."</a></td>
                        <td>".htmlspecialchars($suploaded)."</td>
                        <td>".($sgrade ? htmlspecialchars($sgrade) : "<i>-</i>")."</td>
                        <td>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='sub_id' value='{$sid}'>
                                <input type='text' name='grade' placeholder='Nilai' style='padding:6px;border-radius:6px;width:80px' value='".htmlspecialchars($sgrade)."'>
                                <input type='text' name='feedback' placeholder='Feedback (opsional)' style='padding:6px;border-radius:6px;width:200px'>
                                <button type='submit' name='grade_submission'>Simpan</button>
                            </form>
                        </td>
                      </tr>";
                $no++;
            }
            mysqli_stmt_close($s);
            if (!$has) {
                echo "<tr><td colspan='6'><i>Belum ada submisi.</i></td></tr>";
            }
            ?>
        </table>

        <p style="margin-top:10px;"><a href="dashboard.php">Kembali ke Dashboard</a></p>
    </div>
    <?php
}
?>
<!-- ========== AKHIR SISTEM TUGAS ========== -->

</body>
</html>
