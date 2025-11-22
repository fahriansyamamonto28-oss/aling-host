<?php
session_start();
include "db.php";

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'dosen') {
    die("Akses ditolak.");
}

if (!isset($_GET['id'])) {
    die("ID materi tidak diberikan.");
}

$id = intval($_GET['id']);

// Ambil nama file dulu
$stmt = mysqli_prepare($conn, "SELECT nama_file FROM materi WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nama_file);
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    die("Materi tidak ditemukan.");
}
mysqli_stmt_close($stmt);

// Hapus file fisik
$path = "uploads/" . $nama_file;
if (file_exists($path)) {
    unlink($path);
}

// Hapus record dari DB
$stmt2 = mysqli_prepare($conn, "DELETE FROM materi WHERE id = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

// Log aktivitas
$aktivitas = "Menghapus file: " . $nama_file;
$waktu = date('Y-m-d H:i:s');
$stmt3 = mysqli_prepare($conn, "INSERT INTO log_activity (username, aktivitas, waktu) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt3, "sss", $_SESSION['user'], $aktivitas, $waktu);
mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

header("Location: dashboard.php");
exit;
?>
