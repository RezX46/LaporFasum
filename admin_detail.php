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
require 'koneksi.php';

// Mengecek apakah ada ID yang dikirim melalui URL
if (!isset($_GET['id'])) {
    die("ID Laporan tidak ditemukan di URL.");
}

// Menangkap ID dan mencari datanya di database
$id_laporan = $_GET['id'];
$query = "SELECT * FROM laporan WHERE id_laporan = $id_laporan";
$result = mysqli_query($koneksi, $query);

// Mengecek apakah data dengan ID tersebut benar-benar ada
if (mysqli_num_rows($result) == 0) {
    die("Data laporan tidak ditemukan di database.");
}

// Mengambil data dari database untuk dimasukkan ke variabel $row
$row = mysqli_fetch_assoc($result);

// Menentukan warna label (badge) berdasarkan status
$badge_class = 'badge-kuning';
if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
elseif ($row['status'] == 'ditolak') { $badge_class = 'badge-merah'; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .admin-container { max-width: 800px; }
        .header-admin { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .badge { padding: 5px 10px; border-radius: 20px; color: white; font-size: 0.85em; font-weight: bold; display: inline-block; margin-bottom: 15px; }
        .badge-kuning { background-color: #f1c40f; color: #333;}
        .badge-biru { background-color: #3498db; }
        .badge-hijau { background-color: #2ecc71; }
        .badge-merah { background-color: #e74c3c; }
        
        .detail-box { background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; text-align: left; margin-bottom: 25px; }
        .detail-item { margin-bottom: 15px; }
        .detail-item strong { display: block; color: #2c3e50; margin-bottom: 5px; }
        .foto-laporan { max-width: 100%; height: auto; border-radius: 5px; margin-top: 10px; border: 1px solid #ccc; }
        
        .btn-map { background-color: #e67e22; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.85em; display: inline-block; margin-top: 8px; font-weight: bold; }
        .btn-map:hover { background-color: #d35400; }

        .action-box { background-color: #fff; padding: 20px; border-radius: 8px; border: 2px dashed #3498db; text-align: left; }
        .btn-group { display: flex; gap: 15px; margin-top: 15px; }
        .btn-terima { background-color: #2ecc71; color: white; width: 100%; border: none; cursor: pointer; padding: 12px; border-radius: 5px; font-weight: bold;}
        .btn-terima:hover { background-color: #27ae60; }
        .btn-tolak { background-color: #e74c3c; color: white; width: 100%; border: none; cursor: pointer; padding: 12px; border-radius: 5px; font-weight: bold;}
        .btn-tolak:hover { background-color: #c0392b; }
        .btn-kembali { background-color: #95a5a6; color: white; margin-top: 20px; display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px;}
        select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; margin-top: 10px; margin-bottom: 15px; font-family: inherit; }
    </style>
</head>
<body>

    <div class="container admin-container">
        
        <div class="header-admin">
            <h1 style="margin-bottom: 0;">Detail Laporan #<?= $row['id_laporan'] ?></h1>
            <span class="badge <?= $badge_class ?>">Status: <?= ucfirst($row['status']) ?></span>
        </div>

        <div class="detail-box">
            <div class="detail-item">
                <strong>Tanggal Masuk:</strong>
                <?= date('d M Y, H:i', strtotime($row['tanggal_lapor'])) ?> WITA
            </div>
            <div class="detail-item">
                <strong>Kategori Fasilitas:</strong>
                <?= $row['kategori'] ?>
            </div>
            <div class="detail-item">
                <strong>Deskripsi Keluhan:</strong>
                <?= $row['keluhan'] ?>
            </div>
            <div class="detail-item">
                <strong>Lokasi / Alamat:</strong>
                <?php if ($row['metode_lokasi'] == 'manual'): ?>
                    <?= $row['alamat_manual'] ?> (Ketik Manual)
                <?php else: ?>
                    Lokasi Peta <br>
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map"> Lihat lokasi</a>
                <?php endif; ?>
            </div>
            <div class="detail-item">
                <strong>Foto Bukti:</strong>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Laporan" class="foto-laporan">
            </div>
        </div>

        <?php if ($row['status'] == 'menunggu'): ?>
        <div class="action-box">
            <h2 style="margin-top: 0;">Tindak Lanjut Admin</h2>
            <p style="font-size: 0.9em; margin-bottom: 15px;">Silakan validasi laporan ini.</p>
            
            <form action="proses_validasi.php" method="POST">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">

                <label for="petugas"><strong>Tugaskan Kepada:</strong></label>
                <select id="petugas" name="petugas" required>
                    <option value="" disabled selected>-- Pilih Tim / Petugas Lapangan --</option>
                    <option value="1">Tim Reaksi Cepat A (Jalan & Jembatan)</option>
                    <option value="2">Tim Reaksi Cepat B (Penerangan)</option>
                    <option value="3">Tim C (Drainase & Kebersihan)</option>
                    //nanti tambah (atau hilangin aja)
                </select>

                <div class="btn-group">
                    <button type="submit" name="aksi" value="terima" class="btn-terima">✔️ Terima & Tugaskan</button>
                    <button type="submit" name="aksi" value="tolak" class="btn-tolak" formnovalidate>❌ Tolak Laporan</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <a href="admin.php" class="btn-kembali">← Kembali ke Dashboard</a>
    </div>

</body>
</html>