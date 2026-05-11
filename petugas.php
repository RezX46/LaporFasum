<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>alert('Akses Ditolak! Anda harus login sebagai Petugas.'); window.location.href = 'login.html';</script>";
    exit();
}
require 'koneksi.php';

$id_user_induk = $_SESSION['id_user'];
$query_cari_petugas = mysqli_query($koneksi, "SELECT id_petugas FROM petugas WHERE id_user = '$id_user_induk'");
$data_petugas = mysqli_fetch_assoc($query_cari_petugas);
$id_petugas_asli = $data_petugas['id_petugas'];

// Mengambil SEMUA laporan untuk petugas ini, diurutkan yang "diproses" (Perlu Tindakan) ada di urutan paling atas
$query = "SELECT l.*, k.nama_kategori 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE l.id_petugas = '$id_petugas_asli' 
          ORDER BY l.status = 'diproses' DESC, l.id_laporan DESC";
$result = mysqli_query($koneksi, $query);
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
            <a href="logout.php" class="btn-logout">Keluar</a>
        </div>

        <h2 style="text-align: center; margin-bottom: 20px;">Daftar Tugas Lapangan</h2>

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
                    echo "<tr><td colspan='5' style='text-align:center;'>Belum ada tugas untuk instansi Anda.</td></tr>";
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
                        <a href="petugas_detail.php?id=<?= $row['id_laporan'] ?>" class="btn-detail">Lihat Detail & Eksekusi</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</body>
</html>