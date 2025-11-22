-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Nov 2025 pada 08.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `mata_kuliah` varchar(150) DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `uploader` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `assignments`
--

INSERT INTO `assignments` (`id`, `judul`, `deskripsi`, `mata_kuliah`, `deadline`, `uploader`, `created_at`) VALUES
(1, 'Buatkan beberapa pengertian dari mata kuliah ini!', '', 'Pemrograman Web', '2025-11-30 12:00:00', 'Mner efraim', '2025-11-21 23:28:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_activity`
--

CREATE TABLE `log_activity` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `aktivitas` text NOT NULL,
  `waktu` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_activity`
--

INSERT INTO `log_activity` (`id`, `username`, `aktivitas`, `waktu`) VALUES
(1, 'unima', 'Mengupload file: 1761142945_PROGRES_LMS_MINGGU_KE_4.pdf', '2025-10-22 16:22:25'),
(2, 'Mner efraim', 'Mengupload file: 1761154438_Laporan_Login_LMS_Sederhana.docx', '2025-10-22 19:33:58'),
(3, 'Mner efraim', 'Mengupload file: 1761154496_PROGRES_LMS_MINGGU_KE_3.pdf', '2025-10-22 19:34:56'),
(4, 'Mner efraim', 'Menghapus file: 1761142945_PROGRES_LMS_MINGGU_KE_4.pdf', '2025-10-22 19:35:18'),
(5, 'Mner efraim', 'Mengupload file: 1761154551_PROGRES_LMS_MINGGU_KE_4.pdf', '2025-10-22 19:35:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materi`
--

CREATE TABLE `materi` (
  `id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `uploader` varchar(100) NOT NULL,
  `tanggal_upload` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `materi`
--

INSERT INTO `materi` (`id`, `nama_file`, `uploader`, `tanggal_upload`) VALUES
(2, '1761154438_Laporan_Login_LMS_Sederhana.docx', 'Mner efraim', '2025-10-22 19:33:58'),
(3, '1761154496_PROGRES_LMS_MINGGU_KE_3.pdf', 'Mner efraim', '2025-10-22 19:34:56'),
(4, '1761154551_PROGRES_LMS_MINGGU_KE_4.pdf', 'Mner efraim', '2025-10-22 19:35:51'),
(5, '1762389954_PAPER_KELOMPOK_9.docx', 'Mner efraim', '2025-11-06 01:45:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `presensi`
--

CREATE TABLE `presensi` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `mata_kuliah` varchar(100) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `presensi`
--

INSERT INTO `presensi` (`id`, `username`, `mata_kuliah`, `tanggal`, `status`) VALUES
(3, 'Fahri', 'Pemrograman Web', '2025-11-21', 'Izin'),
(4, 'Fahri', 'Basis Data', '2025-11-21', 'Hadir'),
(5, 'Fahri', 'Jaringan Komputer', '2025-11-21', 'Alpa'),
(6, 'Fahri', 'Kecerdasan Buatan', '2025-11-21', 'Sakit');

-- --------------------------------------------------------

--
-- Struktur dari tabel `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `grade` varchar(20) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `username`, `file_name`, `original_name`, `uploaded_at`, `grade`, `feedback`) VALUES
(1, 1, 'Fahri', '1763742386_320_LAPORAN_TUGAS_2.docx', 'LAPORAN TUGAS 2.docx', '2025-11-22 00:26:26', '100', 'baik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `foto`, `role`) VALUES
(1, 'admin', '12345', NULL, 'mahasiswa'),
(2, 'mahasiswa', 'password', NULL, 'mahasiswa'),
(5, 'dosen1', '$2y$10$UkgfJ3GWPutHCFP8qENkQ.12zYfakW6y33Qd3UmO3Ysj6t09tppBy', NULL, 'dosen'),
(6, 'mahasiswa1', '$2y$10$zhye1DDIUsL.FUzfvCqkh.QX22ip7Z3dFP5mXI96plrMsBE9OUmgG', NULL, 'mahasiswa'),
(7, 'Fahri', '$2y$10$UaEXuH.CxPFGcHqPUSIEZOwBfMFxMS0wY4q95OuJPro/U.TXA1vCe', 'Fahri_1761151052.jpg', 'mahasiswa'),
(8, 'Mner efraim', '$2y$10$m5HMiUa3vIScyYyv2gJkduGzBH87WwSKD9pNDXEee9.QYkHsGsDci', '1761152803_mner efraim.jpg', 'dosen'),
(9, '23210140', '$2y$10$uvA5zYFmFLvB8bX/eQhXz.W93LmTOwR8Jj870H2DRDr2Ju6rZNSkS', 'default-avatar.png', 'mahasiswa'),
(10, 'ALAN', '$2y$10$0Ryn/OQm7GgHqI/53bUa7u3eJW4JbvniNTEvoi.ZynizuBVV5RR9m', 'default-avatar.png', 'mahasiswa'),
(11, 'ALAN1', '$2y$10$WyvVwelJElqppQkA4JTube9bopiwj/Qjbp4rmDS6KeG8r3Wt9z8yu', 'default-avatar.png', 'dosen'),
(12, 'mahasiswa gila', '$2y$10$L4iSZ1kFX3ASgNLj7.VtheyLv8hsQI2AqxKOAJKN4SmeJOPgEB9r6', 'default-avatar.png', 'mahasiswa'),
(13, 'aling', '$2y$10$TwaeHQ2W0pOgRIjV41ecfusep/Na6FJpX6dQ9hiBEy21tT11.kmgS', 'default-avatar.png', 'mahasiswa'),
(14, '123', '$2y$10$eP9.eiGEWIFuCPVC5K/HiOX8.yBCTAYXYtHYJ0tbtbSbfG0XA4yJO', NULL, 'mahasiswa'),
(15, '123', '$2y$10$4xSROHw4yolnrB23x/1ox.CzJ/BAHR9m9tz1x0w1gQgUreMD1HIKO', NULL, 'mahasiswa'),
(16, '098', '$2y$10$r/HfKULHruraMNNQnvoB7O.MtQyXhQFtL9kKAVZXk9yepFhYIQs8i', 'default-avatar.png', 'mahasiswa');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_activity`
--
ALTER TABLE `log_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `materi`
--
ALTER TABLE `materi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `log_activity`
--
ALTER TABLE `log_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `materi`
--
ALTER TABLE `materi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
