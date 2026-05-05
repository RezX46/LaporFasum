<?php
session_start(); // Mulai pengecekan kartu pengenal
// Jika tidak ada kartu pengenal, atau jabatannya bukan admin, tendang keluar!
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>
            alert('Akses Ditolak! Anda harus login sebagai Admin.');
            window.location.href = 'login.html';
          </script>";
    exit(); // Hentikan eksekusi kode di bawahnya
}
// 1. Hubungkan ke database
require 'koneksi.php';

// 2. Ambil semua data dari tabel laporan, urutkan dari yang terbaru (DESC)
$query = "SELECT * FROM laporan ORDER BY id_laporan DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .admin-container { max-width: 1000px; margin-top: 20px; }
        .header-admin { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .btn-logout { background-color: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 0.9em; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; }
        tr:hover { background-color: #f1f1f1; }
        
        /* Desain Label Status */
        .badge { padding: 5px 10px; border-radius: 20px; color: white; font-size: 0.85em; font-weight: bold; }
        .badge-kuning { background-color: #f1c40f; color: #333; }
        .badge-biru { background-color: #3498db; }
        .badge-hijau { background-color: #2ecc71; }
        .badge-merah { background-color: #e74c3c; }

        .btn-detail { background-color: #2c3e50; padding: 6px 12px; font-size: 0.85em; margin-top: 0; width: auto; color: white; text-decoration: none; border-radius: 5px;}
        .btn-detail:hover { background-color: #1a252f; }
        .btn-hapus { background-color: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85em; font-weight: bold; margin-left: 5px;}
        .btn-hapus:hover { background-color: #c0392b; }
    </style>
</head>
<body>

    <div class="container admin-container">
        
        <div class="header-admin">
            <div>
                <h1 style="margin-bottom: 5px;">Dashboard Admin</h1>
                <p style="margin: 0; font-size: 0.9em;">Selamat datang, <strong>Atmin</strong></p>
            </div>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </div>

        <h2>Daftar Laporan Masuk</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Lokasi (Singkat)</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // 3. Looping (Perulangan) untuk menampilkan data satu per satu
                while($row = mysqli_fetch_assoc($result)) { 
                    
                    // Menentukan warna label (badge) berdasarkan status dari database
                    $badge_class = 'badge-kuning'; // Default
                    if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
                    elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
                    elseif ($row['status'] == 'ditolak') { $badge_class = 'badge-merah'; }

                    // Menentukan format tampilan lokasi
                    $tampil_lokasi = ($row['metode_lokasi'] == 'manual') ? $row['alamat_manual'] : '📍 Titik Koordinat Peta';
                ?>
                <tr>
                    <td>#<?= $row['id_laporan'] ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_lapor'])) ?></td>
                    <td><?= $row['kategori'] ?></td>
                    <td><?= $tampil_lokasi ?></td>
                    <td><span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span></td>
                    
                    <td><a href="admin_detail.php?id=<?= $row['id_laporan'] ?>" class="btn-detail">Lihat Detail</a></td>
                    <a href="hapus.php?id=<?= $row['id_laporan'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan ini secara permanen?');" class="btn-hapus"> Hapus</a>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</body>
</html>