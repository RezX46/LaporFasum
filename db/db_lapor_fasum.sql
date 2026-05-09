-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Bulan Mei 2026 pada 14.13
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
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_instansi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `id_user`, `id_instansi`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6),
(7, 7, 7);

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
  `status` enum('menunggu','diproses','menunggu verifikasi','selesai','ditolak') DEFAULT 'menunggu',
  `id_petugas` int(11) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `tanggal_lapor`, `foto`, `keluhan`, `id_kategori`, `metode_lokasi`, `latitude`, `longitude`, `alamat_manual`, `status`, `id_petugas`, `foto_bukti`) VALUES
(1, '2026-05-07 10:16:01', '1778148961_triple_t.jpg', 'wwfwfwf', 14, 'peta', '-8.590716', '116.09301', NULL, 'selesai', 8, 'bukti_1778152024_triple_t.jpg'),
(2, '2026-05-07 10:34:28', '1778150068_IMG_20251211_193012.jpg', 'bjnkmoi', 10, 'peta', '-8.590716', '116.09301', NULL, 'menunggu', NULL, NULL),
(3, '2026-05-07 11:03:37', 'dummy_pusat1.jpg', 'Saya tidak tahu ini lapor ke mana, ada tiang miring mau roboh.', 3, 'peta', '-8.5877', '116.0965', NULL, 'menunggu', NULL, NULL),
(4, '2026-05-07 11:03:37', 'dummy_pusat2.jpg', 'Tolong ini ada fasilitas umum yang rusak parah di pertigaan.', 4, 'manual', NULL, NULL, 'Pertigaan lampu merah pasar', 'menunggu', NULL, NULL),
(5, '2026-05-07 11:03:37', 'dummy_pu1.jpg', 'Jalan berlubang cukup dalam, sangat bahaya kalau malam.', 2, 'peta', '-8.5880', '116.0970', NULL, 'diproses', 1, NULL),
(6, '2026-05-07 11:03:37', 'dummy_pu2.jpg', 'Trotoar amblas akibat galian yang tidak ditutup.', 3, 'peta', '-8.5890', '116.0980', NULL, 'selesai', 2, 'bukti_pu2.jpg'),
(7, '2026-05-07 11:03:37', 'dummy_pu3.jpg', 'Jembatan retak di bagian penyangganya.', 4, 'manual', NULL, NULL, 'Jembatan dekat alun-alun', 'menunggu', NULL, NULL),
(8, '2026-05-07 11:03:37', 'dummy_pu4.jpg', 'Saluran air mampet dan bau.', 5, 'peta', '-8.5860', '116.0950', NULL, 'ditolak', NULL, NULL),
(9, '2026-05-07 11:03:37', 'dummy_dishub1.jpg', 'Lampu jalan mati sudah 3 hari, jalanan jadi sangat gelap.', 6, 'peta', '-8.5850', '116.0940', NULL, 'diproses', 3, NULL),
(10, '2026-05-07 11:03:37', 'dummy_dishub2.jpg', 'Rambu dilarang parkir hilang dicabut orang.', 7, 'manual', NULL, NULL, 'Jalan utama depan SMA 1', 'selesai', 4, 'bukti_dishub2.jpg'),
(11, '2026-05-07 11:03:37', 'dummy_dishub3.jpg', 'Lampu lalu lintas error, hijau semua jadi macet.', 8, 'peta', '-8.5840', '116.0930', NULL, 'menunggu', NULL, NULL),
(12, '2026-05-07 11:03:37', 'dummy_dlh1.jpg', 'Ayunan di taman kota rusak dan rantainya putus.', 10, 'peta', '-8.5830', '116.0920', NULL, 'diproses', 5, NULL),
(13, '2026-05-07 11:03:37', 'dummy_dlh2.jpg', 'Pohon tumbang menghalangi sebagian jalan raya.', 11, 'peta', '-8.5820', '116.0910', NULL, 'selesai', 6, 'bukti_dlh2.jpg'),
(14, '2026-05-07 11:03:37', 'dummy_dlh3.jpg', 'Tempat sampah umum sudah hancur dan tumpah berserakan.', 12, 'manual', NULL, NULL, 'Taman bermain komplek', 'menunggu', NULL, NULL),
(15, '2026-05-07 11:03:37', 'dummy_pln1.jpg', 'Tiang listrik hampir roboh tertabrak truk.', 13, 'peta', '-8.5810', '116.0900', NULL, 'selesai', 7, 'bukti_1778160716_green.png'),
(16, '2026-05-07 11:03:37', 'dummy_pln2.jpg', 'Kabel listrik menjuntai ke jalan, rawan tersangkut kendaraan.', 14, 'peta', '-8.5800', '116.0890', NULL, 'selesai', 8, 'bukti_pln2.jpg'),
(17, '2026-05-07 11:03:37', 'dummy_pln3.jpg', 'Gardu listrik mengeluarkan suara letupan kecil.', 15, 'manual', NULL, NULL, 'Gang mawar Ujung', 'selesai', 8, 'bukti_1778152009_triple_t.jpg'),
(18, '2026-05-07 11:03:37', 'dummy_pdam1.jpg', 'Pipa air bersih bocor deras membanjiri jalan raya.', 16, 'peta', '-8.5790', '116.0880', NULL, 'diproses', 9, NULL),
(19, '2026-05-07 11:03:37', 'dummy_pdam2.jpg', 'Fasilitas air siap minum di taman tidak keluar airnya.', 17, 'peta', '-8.5780', '116.0870', NULL, 'selesai', 10, 'bukti_pdam2.jpg'),
(20, '2026-05-07 11:03:37', 'dummy_pdam3.jpg', 'Ada galian pipa yang ditinggalkan begitu saja.', 16, 'manual', NULL, NULL, 'Depan masjid raya', 'menunggu', NULL, NULL),
(21, '2026-05-07 11:03:37', 'dummy_telkom1.jpg', 'Tiang internet miring ke arah rumah warga.', 18, 'peta', '-8.5770', '116.0860', NULL, 'diproses', 11, NULL),
(22, '2026-05-07 11:03:37', 'dummy_telkom2.jpg', 'Kabel fiber optik putus dan menghalangi trotoar.', 18, 'peta', '-8.5760', '116.0850', NULL, 'selesai', 12, 'bukti_telkom2.jpg'),
(23, '2026-05-07 13:20:20', '1778160020_triple_t.jpg', 'efwfewfwf', 1, 'peta', '-8.590716', '116.09301', NULL, 'ditolak', NULL, NULL),
(24, '2026-05-07 14:02:09', 'pu_verif1.jpg', 'Ada lubang menganga di tengah jalan raya bypass, sangat rawan kecelakaan.', 2, 'peta', '-8.5810', '116.0910', NULL, 'menunggu verifikasi', 1, 'bukti_pu_verif1.jpg'),
(25, '2026-05-07 14:02:09', 'pu_verif2.jpg', 'Penutup gorong-gorong hilang dicuri, lubangnya berbahaya bagi pejalan kaki.', 5, 'manual', NULL, NULL, 'Trotoar depan Bank NTB Syariah', 'menunggu verifikasi', 2, 'bukti_pu_verif2.jpg'),
(26, '2026-05-07 14:02:09', 'dishub_verif1.jpg', 'Lampu penerangan jalan di tikungan tajam mati, gelap gulita saat malam.', 6, 'peta', '-8.5822', '116.0922', NULL, 'menunggu verifikasi', 3, 'bukti_dishub_verif1.jpg'),
(27, '2026-05-07 14:02:09', 'dishub_verif2.jpg', 'Rambu penunjuk arah tertutup dahan pohon tebal dan catnya sudah pudar.', 7, 'peta', '-8.5833', '116.0933', NULL, 'menunggu verifikasi', 4, 'bukti_dishub_verif2.jpg'),
(28, '2026-05-07 14:02:09', 'dlh_verif1.jpg', 'Tumpukan sampah liar menutupi sebagian bahu jalan dan berbau busuk.', 12, 'manual', NULL, NULL, 'Jalan Lingkar Selatan dekat tanah kosong', 'menunggu verifikasi', 5, 'bukti_dlh_verif1.jpg'),
(29, '2026-05-07 14:02:09', 'dlh_verif2.jpg', 'Fasilitas bangku di taman kota patah dan kayunya lapuk.', 10, 'peta', '-8.5844', '116.0944', NULL, 'menunggu verifikasi', 6, 'bukti_dlh_verif2.jpg'),
(30, '2026-05-07 14:02:09', 'pln_verif1.jpg', 'Kabel jaringan listrik putus tersangkut truk besar yang lewat.', 14, 'peta', '-8.5855', '116.0955', NULL, 'menunggu verifikasi', 7, 'bukti_pln_verif1.jpg'),
(31, '2026-05-07 14:02:09', 'pln_verif2.jpg', 'Tiang listrik keropos di bagian bawah, takut roboh menimpa rumah warga.', 13, 'manual', NULL, NULL, 'Gang Kamboja No 5, Majeluk', 'selesai', 8, 'bukti_1778162605_triple_t.webp'),
(32, '2026-05-07 14:02:09', 'pdam_verif1.jpg', 'Pipa air bersih utama bocor, airnya membanjiri jalan aspal.', 16, 'peta', '-8.5866', '116.0966', NULL, 'menunggu verifikasi', 9, 'bukti_pdam_verif1.jpg'),
(33, '2026-05-07 14:02:09', 'telkom_verif1.jpg', 'Tiang jaringan Telkom miring dan kabel fiber optiknya menjuntai rendah.', 18, 'peta', '-8.5877', '116.0977', NULL, 'menunggu verifikasi', 11, 'bukti_telkom_verif1.jpg'),
(34, '2026-05-07 14:16:34', '1778163394_28.png', 'dewfrfe', 13, 'peta', '-8.592475825435136', '116.09810829162599', NULL, 'selesai', 7, 'bukti_1778163483_triple_t.jpg'),
(35, '2026-05-07 14:34:17', '1778164457_triple_t.jpg', 'jalan ketimpa pohon', 2, 'peta', '-8.596692564350878', '116.09347075223924', NULL, 'selesai', 2, 'bukti_1778164903_IMG_20251211_193012.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_instansi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `id_user`, `id_instansi`) VALUES
(1, 8, 2),
(2, 9, 2),
(3, 10, 3),
(4, 11, 3),
(5, 12, 4),
(6, 13, 4),
(7, 14, 5),
(8, 15, 5),
(9, 16, 6),
(10, 17, 6),
(11, 18, 7),
(12, 19, 7);

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
(1, 'Admin Operator Pusat', 'admin_pusat', '123', 'admin'),
(2, 'Admin Dinas PU', 'admin_pu', '123', 'admin'),
(3, 'Admin Dinas Perhubungan', 'admin_dishub', '123', 'admin'),
(4, 'Admin Dinas Lingkungan Hidup', 'admin_dlh', '123', 'admin'),
(5, 'Admin PLN', 'admin_pln', '123', 'admin'),
(6, 'Admin PDAM', 'admin_pdam', '123', 'admin'),
(7, 'Admin Telkom', 'admin_telkom', '123', 'admin'),
(8, 'PU 1', 'petugas_pu1', '123', 'petugas'),
(9, 'PU 2', 'petugas_pu2', '123', 'petugas'),
(10, 'Dishub 1', 'petugas_dishub1', '123', 'petugas'),
(11, 'Dishub 2', 'petugas_dishub2', '123', 'petugas'),
(12, 'DLH 1', 'petugas_dlh1', '123', 'petugas'),
(13, 'DLH 2', 'petugas_dlh2', '123', 'petugas'),
(14, 'PLN 1', 'petugas_pln1', '123', 'petugas'),
(15, 'PLN 2', 'petugas_pln2', '123', 'petugas'),
(16, 'PDAM 1', 'petugas_pdam1', '123', 'petugas'),
(17, 'PDAM 2', 'petugas_pdam2', '123', 'petugas'),
(18, 'Telkom 1', 'petugas_telkom1', '123', 'petugas'),
(19, 'Telkom 2', 'petugas_telkom2', '123', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_instansi` (`id_instansi`);

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
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_instansi` (`id_instansi`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_ibfk_2` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD CONSTRAINT `petugas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `petugas_ibfk_2` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
