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
    <title>Dashboard Admin – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="site-navbar">
        <a href="admin.php" class="brand">&#128205; <span>Lapor</span>Fasum</a>
        <nav>
            <a href="personil.php">&#128101; Manajemen Personil</a>
            <a href="pengaturan_akun.php">&#9881; Pengaturan Akun</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </nav>
    </nav>

    <div class="page-header">
        <h1>&#128203; Dashboard Admin</h1>
        <p>Selamat datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
    </div>

    <div class="page-body" style="max-width:1100px;">
        <div class="card">
            <div class="card-title">Daftar Laporan Masuk</div>
            <div style="overflow-x:auto;">
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
                        echo "<tr><td colspan='5' style='text-align:center;padding:30px;color:#78909c;'>Belum ada laporan masuk untuk instansi Anda.</td></tr>";
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
        </div>
    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>