<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}

require 'koneksi.php';

if (!isset($_GET['id'])) { die("ID Laporan tidak ditemukan."); }

$id_laporan = (int)$_GET['id'];
$id_user_induk = $_SESSION['id_user'];

$query_cari_petugas = mysqli_query($koneksi, "SELECT id_petugas FROM petugas WHERE id_user = '$id_user_induk'");
$data_petugas = mysqli_fetch_assoc($query_cari_petugas);
$id_petugas_asli = $data_petugas['id_petugas'];

$query = "SELECT l.*, k.nama_kategori 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE l.id_laporan = $id_laporan";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) { die("Data tidak ditemukan."); }
$row = mysqli_fetch_assoc($result);

if ($row['id_petugas'] != $id_petugas_asli) {
    die("Akses Ditolak! Laporan ini bukan riwayat tugas Anda.");
}

$badge_class = 'badge-hijau';
$teks_status = 'Selesai (Diverifikasi)';

if ($row['status'] == 'menunggu verifikasi') {
    $badge_class = 'badge-oranye';
    $teks_status = 'Menunggu Verifikasi Admin';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    </style>
</head>
<body>

    <div class="container petugas-container">
        
        <div class="header-petugas">
            <h1 style="margin-bottom: 0;">Tugas #<?= $row['id_laporan'] ?></h1>
            <span class="<?= $badge_class ?>">Status: <?= $teks_status ?></span>
        </div>

        <div class="detail-box">
            <div class="detail-item">
                <strong>Tanggal Laporan Masuk:</strong>
                <?= date('d M Y, H:i', strtotime($row['tanggal_lapor'])) ?> WITA
            </div>
            <div class="detail-item">
                <strong>Kategori Fasilitas:</strong>
                <?= $row['nama_kategori'] ?>
            </div>
            <div class="detail-item">
                <strong>Keluhan Warga:</strong>
                <?= $row['keluhan'] ?>
            </div>
            <div class="detail-item">
                <strong>Lokasi:</strong>
                <?= !empty($row['alamat_manual']) ? $row['alamat_manual'] : 'Titik Koordinat Peta (GPS)' ?>
            </div>

            <div class="foto-grid">
                <div class="foto-box">
                    <span>🔴 Kondisi Awal </span>
                    <img src="uploads/<?= $row['foto'] ?>" alt="Foto Kondisi Awal" class="foto-laporan">
                </div>
                
                <div class="foto-box" style="border-color: #2ecc71; background-color: #f0fdf4;">
                    <span style="color: #27ae60;">🟢 Hasil Perbaikan </span>
                    <img src="uploads/<?= $row['foto_bukti'] ?>" alt="Foto Bukti Perbaikan" class="foto-laporan">
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: left;">
            <a href="petugas.php" class="btn-kembali">← Kembali ke Dashboard</a>
        </div>

    </div>
</body>
</html>