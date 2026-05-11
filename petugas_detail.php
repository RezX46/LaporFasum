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

$badge_class = 'badge-kuning';
if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
elseif ($row['status'] == 'menunggu verifikasi') { $badge_class = 'badge-oranye'; }
elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

    <div class="container admin-detail-container">
        
        <div class="header-petugas">
            <h1 style="margin-bottom: 0;">Tugas #<?= $row['id_laporan'] ?></h1>
            <span class="badge <?= $badge_class ?>">Status: <?= ucfirst($row['status']) ?></span>
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
                <strong>Lokasi / Patokan:</strong>
                <?= !empty($row['alamat_manual']) ? $row['alamat_manual'] : 'Titik Koordinat Peta (GPS)' ?>
                <br>
                <?php if($row['latitude'] != NULL && $row['longitude'] != NULL): ?>
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map">📍 Buka di Google Maps</a>
                <?php endif; ?>
            </div>

            <div class="detail-item">
                <strong>Foto Kondisi Kerusakan (Awal):</strong>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Kondisi Awal" class="foto-laporan">
            </div>
        </div>

        <?php if ($row['status'] == 'diproses'): ?>
            <div class="action-box" style="border-color: #3498db;">
                <h2 style="margin-top: 0; color: #2980b9;">Tindakan Penyelesaian</h2>
                <p style="font-size: 0.9em; margin-bottom: 15px;">Pekerjaan telah selesai? Silakan unggah foto hasil perbaikan di bawah ini untuk diverifikasi oleh Admin.</p>
                
                <form action="proses_selesai.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="font-size: 0.9em; font-weight: bold;">Unggah Foto Hasil Perbaikan:</label>
                        <input type="file" name="foto_bukti" accept="image/*" required style="margin-bottom: 0;">
                    </div>

                    <button type="submit" class="btn-selesai">✔️ Kirim Bukti dan Selesaikan Tugas</button>
                </form>
            </div>
            
        <?php elseif ($row['status'] == 'menunggu verifikasi' || $row['status'] == 'selesai'): ?>
            <div class="action-box" style="border-color: #2ecc71;">
                <h2 style="margin-top: 0; color: #27ae60;">Bukti Perbaikan yang Dikirim</h2>
                <div style="text-align: center; margin-top: 15px;">
                    <img src="uploads/<?= $row['foto_bukti'] ?>" alt="Foto Bukti Perbaikan" class="foto-laporan" style="border: 3px solid #2ecc71;">
                </div>
            </div>
        <?php endif; ?>

        <div style="margin-top: 20px; text-align: left;">
            <a href="petugas.php" class="btn-kembali" style="margin-top: 0;">← Kembali ke Dashboard</a>
        </div>

    </div>
</body>
</html>