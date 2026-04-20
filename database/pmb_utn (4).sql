-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 20 Apr 2026 pada 04.55
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pmb_utn`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', '$2a$12$TBbqy7rH3hLzb.jaLioZNe0iX7OPzAePAbTvUyyh6Jzj4IBHAgvwu', 'Administrator UTN', '2026-01-27 14:24:19'),
(2, 'admin10', '4fbd41a36dac3cd79aa1041c9648ab89', 'Admin', '2026-01-27 14:42:14'),
(3, 'admin01', '0192023a7bbd73250516f069df18b500', 'Admin AR', '2026-02-05 07:00:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `calon_mahasiswa`
--

CREATE TABLE `calon_mahasiswa` (
  `id_calon` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_test` varchar(20) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `asal_sekolah` varchar(100) DEFAULT NULL,
  `jurusan_sekolah` varchar(50) DEFAULT NULL,
  `tahun_lulus` year(4) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `calon_mahasiswa`
--

INSERT INTO `calon_mahasiswa` (`id_calon`, `nama_lengkap`, `email`, `no_test`, `nim`, `foto`, `password`, `no_hp`, `alamat`, `asal_sekolah`, `jurusan_sekolah`, `tahun_lulus`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `created_at`) VALUES
(27, 'RAFFLI ARDITYA', 'raffliarditya@gmail.com', 'UR-2026-0027', 'UAR20260027', 'user_1775363900.jpg', 'e10adc3949ba59abbe56e057f20f883e', '085810077475', 'Jalan', 'SMKN 65 JAKARTA TIMUR', 'PPLG', '2026', 'L', 'Rs budi asih', '2008-01-10', '2026-04-05 04:38:20'),
(28, 'Fadli', 'Fadli@gmail.com', 'UR-2026-0028', 'UAR20260028', 'user_1775379630.jpg', 'e10adc3949ba59abbe56e057f20f883e', '085810077475', 'Jalan', 'SMKN 65 JAKARTA TIMUR', 'HOTEL', '2026', 'L', 'Rs budi asih', '2008-01-10', '2026-04-05 09:00:30'),
(29, 'Muhammad Arditya', 'arditya@gmail.com', 'UR-2026-0029', 'UAR20260029', 'user_1775562539.jpg', 'e10adc3949ba59abbe56e057f20f883e', '084758928929', 'Jl.Pancawarga 1', 'SMKN 65 JAKARTA', 'PPLG', '2026', 'L', 'Jakarta', '2008-01-10', '2026-04-07 11:49:00'),
(30, 'Stevan', 'stevan@gmail.com', 'UR-2026-0030', NULL, 'user_1775607719.jpg', 'e10adc3949ba59abbe56e057f20f883e', '08458828828', 'Jl', 'SMKN 65 JAKARTA', 'PPLG', '2026', 'L', 'Jakarta', '2008-01-10', '2026-04-08 00:21:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_ulang`
--

CREATE TABLE `daftar_ulang` (
  `id_daftar_ulang` int(11) NOT NULL,
  `id_pendaftaran` int(11) DEFAULT NULL,
  `tanggal_daftar_ulang` date DEFAULT NULL,
  `no_induk_mahasiswa` varchar(20) DEFAULT NULL,
  `status_pembayaran` enum('belum','lunas') DEFAULT 'belum',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `upload_ktp` varchar(255) DEFAULT NULL,
  `upload_kk` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `daftar_ulang`
--

INSERT INTO `daftar_ulang` (`id_daftar_ulang`, `id_pendaftaran`, `tanggal_daftar_ulang`, `no_induk_mahasiswa`, `status_pembayaran`, `bukti_pembayaran`, `upload_ktp`, `upload_kk`, `status_verifikasi`) VALUES
(14, 23, '2026-04-05', NULL, 'lunas', '1775372585_1774757657_astronaut-cinematic-3840x2160-25890.jpg', '1775372585_1774757657_astronaut-cinematic-3840x2160-25890.jpg', '1775372585_bmw-m3-angel-eyes-black-background-5k-3840x2160-896.jpg', 'diterima'),
(15, 24, '2026-04-05', '26MI002', 'belum', NULL, NULL, NULL, 'diterima'),
(16, 25, '2026-04-07', NULL, 'lunas', '1775562685_Laskar_Pelangi_film.jpg', '1775562685_Kartu Pelajar.png', '1775562685_Laskar_Pelangi_film.jpg', 'diterima');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `id_jurusan` int(11) NOT NULL,
  `kode_jurusan` varchar(10) NOT NULL,
  `nama_jurusan` varchar(100) NOT NULL,
  `kuota` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`id_jurusan`, `kode_jurusan`, `nama_jurusan`, `kuota`) VALUES
(1, 'TI', 'Teknik Informatika', 100),
(2, 'SI', 'Sistem Informasi', 80),
(3, 'TK', 'Teknik Komputer', 60),
(4, 'MI', 'Manajemen Informatika', 70);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_calon` int(11) DEFAULT NULL,
  `id_jurusan` int(11) DEFAULT NULL,
  `tanggal_daftar` date DEFAULT NULL,
  `status` enum('pending','lulus','tidak_lulus') DEFAULT 'pending',
  `nilai_test` decimal(5,2) DEFAULT NULL,
  `no_test` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `id_calon`, `id_jurusan`, `tanggal_daftar`, `status`, `nilai_test`, `no_test`) VALUES
(23, 27, 4, '2026-04-05', 'lulus', 100.00, NULL),
(24, 28, 4, '2026-04-05', 'lulus', 100.00, NULL),
(25, 29, 1, '2026-04-07', 'lulus', 100.00, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `key_name`, `value`, `description`, `updated_at`) VALUES
(1, 'biaya_pendaftaran', '', 'Biaya pendaftaran awal', '2026-03-29 09:13:50'),
(2, 'biaya_daftar_ulang', '', 'Biaya daftar ulang', '2026-03-29 09:13:50'),
(3, 'bank_name', 'BCA', 'Nama bank', '2026-03-29 09:08:43'),
(4, 'bank_account', '1234567890', 'Nomor rekening', '2026-03-29 09:08:43'),
(5, 'bank_account_name', 'UNIVERSITAS ARTEN', 'Atas nama rekening', '2026-03-29 09:13:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_ujian`
--

CREATE TABLE `pengaturan_ujian` (
  `id` int(11) NOT NULL,
  `durasi_menit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_ujian`
--

INSERT INTO `pengaturan_ujian` (`id`, `durasi_menit`) VALUES
(1, 60);

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal_test`
--

CREATE TABLE `soal_test` (
  `id_soal` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` varchar(255) DEFAULT NULL,
  `pilihan_b` varchar(255) DEFAULT NULL,
  `pilihan_c` varchar(255) DEFAULT NULL,
  `pilihan_d` varchar(255) DEFAULT NULL,
  `jawaban_benar` enum('a','b','c','d') DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `id_jurusan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal_test`
--

INSERT INTO `soal_test` (`id_soal`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `jawaban_benar`, `kategori`, `jurusan`, `id_jurusan`) VALUES
(9, 'Semua mahasiswa rajin belajar.\r\nSebagian mahasiswa suka olahraga.\r\n\r\nKesimpulan yang tepat adalah…', 'Semua yang suka olahraga rajin belajar', 'Sebagian yang suka olahraga rajin belajar', 'Semua yang rajin belajar suka olahraga', 'Tidak ada yang suka olahraga rajin belajar', 'b', 'Umum', NULL, 4),
(10, 'Html?', '1', '2', '3', '4', 'a', 'Umum', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `calon_mahasiswa`
--
ALTER TABLE `calon_mahasiswa`
  ADD PRIMARY KEY (`id_calon`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  ADD PRIMARY KEY (`id_daftar_ulang`),
  ADD UNIQUE KEY `no_induk_mahasiswa` (`no_induk_mahasiswa`),
  ADD KEY `id_pendaftaran` (`id_pendaftaran`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id_jurusan`),
  ADD UNIQUE KEY `kode_jurusan` (`kode_jurusan`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_calon` (`id_calon`),
  ADD KEY `id_jurusan` (`id_jurusan`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name` (`key_name`);

--
-- Indeks untuk tabel `pengaturan_ujian`
--
ALTER TABLE `pengaturan_ujian`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `soal_test`
--
ALTER TABLE `soal_test`
  ADD PRIMARY KEY (`id_soal`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `calon_mahasiswa`
--
ALTER TABLE `calon_mahasiswa`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  MODIFY `id_daftar_ulang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id_jurusan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_ujian`
--
ALTER TABLE `pengaturan_ujian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `soal_test`
--
ALTER TABLE `soal_test`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  ADD CONSTRAINT `daftar_ulang_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`);

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `calon_mahasiswa` (`id_calon`),
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`id_jurusan`) REFERENCES `jurusan` (`id_jurusan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
