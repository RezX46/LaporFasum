<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>
            alert('Akses Ditolak! Anda harus login sebagai Petugas.');
            window.location.href = 'login.html';
          </script>";
    exit();
}

require 'koneksi.php';

$id_user_induk = $_SESSION['id_user'];

$query_cari_petugas = mysqli_query($koneksi, "SELECT id_petugas FROM petugas WHERE id_user = '$id_user_induk'");
$data_petugas = mysqli_fetch_assoc($query_cari_petugas);
$id_petugas_asli = $data_petugas['id_petugas'];

$query_aktif = "SELECT l.*, k.nama_kategori 
                FROM laporan l 
                JOIN kategori k ON l.id_kategori = k.id_kategori 
                WHERE l.id_petugas = '$id_petugas_asli' AND l.status = 'diproses' 
                ORDER BY l.id_laporan DESC";
$result_aktif = mysqli_query($koneksi, $query_aktif);

$query_selesai = "SELECT l.*, k.nama_kategori 
                  FROM laporan l 
                  JOIN kategori k ON l.id_kategori = k.id_kategori 
                  WHERE l.id_petugas = '$id_petugas_asli' 
                  AND (l.status = 'selesai' OR l.status = 'menunggu verifikasi') 
                  ORDER BY l.id_laporan DESC";
$result_selesai = mysqli_query($koneksi, $query_selesai);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container petugas-container">
        
        <div class="header-petugas">
            <div style="text-align: left;">
                <h1 style="margin-bottom: 5px; font-size: 1.5em;">Dashboard Petugas</h1>
                <p style="margin: 0; font-size: 0.9em;"><strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
            </div>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </div>

        <h2 class="section-title">Tugas Harus Dikerjakan</h2>

        <?php if(mysqli_num_rows($result_aktif) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result_aktif)): ?>
            <div class="task-card">
                <div class="task-header">
                    <strong>Laporan #<?= $row['id_laporan'] ?> (<?= $row['nama_kategori'] ?>)</strong>
                    <span class="badge-kuning">Perlu Tindakan</span>
                </div>
                
                <p style="margin-bottom: 5px;"><strong>Patokan Lokasi:</strong> 
                    <?= !empty($row['alamat_manual']) ? $row['alamat_manual'] : 'GPS / Titik Koordinat' ?>
                </p>
                
                <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map">🗺️ Buka Maps</a>
                
                <p style="margin-bottom: 10px; margin-top: 10px; font-size: 0.9em; color: #666;"><strong>Keluhan:</strong> <?= $row['keluhan'] ?></p>
                <p style="margin-bottom: 5px; font-size: 0.9em;"><strong>Foto Kondisi Kerusakan:</strong></p>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Kondisi Awal" style="max-width: 100%; height: auto; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 15px;">
                
                <form class="proof-section" action="proses_selesai.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <p style="font-weight: bold; margin-bottom: 10px; font-size: 0.9em;">Tindakan Penyelesaian:</p>
                    
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="font-size: 0.85em;">Unggah Foto Hasil Perbaikan:</label>
                        <input type="file" name="foto_bukti" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn-selesai">✔️ Kirim Bukti dan Laporan </button>
                </form>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: left; color: #7f8c8d; font-style: italic;">Tidak ada tugas baru saat ini.</p>
        <?php endif; ?>

        <h2 class="section-title" style="margin-top: 40px;">Riwayat Tugas</h2>

        <?php if(mysqli_num_rows($result_selesai) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result_selesai)): ?>
            <div class="task-card" style="opacity: 0.9;">
                <div class="task-header">
                    <strong>Laporan #<?= $row['id_laporan'] ?></strong>
                    
                    <?php if ($row['status'] == 'menunggu verifikasi'): ?>
                        <span class="badge-oranye">Menunggu Verifikasi Admin</span>
                    <?php else: ?>
                        <span class="badge-hijau">Selesai</span>
                    <?php endif; ?>

                </div>
                <p style="margin-bottom: 5px;"><strong>Kategori:</strong> <?= $row['nama_kategori'] ?></p>
                
                <a href="petugas_detail.php?id=<?= $row['id_laporan'] ?>" class="btn-detail">Lihat Detail & Foto Bukti</a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: left; color: #7f8c8d; font-style: italic;">Belum ada riwayat tugas yang diselesaikan.</p>
        <?php endif; ?>

    </div>

</body>
</html>