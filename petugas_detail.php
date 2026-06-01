<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}

require 'koneksi.php';

if (!isset($_GET['id'])) { die("ID Laporan tidak ditemukan."); }

$id_laporan = (int)$_GET['id'];
$id_petugas_asli = $_SESSION['id_user'];

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
    <title>Detail Tugas – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

    <nav class="site-navbar">
        <a href="petugas.php" class="brand"><span class="brand-lapor">Lapor</span><span class="brand-fasum">Fasum</span></a>
        <nav>
            <a href="petugas.php">Dashboard</a>
            <a href="pengaturan_akun.php">Akun</a>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">Keluar</a>
        </nav>
    </nav>

    <div class="page-header" style="padding:20px 40px;">
        <h1 style="font-size:1.4rem;">Tugas #<?= $row['id_laporan'] ?></h1>
        <p>Tinjau detail dan kirim bukti penyelesaian pekerjaan.</p>
    </div>

    <div class="page-body-narrow">

        <div class="status-bar">
            <h2>Status Tugas</h2>
            <span class="badge <?= $badge_class ?>"><?= $teks_status ?></span>
        </div>

        <?php if (!empty($row['pesan_admin'])): ?>
        <div class="catatan-admin-box">
            <h3>Catatan Admin:</h3>
            <p>"<?= $row['pesan_admin'] ?>"</p>
        </div>
        <?php endif; ?>

        <div class="compact-detail-box">
            <div class="info-grid-detail">
                <div class="info-row">
                    <strong>Tanggal Laporan</strong>
                    <span><?= date('d M Y, H:i', strtotime($row['tanggal_lapor'])) ?> WITA</span>
                </div>
                <div class="info-row">
                    <strong>Kategori</strong>
                    <span><?= $row['nama_kategori'] ?></span>
                </div>
                <div class="info-row full-width">
                    <strong>Keluhan Warga</strong>
                    <span><?= $row['keluhan'] ?></span>
                </div>
                <div class="info-row full-width">
                    <strong>Lokasi</strong>
                    <span>
                        <?= !empty($row['alamat_manual']) ? $row['alamat_manual'] : 'Lihat di Peta (GPS)' ?>
                        <?php if(!empty($row['latitude']) && !empty($row['longitude'])): ?>
                            <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map" style="margin-top:5px;padding:4px 10px;font-size:0.8rem;display:inline-block;"> Buka Lokasi di Maps</a>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <div class="foto-grid" style="margin-top:14px;">
                <div class="foto-box">
                    <span>Kondisi Kerusakan</span>
                    <img src="uploads/<?= $row['foto'] ?>" class="foto-laporan">
                </div>
                <?php if (!empty($row['foto_bukti'])): ?>
                <div class="foto-box" style="border-color:#2ecc71;background:#f0fdf4;">
                    <span style="color:#27ae60;">Hasil Perbaikan Anda</span>
                    <img src="uploads/<?= $row['foto_bukti'] ?>" class="foto-laporan">
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($row['status'] == 'diproses'): ?>
        <div class="compact-action-box">
            <h2 style="color:#27ae60;">Kirim Laporan Selesai</h2>
            <form action="proses_selesai.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                
                <div class="form-group" style="margin-bottom:12px;">
                    <label style="font-size:0.88rem; font-weight:bold; display:block; margin-bottom:6px;">Catatan / Keterangan Lapangan:</label>
                    <textarea name="keterangan_petugas" placeholder="Contoh: Perbaikan selesai, fasilitas sudah dapat digunakan kembali..." required style="width:100%; padding:10px; border-radius:6px; border:1px solid #dbeafe; box-sizing:border-box; min-height:80px; font-family:inherit; font-size:0.9rem;"></textarea>
                </div>

                <div class="form-group" style="margin-bottom:10px;">
                    <label style="font-size:0.88rem; font-weight:bold; display:block; margin-bottom:6px;">Unggah Foto Bukti Perbaikan (Maks 10 MB):</label>
                    <input type="file" name="foto_bukti" accept="image/*" required>
                </div>
                <button type="submit" class="btn-terima"> Kirim Bukti ke Admin</button>
            </form>
        </div>
        <?php endif; ?>

        <div style="margin-top:16px;">
            <button type="button" onclick="history.back()" class="btn-kembali" style="margin-top:0;">← Kembali</button>
        </div>

    </div>

    <footer class="site-footer">© 2025 <span>LaporFasum</span> — Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>