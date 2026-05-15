<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>
            alert('Akses Ditolak! Anda harus login sebagai Admin.');
            window.location.href = 'login.html';
          </script>";
    exit();
}
require 'koneksi.php';

// Mengambil ID Instansi dari Admin yang sedang login
$id_instansi_admin = $_SESSION['id_instansi'];

$query = "SELECT l.*, k.nama_kategori 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE k.id_instansi = '$id_instansi_admin' 
          ORDER BY l.id_laporan DESC";
          
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container admin-container">
        
        <div class="header-admin">
            <div>
                <h1 style="margin-bottom: 5px;">Dashboard Admin</h1>
                <p style="margin: 0; font-size: 0.9em;">Selamat datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
            </div>
            <a href="personil.php" class="btn-logout">Manajemen Personil</a>
            <a href="pengaturan_akun.php" class="btn-detail" style="background-color: #3498db;">Pengaturan Akun</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </div>

        <h2>Daftar Laporan Masuk</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($result) == 0){
                    echo "<tr><td colspan='5' style='text-align:center;'>Belum ada laporan masuk untuk instansi Anda.</td></tr>";
                }
                while($row = mysqli_fetch_assoc($result)) { 
                    $badge_class = 'badge-kuning'; 
                    if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
                    elseif ($row['status'] == 'menunggu verifikasi') { $badge_class = 'badge-oranye'; }
                    elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
                    elseif ($row['status'] == 'ditolak') { $badge_class = 'badge-merah'; }
                ?>
                <tr>
                    <td>#<?= $row['id_laporan'] ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_lapor'])) ?></td>
                    <td><?= $row['nama_kategori'] ?></td>
                    <td><span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span></td>
                    
                    <td>
                        <a href="admin_detail.php?id=<?= $row['id_laporan'] ?>" class="btn-detail">Lihat Detail</a>
                        <?php if ($row['status'] == 'ditolak'): ?>
                            <a href="hapus.php?id=<?= $row['id_laporan'] ?>" onclick="return confirm('Yakin hapus permanen?');" class="btn-hapus">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</body>
</html>