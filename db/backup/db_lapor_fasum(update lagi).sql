-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Bulan Mei 2026 pada 16.52
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
  `status` enum('menunggu','diproses','menunggu verifikasi','selesai','ditolak') DEFAULT 'menunggu',
  `id_petugas` int(11) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `pesan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `tanggal_lapor`, `foto`, `keluhan`, `id_kategori`, `metode_lokasi`, `latitude`, `longitude`, `alamat_manual`, `status`, `id_petugas`, `foto_bukti`, `pesan_admin`) VALUES
(1, '2026-05-07 10:16:01', '1778148961_triple_t.jpg', 'wwfwfwf', 14, 'peta', '-8.590716', '116.09301', NULL, 'selesai', 15, 'bukti_1778152024_triple_t.jpg', NULL),
(2, '2026-05-07 10:34:28', '1778150068_IMG_20251211_193012.jpg', 'bjnkmoi', 10, 'peta', '-8.590716', '116.09301', NULL, 'ditolak', NULL, NULL, NULL),
(3, '2026-05-07 11:03:37', 'dummy_pusat1.jpg', 'Saya tidak tahu ini lapor ke mana, ada tiang miring mau roboh.', 3, 'peta', '-8.5877', '116.0965', NULL, 'diproses', 9, 'bukti_1778484712_triple_t.jpg', 'asdqwdqdqdqdqwdwq'),
(4, '2026-05-07 11:03:37', 'dummy_pusat2.jpg', 'Tolong ini ada fasilitas umum yang rusak parah di pertigaan.', 1, 'manual', NULL, NULL, 'Pertigaan lampu merah pasar', 'menunggu', NULL, NULL, NULL),
(5, '2026-05-07 11:03:37', 'dummy_pu1.jpg', 'Jalan berlubang cukup dalam, sangat bahaya kalau malam.', 2, 'peta', '-8.5880', '116.0970', NULL, 'diproses', 8, NULL, NULL),
(6, '2026-05-07 11:03:37', 'dummy_pu2.jpg', 'Trotoar amblas akibat galian yang tidak ditutup.', 3, 'peta', '-8.5890', '116.0980', NULL, 'selesai', 9, 'bukti_pu2.jpg', NULL),
(7, '2026-05-07 11:03:37', 'dummy_pu3.jpg', 'Jembatan retak di bagian penyangganya.', 4, 'manual', NULL, NULL, 'Jembatan dekat alun-alun', 'diproses', 9, NULL, NULL),
(8, '2026-05-07 11:03:37', 'dummy_pu4.jpg', 'Saluran air mampet dan bau.', 5, 'peta', '-8.5860', '116.0950', NULL, 'ditolak', NULL, NULL, NULL),
(9, '2026-05-07 11:03:37', 'dummy_dishub1.jpg', 'Lampu jalan mati sudah 3 hari, jalanan jadi sangat gelap.', 6, 'peta', '-8.5850', '116.0940', NULL, 'diproses', 11, NULL, NULL),
(10, '2026-05-07 11:03:37', 'dummy_dishub2.jpg', 'Rambu dilarang parkir hilang dicabut orang.', 7, 'manual', NULL, NULL, 'Jalan utama depan SMA 1', 'selesai', 11, 'bukti_dishub2.jpg', NULL),
(11, '2026-05-07 11:03:37', 'dummy_dishub3.jpg', 'Lampu lalu lintas error, hijau semua jadi macet.', 8, 'peta', '-8.5840', '116.0930', NULL, 'menunggu', NULL, NULL, NULL),
(12, '2026-05-07 11:03:37', 'dummy_dlh1.jpg', 'Ayunan di taman kota rusak dan rantainya putus.', 10, 'peta', '-8.5830', '116.0920', NULL, 'diproses', 12, NULL, NULL),
(13, '2026-05-07 11:03:37', 'dummy_dlh2.jpg', 'Pohon tumbang menghalangi sebagian jalan raya.', 11, 'peta', '-8.5820', '116.0910', NULL, 'selesai', 13, 'bukti_dlh2.jpg', NULL),
(14, '2026-05-07 11:03:37', 'dummy_dlh3.jpg', 'Tempat sampah umum sudah hancur dan tumpah berserakan.', 1, 'manual', NULL, NULL, 'Taman bermain komplek', 'menunggu', NULL, NULL, NULL),
(15, '2026-05-07 11:03:37', 'dummy_pln1.jpg', 'Tiang listrik hampir roboh tertabrak truk.', 13, 'peta', '-8.5810', '116.0900', NULL, 'selesai', 14, 'bukti_1778160716_green.png', NULL),
(16, '2026-05-07 11:03:37', 'dummy_pln2.jpg', 'Kabel listrik menjuntai ke jalan, rawan tersangkut kendaraan.', 14, 'peta', '-8.5800', '116.0890', NULL, 'selesai', 15, 'bukti_pln2.jpg', NULL),
(17, '2026-05-07 11:03:37', 'dummy_pln3.jpg', 'Gardu listrik mengeluarkan suara letupan kecil.', 15, 'manual', NULL, NULL, 'Gang mawar Ujung', 'selesai', 15, 'bukti_1778152009_triple_t.jpg', NULL),
(18, '2026-05-07 11:03:37', 'dummy_pdam1.jpg', 'Pipa air bersih bocor deras membanjiri jalan raya.', 16, 'peta', '-8.5790', '116.0880', NULL, 'selesai', 16, 'bukti_1778498336_6a01bb20d857e.jpg', NULL),
(19, '2026-05-07 11:03:37', 'dummy_pdam2.jpg', 'Fasilitas air siap minum di taman tidak keluar airnya.', 17, 'peta', '-8.5780', '116.0870', NULL, 'selesai', 17, 'bukti_pdam2.jpg', NULL),
(20, '2026-05-07 11:03:37', 'dummy_pdam3.jpg', 'Ada galian pipa yang ditinggalkan begitu saja.', 16, 'manual', NULL, NULL, 'Depan masjid raya', 'menunggu', NULL, NULL, NULL),
(21, '2026-05-07 11:03:37', 'dummy_telkom1.jpg', 'Tiang internet miring ke arah rumah warga.', 18, 'peta', '-8.5770', '116.0860', NULL, 'diproses', 18, NULL, NULL),
(22, '2026-05-07 11:03:37', 'dummy_telkom2.jpg', 'Kabel fiber optik putus dan menghalangi trotoar.', 18, 'peta', '-8.5760', '116.0850', NULL, 'selesai', 19, 'bukti_telkom2.jpg', NULL),
(23, '2026-05-07 13:20:20', '1778160020_triple_t.jpg', 'efwfewfwf', 1, 'peta', '-8.590716', '116.09301', NULL, 'ditolak', NULL, NULL, NULL),
(24, '2026-05-07 14:02:09', 'pu_verif1.jpg', 'Ada lubang menganga di tengah jalan raya bypass, sangat rawan kecelakaan.', 2, 'peta', '-8.5810', '116.0910', NULL, 'diproses', 8, 'bukti_pu_verif1.jpg', NULL),
(25, '2026-05-07 14:02:09', 'pu_verif2.jpg', 'Penutup gorong-gorong hilang dicuri, lubangnya berbahaya bagi pejalan kaki.', 5, 'manual', NULL, NULL, 'Trotoar depan Bank NTB Syariah', 'diproses', 9, 'bukti_pu_verif2.jpg', NULL),
(26, '2026-05-07 14:02:09', 'dishub_verif1.jpg', 'Lampu penerangan jalan di tikungan tajam mati, gelap gulita saat malam.', 6, 'peta', '-8.5822', '116.0922', NULL, 'menunggu verifikasi', 10, 'bukti_dishub_verif1.jpg', NULL),
(27, '2026-05-07 14:02:09', 'dishub_verif2.jpg', 'Rambu penunjuk arah tertutup dahan pohon tebal dan catnya sudah pudar.', 7, 'peta', '-8.5833', '116.0933', NULL, 'menunggu verifikasi', 11, 'bukti_dishub_verif2.jpg', NULL),
(28, '2026-05-07 14:02:09', 'dlh_verif1.jpg', 'Tumpukan sampah liar menutupi sebagian bahu jalan dan berbau busuk.', 12, 'manual', NULL, NULL, 'Jalan Lingkar Selatan dekat tanah kosong', 'menunggu verifikasi', 12, 'bukti_dlh_verif1.jpg', NULL),
(29, '2026-05-07 14:02:09', 'dlh_verif2.jpg', 'Fasilitas bangku di taman kota patah dan kayunya lapuk.', 10, 'peta', '-8.5844', '116.0944', NULL, 'selesai', 13, 'bukti_dlh_verif2.jpg', NULL),
(30, '2026-05-07 14:02:09', 'pln_verif1.jpg', 'Kabel jaringan listrik putus tersangkut truk besar yang lewat.', 14, 'peta', '-8.5855', '116.0955', NULL, 'selesai', 15, 'bukti_1778416865_triple_t.jpg', NULL),
(31, '2026-05-07 14:02:09', 'pln_verif2.jpg', 'Tiang listrik keropos di bagian bawah, takut roboh menimpa rumah warga.', 13, 'manual', NULL, NULL, 'Gang Kamboja No 5, Majeluk', 'selesai', 15, 'bukti_1778162605_triple_t.webp', NULL),
(32, '2026-05-07 14:02:09', 'pdam_verif1.jpg', 'Pipa air bersih utama bocor, airnya membanjiri jalan aspal.', 16, 'peta', '-8.5866', '116.0966', NULL, 'diproses', 17, 'bukti_1778498800_6a01bcf0b7b00.jpg', NULL),
(33, '2026-05-07 14:02:09', 'telkom_verif1.jpg', 'Tiang jaringan Telkom miring dan kabel fiber optiknya menjuntai rendah.', 18, 'peta', '-8.5877', '116.0977', NULL, 'menunggu verifikasi', 18, 'bukti_telkom_verif1.jpg', NULL),
(34, '2026-05-07 14:16:34', '1778163394_28.png', 'dewfrfe', 13, 'peta', '-8.592475825435136', '116.09810829162599', NULL, 'selesai', 14, 'bukti_1778163483_triple_t.jpg', NULL),
(35, '2026-05-07 14:34:17', '1778164457_triple_t.jpg', 'jalan ketimpa pohon', 2, 'peta', '-8.596692564350878', '116.09347075223924', NULL, 'selesai', 9, 'bukti_1778164903_IMG_20251211_193012.jpg', NULL),
(36, '2026-05-11 01:44:43', '1778463883_triple_t.jpg', 'wlbwgjw4gn', 4, 'peta', '', '', NULL, 'diproses', 9, 'bukti_1778463978_triple_t.jpg', NULL),
(37, '2026-05-11 03:09:21', '1778468961_triple_t.jpg', 'ngiunenn', 2, 'peta', '-8.586877', '116.096839', NULL, 'diproses', 8, NULL, NULL),
(38, '2026-05-11 05:25:43', '1778477142_triple_t.jpg', 'jalan rusak', 4, 'peta', '-8.580254733695252', '116.10291481018068', NULL, 'selesai', 8, 'bukti_1778477294_triple_t.jpg', NULL),
(39, '2026-05-11 05:53:30', '1778478810_triple_t.jpg', 'hhtyrhrht', 10, 'peta', '-8.587460018980236', '116.09719205139162', NULL, 'selesai', 13, 'bukti_1778497997_6a01b9cdb1c3b.jpg', NULL),
(40, '2026-05-11 06:12:13', '1778479933_triple_t.jpg', 'jrgnkjaakngkjssk.e', 15, 'peta', '-8.587462522722806', '116.09719176978331', NULL, 'selesai', 14, 'bukti_1778683310_6a048daedb981.jpg', NULL),
(41, '2026-05-11 07:22:51', '1778484171_triple_t.jpg', 'gearesgrseg', 2, 'peta', '-8.590922', '116.093062', NULL, 'selesai', 8, 'bukti_1778484558_IMG_20251211_193012.jpg', NULL),
(42, '2026-05-11 08:08:36', '1778486916_triple_t.jpg', 'sdffefw', 3, 'peta', '-8.590922', '116.093062', NULL, 'selesai', 9, 'bukti_1778487295_IMG_20251211_193012.jpg', NULL),
(43, '2026-05-11 10:50:05', '1778496605_6a01b45d5ba98.jpg', 'vsdvs', 16, 'peta', '-8.590716', '116.09301', NULL, 'menunggu', NULL, NULL, NULL),
(44, '2026-05-11 10:51:54', '1778496714_6a01b4ca69b97.jpg', 'vdsvsds', 6, 'peta', '-8.59060873967416', '116.09192848205568', NULL, 'menunggu', NULL, NULL, NULL),
(45, '2026-05-12 02:35:37', '1778553337_6a0291f93ce51.jpg', 'fnengrrnerkgnreg jherg', 2, 'peta', '-8.586828', '116.096756', NULL, 'menunggu', NULL, NULL, NULL),
(46, '2026-05-12 02:40:17', '1778553617_6a029311a3a9c.jpg', 'dsvfdsfv', 2, 'peta', '-8.586792', '116.09666', NULL, 'menunggu', NULL, NULL, NULL),
(47, '2026-05-13 03:36:11', '1778643371_6a03f1ab50eec.jpg', ' vkernnpergnegn', 6, 'peta', '-8.587217347538575', '116.0959230146951', NULL, 'menunggu', NULL, NULL, NULL),
(48, '2026-05-13 03:38:04', '1778643484_6a03f21c56345.jpg', 'reojnowfnkelgr', 10, 'peta', '-8.587217347538575', '116.0959230146951', NULL, 'selesai', 12, 'bukti_1778643662_6a03f2ce2c566.jpg', NULL),
(49, '2026-05-13 14:44:51', '1778683490_6a048e62f1513.jpg', 'fsggrsgrgr', 16, 'peta', '-8.5865', '116.0798', NULL, 'selesai', 16, 'bukti_1778683697_6a048f31e5f75.jpg', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_laporan`
--

