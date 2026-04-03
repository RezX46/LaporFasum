<?php
session_start();

// 1. Keamanan: Pastikan yang masuk adalah petugas
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>
            alert('Akses Ditolak! Anda harus login sebagai Petugas.');
            window.location.href = 'login.html';
          </script>";
    exit();
}

require 'koneksi.php';

// Ambil ID petugas yang sedang login dari session
$id_petugas = $_SESSION['id_user'];

// 2. Ambil data tugas yang sedang "diproses" KHUSUS untuk petugas ini
$query_aktif = "SELECT * FROM laporan WHERE id_petugas = '$id_petugas' AND status = 'diproses' ORDER BY id_laporan DESC";
$result_aktif = mysqli_query($koneksi, $query_aktif);

// 3. Ambil data tugas yang sudah "selesai" KHUSUS untuk petugas ini
$query_selesai = "SELECT * FROM laporan WHERE id_petugas = '$id_petugas' AND status = 'selesai' ORDER BY id_laporan DESC";
$result_selesai = mysqli_query($koneksi, $query_selesai);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .petugas-container { max-width: 600px; }
        .header-petugas { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .btn-logout { background-color: #e74c3c; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 0.9em; }
        .section-title { text-align: left; margin-bottom: 15px; color: #34495e; border-left: 4px solid #3498db; padding-left: 10px; }
        .task-card { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: left; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .task-header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .badge-kuning { background-color: #f1c40f; color: #333; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold;}
        .badge-hijau { background-color: #2ecc71; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold;}
        .proof-section { background-color: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px dashed #3498db; margin-top: 15px; }
        .btn-selesai { background-color: #2ecc71; width: 100%; margin-top: 15px; border: none; padding: 10px; color: white; font-weight: bold; border-radius: 5px; cursor: pointer;}
        .btn-selesai:hover { background-color: #27ae60; }
        .btn-detail { background-color: #3498db; width: 100%; padding: 8px; font-size: 0.9em; margin-top: 10px; color: white; border: none; border-radius: 5px; cursor: pointer;}
        .btn-map { background-color: #e67e22; color: white; padding: 4px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8em; display: inline-block; margin-bottom: 10px; font-weight: bold; }
        .btn-map:hover { background-color: #d35400; }
    </style>
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
                    <strong>Laporan #<?= $row['id_laporan'] ?></strong>
                    <span class="badge-kuning">Perlu Tindakan</span>
                </div>
                
                <p style="margin-bottom: 5px;"><strong>Lokasi:</strong> 
                    <?php if ($row['metode_lokasi'] == 'manual'): ?>
                        <?= $row['alamat_manual'] ?>
                    <?php else: ?>
                        Titik Koordinat Peta
                    <?php endif; ?>
                </p>
                
                <?php if ($row['metode_lokasi'] == 'peta'): ?>
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map">🗺️ Buka Rute di Google Maps</a>
                <?php endif; ?>
                
                <p style="margin-bottom: 10px; font-size: 0.9em; color: #666;"><strong>Keluhan:</strong> <?= $row['keluhan'] ?></p>
                <p style="margin-bottom: 5px; font-size: 0.9em;"><strong>Foto Kondisi Kerusakan:</strong></p>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Kondisi Awal" style="max-width: 100%; height: auto; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 15px;">
                <form class="proof-section" action="proses_selesai.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <p style="font-weight: bold; margin-bottom: 10px; font-size: 0.9em;">Tindakan Penyelesaian:</p>
                    
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="font-size: 0.85em;">Unggah Foto Hasil Perbaikan:</label>
                        <input type="file" name="foto_bukti" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn-selesai">✔️ Tandai Selesai</button>
                </form>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: left; color: #7f8c8d; font-style: italic;">Hore! Tidak ada tugas baru saat ini.</p>
        <?php endif; ?>

        <h2 class="section-title" style="margin-top: 40px;">Riwayat Tugas Selesai</h2>

        <?php if(mysqli_num_rows($result_selesai) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result_selesai)): ?>
            <div class="task-card" style="opacity: 0.8;">
                <div class="task-header">
                    <strong>Laporan #<?= $row['id_laporan'] ?></strong>
                    <span class="badge-hijau">Selesai</span>
                </div>
                <p style="margin-bottom: 5px;"><strong>Kategori:</strong> <?= $row['kategori'] ?></p>
                <button class="btn-detail" onclick="alert('Tugas ini sudah selesai dilaporkan dengan bukti: <?= $row['foto_bukti'] ?>')">Lihat Bukti Tersimpan</button>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: left; color: #7f8c8d; font-style: italic;">Belum ada riwayat tugas yang diselesaikan.</p>
        <?php endif; ?>

    </div>

</body>
</html>