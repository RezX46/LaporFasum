<?php
session_start(); 
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}
require 'koneksi.php';

if (!isset($_GET['id'])) { die("ID Laporan tidak ditemukan."); }

$id_laporan = (int)$_GET['id'];
$id_instansi_admin = $_SESSION['id_instansi'];

// Ambil data laporan digabung dengan tabel kategori untuk mendapatkan id_instansi-nya
$query = "SELECT l.*, k.nama_kategori, k.id_instansi 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE l.id_laporan = $id_laporan";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) { die("Data tidak ditemukan."); }
$row = mysqli_fetch_assoc($result);

// Pastikan Admin tidak mengintip laporan instansi lain dengan mengubah URL ID
if ($row['id_instansi'] != $id_instansi_admin) {
    die("Akses Ditolak! Laporan ini bukan wilayah kewenangan instansi Anda.");
}

$badge_class = 'badge-kuning';
if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
elseif ($row['status'] == 'ditolak') { $badge_class = 'badge-merah'; }

// Logika pemisah peran
if ($id_instansi_admin == 1) {
    // Admin Pusat
    $query_opsi = mysqli_query($koneksi, "
        SELECT k.id_kategori, k.nama_kategori, i.nama_instansi 
        FROM kategori k 
        JOIN instansi i ON k.id_instansi = i.id_instansi 
        WHERE k.id_instansi != 1 
        ORDER BY i.nama_instansi ASC, k.nama_kategori ASC
    ");
} else {
    // Admin instansi
    $query_opsi = mysqli_query($koneksi, "
        SELECT p.id_petugas, u.nama_lengkap 
        FROM petugas p 
        JOIN users u ON p.id_user = u.id_user 
        WHERE p.id_instansi = '$id_instansi_admin'
    ");
}
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
                <?= $row['nama_kategori'] ?>
            </div>
            <div class="detail-item">
                <strong>Deskripsi Keluhan:</strong>
                <?= $row['keluhan'] ?>
            </div>
            <div class="detail-item">
                <strong>Lokasi / Patokan:</strong>
                <?php if(!empty($row['alamat_manual'])) { echo $row['alamat_manual'] . "<br>"; } ?>
                <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map">📍 Buka di Google Maps</a>
            </div>
            <div class="detail-item">
                <strong>Foto Bukti:</strong>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Laporan" class="foto-laporan">
            </div>
        </div>

        <?php if ($row['status'] == 'menunggu'): ?>
        <div class="action-box">
            
            <?php if ($id_instansi_admin == 1): ?>
                <h2 style="margin-top: 0;">Teruskan Laporan</h2>
                <p style="font-size: 0.9em; margin-bottom: 15px;">Pilih kategori yang tepat untuk meneruskan laporan ini ke dinas terkait.</p>
                <form action="proses_validasi.php" method="POST">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <select name="id_kategori_baru" required>
                        <option value="" disabled selected>-- Pilih Instansi & Kategori Tujuan --</option>
                        <?php 
                        $current_instansi = '';
                        while($k = mysqli_fetch_assoc($query_opsi)): 
                            // Jika nama instansinya berbeda dari yang sebelumnya, buat grup baru
                            if ($current_instansi != $k['nama_instansi']) {
                                if ($current_instansi != '') {
                                    echo "</optgroup>"; // Tutup grup sebelumnya
                                }
                                $current_instansi = $k['nama_instansi'];
                                echo "<optgroup label='➡️ Teruskan ke: " . strtoupper($current_instansi) . "'>";
                            }
                        ?>
                            <option value="<?= $k['id_kategori'] ?>">-- Kategori: <?= $k['nama_kategori'] ?></option>
                        <?php endwhile; ?>
                        <?php if ($current_instansi != '') echo "</optgroup>"; // Tutup grup terakhir ?>
                    </select>
                    <button type="submit" name="aksi" value="forward" class="btn-terima">➡️ Teruskan ke Dinas Terkait</button>
                    <button type="submit" name="aksi" value="tolak" class="btn-tolak" style="margin-top: 10px;" formnovalidate>❌ Tolak </button>
                </form>

            <?php else: ?>
                <h2 style="margin-top: 0;">Tindak Lanjut Laporan</h2>
                <p style="font-size: 0.9em; margin-bottom: 15px;">Pilih tim lapangan untuk mengerjakan laporan ini.</p>
                <form action="proses_validasi.php" method="POST">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <select name="id_petugas" required>
                        <option value="" disabled selected>-- Pilih Tim / Petugas Lapangan --</option>
                        <?php while($p = mysqli_fetch_assoc($query_opsi)): ?>
                            <option value="<?= $p['id_petugas'] ?>"><?= $p['nama_lengkap'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="btn-group">
                        <button type="submit" name="aksi" value="terima" class="btn-terima">✔️ Terima & Tugaskan</button>
                        <button type="submit" name="aksi" value="tolak" class="btn-tolak" formnovalidate>❌ Tolak Laporan</button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <a href="admin.php" class="btn-kembali" style="margin-top: 0;">← Kembali ke Dashboard</a>
        </div>

    </div>
</body>
</html>