CREATE TABLE `riwayat_laporan` (
  `id_riwayat` int(11) NOT NULL,
  `id_laporan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_petugas_penerima` int(11) DEFAULT NULL,
  `aksi` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal_aksi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat_laporan`
--

INSERT INTO `riwayat_laporan` (`id_riwayat`, `id_laporan`, `id_user`, `id_petugas_penerima`, `aksi`, `keterangan`, `tanggal_aksi`) VALUES
(1, 14, 4, NULL, 'kembalikan', 'dkjgnegrkgmekgm', '2026-05-11 06:11:28'),
(2, 40, 2, NULL, 'kembalikan', 'e kjsnvsm;l', '2026-05-11 06:12:31'),
(3, 40, 1, NULL, 'forward', 'Diteruskan ke kategori baru (ID Kategori: 15).', '2026-05-11 06:13:33'),
(4, 40, 5, NULL, 'terima', 'Menugaskan petugas lapangan (ID: 7).', '2026-05-11 06:14:05'),
(5, 7, 2, NULL, 'terima', 'Menugaskan petugas lapangan (ID: 2).', '2026-05-11 06:42:52'),
(6, 4, 2, NULL, 'kembalikan', 'esvbdgnfh', '2026-05-11 06:43:03'),
(7, 41, 1, NULL, 'forward', 'Diteruskan ke kategori baru (ID Kategori: 2).', '2026-05-11 07:24:11'),
(8, 41, 2, NULL, 'kembalikan', 'bukan wewenang kami', '2026-05-11 07:25:11'),
(9, 41, 1, NULL, 'forward', 'Diteruskan ke kategori baru (ID Kategori: 2).', '2026-05-11 07:25:29'),
(10, 41, 2, NULL, 'terima', 'Menugaskan petugas lapangan (ID: 1).', '2026-05-11 07:25:48'),
(11, 41, 2, NULL, 'verifikasi_tolak', 'Bukti ditolak. Dikembalikan ke petugas semula. Alasan: salah woi', '2026-05-11 07:27:34'),
(12, 41, 2, NULL, 'verifikasi_tolak', 'Bukti ditolak. Dikembalikan ke petugas semula. Alasan: masih salah', '2026-05-11 07:28:44'),
(13, 41, 2, NULL, 'verifikasi_terima', 'Bukti disetujui. Tugas selesai.', '2026-05-11 07:29:39'),
(14, 3, 2, NULL, 'terima', 'Menugaskan petugas lapangan (ID: 1).', '2026-05-11 07:31:29'),
(15, 3, 2, NULL, 'verifikasi_tolak', 'Bukti ditolak. Tugas dialihkan ke petugas baru. Alasan: asdqwdqdqdqdqwdwq', '2026-05-11 07:32:23'),
(16, 2, 4, NULL, 'tolak', 'Laporan ditolak (Dianggap Spam / Tidak Valid).', '2026-05-11 07:42:36'),
(17, 42, 1, NULL, 'forward', 'Laporan diteruskan ke kategori/dinas baru.', '2026-05-11 08:09:13'),
(18, 42, 4, NULL, 'kembalikan', 'sgwegwrg', '2026-05-11 08:10:05'),
(19, 42, 1, NULL, 'forward', 'Laporan diteruskan ke kategori/dinas baru.', '2026-05-11 08:10:23'),
(20, 42, 2, NULL, 'terima', 'Laporan diterima dan ditugaskan ke petugas.', '2026-05-11 08:10:44'),
(21, 42, 2, NULL, 'verifikasi_tolak', 'Bukti ditolak. Alasan: asdfegwr', '2026-05-11 08:12:57'),
(22, 42, 2, 8, 'verifikasi_tolak', 'Bukti ditolak. Alasan: absaefwarbg', '2026-05-11 08:13:53'),
(23, 42, 2, NULL, 'verifikasi_terima', 'Bukti perbaikan disetujui. Tugas selesai.', '2026-05-11 08:15:17'),
(24, 39, 12, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-11 11:04:39'),
(25, 39, 4, 12, 'verifikasi_tolak', 'Bukti ditolak. Alasan: jvhbuybh', '2026-05-11 11:05:12'),
(26, 39, 13, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-11 11:13:18'),
(27, 39, 4, NULL, 'verifikasi_terima', 'Bukti perbaikan disetujui. Tugas selesai.', '2026-05-11 11:13:34'),
(28, 18, 16, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-11 11:18:57'),
(29, 18, 6, NULL, 'verifikasi_terima', 'Bukti perbaikan disetujui. Tugas selesai.', '2026-05-11 11:19:43'),
(30, 32, 6, NULL, 'verifikasi_tolak', 'Bukti ditolak. Alasan: vwfrve', '2026-05-11 11:26:07'),
(31, 32, 16, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-11 11:26:41'),
(32, 32, 6, 16, 'verifikasi_tolak', 'Bukti ditolak. Alasan: afwewfewfwfwffw', '2026-05-11 11:27:03'),
(33, 9, 10, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-11 11:29:14'),
(34, 9, 3, 10, 'verifikasi_tolak', 'Bukti ditolak. Alasan: efwrerff', '2026-05-11 11:29:46'),
(35, 40, 14, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-12 00:02:09'),
(36, 46, 1, NULL, 'forward', 'Laporan diteruskan ke kategori/dinas baru.', '2026-05-12 02:40:41'),
(37, 48, 2, NULL, 'kembalikan', 'w kfnwn jwf', '2026-05-13 03:38:28'),
(38, 48, 1, NULL, 'forward', 'Laporan diteruskan ke kategori/dinas baru.', '2026-05-13 03:39:00'),
(39, 48, 4, NULL, 'terima', 'Laporan diterima dan ditugaskan ke petugas.', '2026-05-13 03:40:16'),
(40, 48, 12, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-13 03:41:02'),
(41, 48, 4, NULL, 'verifikasi_terima', 'Bukti perbaikan disetujui. Tugas selesai.', '2026-05-13 03:41:52'),
(42, 40, 5, NULL, 'verifikasi_tolak', 'Bukti ditolak. Alasan: afsrfe', '2026-05-13 14:40:34'),
(43, 40, 14, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-13 14:41:51'),
(44, 40, 5, NULL, 'verifikasi_terima', 'Bukti perbaikan disetujui. Tugas selesai.', '2026-05-13 14:42:03'),
(45, 49, 1, NULL, 'forward', 'Laporan diteruskan ke kategori/dinas baru.', '2026-05-13 14:45:44'),
(46, 49, 6, NULL, 'terima', 'Laporan diterima dan ditugaskan ke petugas.', '2026-05-13 14:45:56'),
(47, 49, 17, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-13 14:46:58'),
(48, 49, 6, 17, 'verifikasi_tolak', 'Bukti ditolak. Alasan: ssdfsrg', '2026-05-13 14:47:29'),
(49, 49, 16, NULL, 'kirim_bukti', 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.', '2026-05-13 14:48:18'),
(50, 49, 6, NULL, 'verifikasi_terima', 'Bukti perbaikan disetujui. Tugas selesai.', '2026-05-13 14:49:02');

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
  `id_instansi` int(11) DEFAULT NULL
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
(10, 'Dishub 1', 'petugas_dishub1', '123', 'petugas', 3),
(11, 'Dishub 2', 'petugas_dishub2', '123', 'petugas', 3),
(12, 'DLH 1', 'petugas_dlh1', '123', 'petugas', 4),
(13, 'DLH 2', 'petugas_dlh2', '123', 'petugas', 4),
(14, 'PLN 1', 'petugas_pln1', '123', 'petugas', 5),
(15, 'PLN 2', 'petugas_pln2', '123', 'petugas', 5),
(16, 'PDAM 1', 'petugas_pdam1', '123', 'petugas', 6),
(17, 'PDAM 2', 'petugas_pdam2', '123', 'petugas', 6),
(18, 'Telkom 1', 'petugas_telkom1', '123', 'petugas', 7),
(19, 'Telkom 2', 'petugas_telkom2', '123', 'petugas', 7);

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
  ADD KEY `laporan_ibfk_2` (`id_petugas`);

--
-- Indeks untuk tabel `riwayat_laporan`
--
ALTER TABLE `riwayat_laporan`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_laporan` (`id_laporan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `fk_user_instansi` (`id_instansi`);

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
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `riwayat_laporan`
--
ALTER TABLE `riwayat_laporan`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- Ketidakleluasaan untuk tabel `riwayat_laporan`
--
ALTER TABLE `riwayat_laporan`
  ADD CONSTRAINT `riwayat_laporan_ibfk_1` FOREIGN KEY (`id_laporan`) REFERENCES `laporan` (`id_laporan`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_laporan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_instansi` FOREIGN KEY (`id_instansi`) REFERENCES `instansi` (`id_instansi`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
