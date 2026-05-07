-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Bulan Mei 2026 pada 11.09
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `id_instansi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `tanggal_lapor` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) NOT NULL,
  `keluhan` text NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `metode_lokasi` varchar(20) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `alamat_manual` text DEFAULT NULL,
  `status` enum('menunggu','diproses','selesai','ditolak') NOT NULL DEFAULT 'menunggu',
  `id_petugas` int(11) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `tanggal_lapor`, `foto`, `keluhan`, `kategori`, `metode_lokasi`, `latitude`, `longitude`, `alamat_manual`, `status`, `id_petugas`, `foto_bukti`) VALUES
(1, '2026-04-03 07:35:27', '1775201727_triple_t.jpg', 'jalan rusak', 'Jalan Raya', 'peta', '-8.591860453946488', '116.09499961137773', NULL, 'diproses', 2, NULL),
(2, '2026-04-03 12:11:22', '1775218282_IMG_20251211_193012.jpg', 'pamer dikit', 'Taman', 'peta', '-8.603078705754506', '116.09652847051622', NULL, 'selesai', 2, 'bukti_1775219730_triple_t.jpg'),
(3, '2026-04-03 12:26:34', '1775219194_triple_t.jpg', 'tung tung tung sahur', 'Lampu Jalan', 'peta', '-8.573125581838719', '116.08969688415529', NULL, 'ditolak', NULL, NULL),
(5, '2026-04-10 00:08:20', '1775779699_triple_t.webp', 'srnelsiglgiulens', 'Trotoar', 'peta', '-8.596931250974162', '116.11008435487749', NULL, 'selesai', 2, 'bukti_1775779834_triple_t.webp'),
(6, '2026-05-05 03:18:20', '1777951100_Screenshot_2026-05-05_102919.png', 'eedthhdhhtrh', 'Lampu Jalan', 'peta', '-8.582885613528193', '116.10873252153398', NULL, 'selesai', 2, 'bukti_1777951375_Screenshot_2026-05-05_102919.png'),
(7, '2026-05-05 10:00:44', '1777975244_Screenshot_2026-05-05_102919.png', 'efmefmefm', 'Jalan Raya', 'peta', '-8.58338695638996', '116.11415863037111', NULL, 'menunggu', NULL, NULL),
(8, '2026-05-05 10:01:09', '1777975269_env.png', 'dsafgfnh', 'Drainase', 'peta', '-8.577671409607865', '116.08449608087541', NULL, 'menunggu', NULL, NULL),
(9, '2026-05-05 10:19:00', '1777976340_triple_t.jpg', 'rusak', 'Drainase', 'peta', '-8.600623001145706', '116.09827995300294', NULL, 'menunggu', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password`, `role`) VALUES
(1, 'Admin utama', 'admin', '123456', 'admin'),
(2, 'Tim Reaksi Cepat A', 'petugas1', '123456', 'petugas');

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
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `instansi`
--
ALTER TABLE `instansi`
  MODIFY `id_instansi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD CONSTRAINT `kategori_ibfk_1` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
