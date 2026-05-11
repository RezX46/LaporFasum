<?php
session_start();

// Hanya petugas yang boleh masuk
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
$row = mysqli_fetch_assoc($result);

if (!$row || $row['id_petugas'] != $id_petugas_asli) {
    die("Akses Ditolak! Laporan ini bukan wilayah tugas Anda.");
}

$query_riwayat = "SELECT r.*, u.nama_lengkap 
                  FROM riwayat_laporan r 
                  JOIN users u ON r.id_user = u.id_user 
                  WHERE r.id_laporan = $id_laporan 
                  ORDER BY r.tanggal_aksi DESC";
$riwayat = mysqli_query($koneksi, $query_riwayat);

$badge_class = 'badge-biru';
$teks_status = 'Sedang Diproses';

if ($row['status'] == 'menunggu verifikasi') {
    $badge_class = 'badge-oranye';
    $teks_status = 'Menunggu Verifikasi Admin';
} elseif ($row['status'] == 'selesai') {
    $badge_class = 'badge-hijau';
    $teks_status = 'Selesai (Diverifikasi)';
}
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
        
        <div class="header-admin">
            <h1 style="margin-bottom: 0;">Tugas #<?= $row['id_laporan'] ?></h1>
            <span class="badge <?= $badge_class ?>">Status: <?= $teks_status ?></span>
        </div>

        <?php if (!empty($row['pesan_admin'])): ?>
            <div class="action-box" style="border-color: #e74c3c; background-color: #fff5f5; border-style: solid;">
                <h3 style="color: #e74c3c; margin-top: 0;">Catatan Admin:</h3>
                <p style="color: #333; font-weight: bold; font-style: italic; margin-top: 10px;">
                    "<?= $row['pesan_admin'] ?>"
                </p>
            </div>
        <?php endif; ?>

        <div class="detail-box">
            <div class="detail-item">
                <strong>Tanggal Laporan:</strong>
                <?= date('d M Y, H:i', strtotime($row['tanggal_lapor'])) ?> WITA
            </div>
            <div class="detail-item">
                <strong>Kategori:</strong>
                <?= $row['nama_kategori'] ?>
            </div>
            <div class="detail-item">
                <strong>Keluhan Warga:</strong>
                <?= $row['keluhan'] ?>
            </div>
            <div class="detail-item">
                <strong>Lokasi:</strong>
                <?= !empty($row['alamat_manual']) ? $row['alamat_manual'] : 'Lihat di Peta (GPS)' ?>
                <br>
                <?php if($row['latitude'] != NULL && $row['longitude'] != NULL): ?>
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map"> Buka Lokasi di Maps</a>
                <?php endif; ?>
            </div>

            <div class="foto-grid">
                <div class="foto-box">
                    <span>🔴 Kondisi Kerusakan</span>
                    <img src="uploads/<?= $row['foto'] ?>" class="foto-laporan">
                </div>
                <?php if (!empty($row['foto_bukti'])): ?>
                <div class="foto-box" style="border-color: #2ecc71; background-color: #f0fdf4;">
                    <span style="color: #27ae60;">🟢 Hasil Perbaikan Anda</span>
                    <img src="uploads/<?= $row['foto_bukti'] ?>" class="foto-laporan">
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($row['status'] == 'diproses'): ?>
            <div class="action-box">
                <h2 style="margin-top: 0; color: #2ecc71;">Kirim Laporan Selesai</h2>
                <form action="proses_selesai.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <div class="form-group">
                        <label>Unggah Foto Bukti Perbaikan (Maks 10 MB):</label>
                        <input type="file" name="foto_bukti" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn-terima"> Kirim Bukti ke Admin</button>
                </form>
            </div>
        <?php endif; ?>
        <div style="margin-top: 20px;">
            <a href="petugas.php" class="btn-kembali">← Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>