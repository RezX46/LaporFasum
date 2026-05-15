<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>alert('Akses Ditolak! Anda harus login sebagai Petugas.'); window.location.href = 'login.html';</script>";
    exit();
}
require 'koneksi.php';

$id_petugas_asli = $_SESSION['id_user'];

$query = "SELECT l.*, k.nama_kategori 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE l.id_petugas = '$id_petugas_asli' 
          ORDER BY l.status = 'diproses' DESC, l.id_laporan DESC";
$result = mysqli_query($koneksi, $query);

$query_notif = "SELECT r.*, l.id_laporan, k.nama_kategori 
                FROM riwayat_laporan r 
                JOIN laporan l ON r.id_laporan = l.id_laporan 
                JOIN kategori k ON l.id_kategori = k.id_kategori
                WHERE r.id_petugas_penerima = '$id_petugas_asli' 
                ORDER BY r.tanggal_aksi DESC";
$q_notif = mysqli_query($koneksi, $query_notif);
$jumlah_notif = mysqli_num_rows($q_notif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

    <div class="container admin-container">
        
        <div class="header-petugas">
            <div style="text-align: left;">
                <h1 style="margin-bottom: 5px; font-size: 1.5em;">Dashboard Petugas</h1>
                <p style="margin: 0; font-size: 0.9em;">Selamat datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
            </div>
            
            <div style="display: flex; align-items: center;">
                <button class="btn-notif" onclick="bukaNotif()">
                     Notifikasi (<?= $jumlah_notif ?>)
                </button>
                <a href="pengaturan_akun.php" class="btn-detail" style="background-color: #3498db;">Pengaturan Akun</a>
                <a href="logout.php" class="btn-logout">Keluar</a>
            </div>
        </div>

        <h2 style="text-align: center; margin-bottom: 20px;">Daftar Tugas Lapangan</h2>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal Masuk</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='5' style='text-align:center;'>Belum ada tugas untuk Anda.</td></tr>";
                    }
                    while($row = mysqli_fetch_assoc($result)) { 
                        $badge_class = 'badge-kuning'; 
                        if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
                        elseif ($row['status'] == 'menunggu verifikasi') { $badge_class = 'badge-oranye'; }
                        elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
                    ?>
                    <tr>
                        <td>#<?= $row['id_laporan'] ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_lapor'])) ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td>
                            <a href="petugas_detail.php?id=<?= $row['id_laporan'] ?>" class="btn-detail">Lihat Detail </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

    <div id="notifModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="tutupNotif()">&times;</span>
            <h2 style="margin-top: 0; margin-bottom: 20px; color: #34495e; border-bottom: 2px solid #f39c12; padding-bottom: 10px;">
                Pesan / Instruksi Admin
            </h2>
            
            <?php if($jumlah_notif == 0): ?>
                <p style="text-align: center; color: #7f8c8d; padding: 20px 0;">Tidak ada pesan baru.</p>
            <?php else: ?>
                <?php while($n = mysqli_fetch_assoc($q_notif)): ?>
                    <div class="notif-item">
                        <span class="notif-time"><?= date('d M Y, H:i', strtotime($n['tanggal_aksi'])) ?> WITA</span>
                        <span class="notif-title">Laporan #<?= $n['id_laporan'] ?> (<?= $n['nama_kategori'] ?>)</span>
                        <p class="notif-msg">"<?= $n['keterangan'] ?>"</p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="assets/js/notif.js?v=<?= time(); ?>"></script>
</body>
</html>