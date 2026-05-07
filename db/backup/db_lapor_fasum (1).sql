-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Bulan Mei 2026 pada 11.40
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
-- Database: `db_lapor_fasum`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `instansi`
--

CREATE TABLE `instansi` (
  `id_instansi` int(11) NOT NULL,
  `nama_instansi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `instansi`
--

INSERT INTO `instansi` (`id_instansi`, `nama_instansi`) VALUES
(1, 'Pusat Bantuan (Operator)'),
(2, 'Dinas Pekerjaan Umum (PU)'),
(3, 'Dinas Perhubungan (Dishub)'),
(4, 'Dinas Lingkungan Hidup (DLH)'),
(5, 'PLN (Perusahaan Listrik Negara)'),
(6, 'PDAM (Perusahaan Daerah Air Minum)'),
(7, 'Telkom / Provider Telekomunikasi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `id_instansi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `id_instansi`) VALUES
(1, 'Lain-lain', 1),
(2, 'Jalan Raya', 2),
(3, 'Trotoar', 2),
(4, 'Jembatan', 2),
(5, 'Gorong-gorong / Saluran Air', 2),
(6, 'Lampu Jalan', 3),
(7, 'Rambu Lalu Lintas', 3),
(8, 'Lampu Lalu Lintas (Traffic Light)', 3),
(9, 'Halte Transportasi Umum', 3),
(10, 'Taman Kota', 4),
(11, 'Pohon di Ruang Publik', 4),
(12, 'Fasilitas Pembuangan Sampah', 4),
(13, 'Tiang Listrik', 5),
(14, 'Kabel Listrik', 5),
(15, 'Gardu Listrik', 5),
(16, 'Pipa Air Bersih', 6),
(17, 'Fasilitas Air Siap Minum', 6),
(18, 'Tiang / Kabel Telekomunikasi', 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `tanggal_lapor` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) NOT NULL,
  `keluhan` text NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `metode_lokasi` varchar(20) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `alamat_manual` text DEFAULT NULL,
  `status` enum('menunggu','diproses','selesai','ditolak') DEFAULT 'menunggu',
  `id_petugas` int(11) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL,
  `id_instansi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `id_instansi`) VALUES
(1, 'Admin Operator Pusat', 'admin_pusat', '123', 'admin', 1),
(2, 'Admin Dinas PU', 'admin_pu', '123', 'admin', 2),
(3, 'Admin Dinas Perhubungan', 'admin_dishub', '123', 'admin', 3),
(4, 'Admin Dinas Lingkungan Hidup', 'admin_dlh', '123', 'admin', 4),
(5, 'Admin PLN', 'admin_pln', '123', 'admin', 5),
(6, 'Admin PDAM', 'admin_pdam', '123', 'admin', 6),
(7, 'Admin Telkom', 'admin_telkom', '123', 'admin', 7),
(8, 'PU 1', 'petugas_pu1', '123', 'petugas', 2),
(9, 'PU 2', 'petugas_pu2', '123', 'petugas', 2),
(10, 'PU 3', 'petugas_pu3', '123', 'petugas', 2),
(11, 'PU 4', 'petugas_pu4', '123', 'petugas', 2),
(12, 'PU 5', 'petugas_pu5', '123', 'petugas', 2),
(13, 'Dishub 1', 'petugas_dishub1', '123', 'petugas', 3),
(14, 'Dishub 2', 'petugas_dishub2', '123', 'petugas', 3),
(15, 'Dishub 3', 'petugas_dishub3', '123', 'petugas', 3),
(16, 'Dishub 4', 'petugas_dishub4', '123', 'petugas', 3),
(17, 'Dishub 5', 'petugas_dishub5', '123', 'petugas', 3),
(18, 'DLH 1', 'petugas_dlh1', '123', 'petugas', 4),
(19, 'DLH 2', 'petugas_dlh2', '123', 'petugas', 4),
(20, 'DLH 3', 'petugas_dlh3', '123', 'petugas', 4),
(21, 'DLH 4', 'petugas_dlh4', '123', 'petugas', 4),
(22, 'DLH 5', 'petugas_dlh5', '123', 'petugas', 4),
(23, 'PLN 1', 'petugas_pln1', '123', 'petugas', 5),
(24, 'PLN 2', 'petugas_pln2', '123', 'petugas', 5),
(25, 'PLN 3', 'petugas_pln3', '123', 'petugas', 5),
(26, 'PLN 4', 'petugas_pln4', '123', 'petugas', 5),
(27, 'PLN 5', 'petugas_pln5', '123', 'petugas', 5),
(28, 'PDAM 1', 'petugas_pdam1', '123', 'petugas', 6),
(29, 'PDAM 2', 'petugas_pdam2', '123', 'petugas', 6),
(30, 'PDAM 3', 'petugas_pdam3', '123', 'petugas', 6),
(31, 'PDAM 4', 'petugas_pdam4', '123', 'petugas', 6),
(32, 'PDAM 5', 'petugas_pdam5', '123', 'petugas', 6),
(33, 'Telkom 1', 'petugas_telkom1', '123', 'petugas', 7),
(34, 'Telkom 2', 'petugas_telkom2', '123', 'petugas', 7),
(35, 'Telkom 3', 'petugas_telkom3', '123', 'petugas', 7),
(36, 'Telkom 4', 'petugas_telkom4', '123', 'petugas', 7),
(37, 'Telkom 5', 'petugas_telkom5', '123', 'petugas', 7);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `instansi`
--
ALTER TABLE `instansi`
  ADD PRIMARY KEY (`id_instansi`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `id_instansi` (`id_instansi`);

--
-- Indeks untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_instansi` (`id_instansi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `instansi`
--
ALTER TABLE `instansi`
  MODIFY `id_instansi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD CONSTRAINT `kategori_ibfk_1` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
