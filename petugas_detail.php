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
    <style>
        .petugas-container { max-width: 800px; }
        .header-petugas { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        
        /* CSS Badge */
        .badge-hijau { background-color: #2ecc71; padding: 5px 10px; border-radius: 20px; color: white; font-size: 0.85em; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        .badge-oranye { background-color: #e67e22; padding: 5px 10px; border-radius: 20px; color: white; font-size: 0.85em; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        
        .detail-box { background-color: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-align: left; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .detail-item { margin-bottom: 15px; }
        .detail-item strong { display: block; color: #2c3e50; margin-bottom: 5px; }
        
        /* Grid untuk membandingkan foto sebelum dan sesudah */
        .foto-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px; }
        .foto-box { background-color: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px dashed #ccc; text-align: center; }
        .foto-box span { display: block; font-weight: bold; margin-bottom: 10px; color: #555; }
        .foto-laporan { max-width: 100%; height: auto; border-radius: 5px; border: 1px solid #ddd; }

        .btn-kembali { background-color: #95a5a6; color: white; display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;}
        .btn-kembali:hover { background-color: #7f8c8d; }
        
        @media (max-width: 600px) {
            .foto-grid { grid-template-columns: 1fr; } /* Foto bertumpuk ke bawah di layar HP */
        }
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