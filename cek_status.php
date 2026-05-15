<?php
require 'koneksi.php';

$data_laporan = null;
$error = "";

if (isset($_GET['kode'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['kode']);
    
    $query = "SELECT l.*, k.nama_kategori, i.nama_instansi 
              FROM laporan l 
              JOIN kategori k ON l.id_kategori = k.id_kategori 
              JOIN instansi i ON k.id_instansi = i.id_instansi
              WHERE l.kode_lacak = '$kode'";
              
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data_laporan = mysqli_fetch_assoc($result);
        
        $status_asli = $data_laporan['status'];
        if ($status_asli == 'menunggu' || $status_asli == 'diproses' || $status_asli == 'menunggu verifikasi') {
            $tampilan_status = "Sedang Diproses";
            $badge_class = "badge-biru";
        } elseif ($status_asli == 'selesai') {
            $tampilan_status = "Selesai Dikerjakan";
            $badge_class = "badge-hijau";
        } else {
            $tampilan_status = "Laporan Ditolak";
            $badge_class = "badge-merah";
        }
    } else {
        $error = "Kode lacak tidak ditemukan. Pastikan kode yang Anda masukkan benar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Status Laporan - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <h1>Cek Status Laporan</h1>
        
        <form action="" method="GET" style="margin-bottom: 30px;">
            <input type="text" name="kode" placeholder="Masukkan Kode Lacak (Contoh: LP-XXXXXX)" required value="<?= isset($_GET['kode']) ? $_GET['kode'] : '' ?>">
            <button type="submit" class="btn">Cari Laporan</button>
        </form>

        <?php if ($error): ?>
            <p style="color: #e74c3c; font-weight: bold; background: #fee; padding: 10px; border-radius: 5px;"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($data_laporan): ?>
            <div class="detail-box">
                <div class="header-admin" style="border: none; padding: 0; margin-bottom: 15px;">
                    <h2 style="margin: 0;">Laporan #<?= $data_laporan['kode_lacak'] ?></h2>
                    <span class="badge <?= $badge_class ?>"><?= $tampilan_status ?></span>
                </div>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                
                <div class="detail-item"><strong>Instansi Penanggung Jawab:</strong> <?= $data_laporan['nama_instansi'] ?></div>
                <div class="detail-item"><strong>Kategori:</strong> <?= $data_laporan['nama_kategori'] ?></div>
                <div class="detail-item"><strong>Keluhan Anda:</strong> <?= $data_laporan['keluhan'] ?></div>
                
                <div class="detail-item">
                    <strong>Lokasi / Alamat:</strong><br>
                    <?= !empty($data_laporan['alamat_manual']) ? $data_laporan['alamat_manual'] : 'Lokasi berdasarkan titik GPS' ?>
                    
                    <?php if(!empty($data_laporan['latitude']) && !empty($data_laporan['longitude'])): ?>
                        <a href="https://www.google.com/maps?q=<?= $data_laporan['latitude'] ?>,<?= $data_laporan['longitude'] ?>" target="_blank" class="btn-map"> Buka Lokasi di Google Maps</a>
                    <?php endif; ?>
                </div>

                <div class="foto-grid">
                    <div class="foto-box">
                        <span>Foto Laporan</span>
                        <img src="uploads/<?= $data_laporan['foto'] ?>" class="foto-laporan">
                    </div>
                    <?php if ($status_asli == 'selesai' && !empty($data_laporan['foto_bukti'])): ?>
                    <div class="foto-box" style="border-color: #2ecc71;">
                        <span style="color: #27ae60;">Hasil Perbaikan</span>
                        <img src="uploads/<?= $data_laporan['foto_bukti'] ?>" class="foto-laporan">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="index.html" class="back-link">← Kembali ke Halaman Utama</a>
    </div>
</body>
</html>