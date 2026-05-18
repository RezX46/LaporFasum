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
    <style>
        .info-grid-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
        }
        .info-grid-detail .full-width { grid-column: 1 / -1; }
        .info-row {
            background: var(--blue-pale);
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .info-row strong {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--blue-dark);
            margin-bottom: 3px;
        }
        .info-row span { font-size: 0.92rem; color: var(--gray-text); }
        .compact-detail-box {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid #dbeafe;
            padding: 16px 18px;
            margin-bottom: 14px;
            box-shadow: var(--shadow-sm);
        }
        .compact-action-box {
            background: var(--white);
            border-radius: var(--radius);
            border: 2px dashed var(--blue-mid);
            padding: 16px 18px;
            margin-bottom: 14px;
            box-shadow: var(--shadow-sm);
        }
        .compact-action-box h2 { margin: 0 0 12px; font-size: 1rem; }
        .compact-action-box textarea { min-height: 70px; }
        .foto-compact { max-width: 100%; height: auto; border-radius: 8px; margin-top: 8px; border: 2px solid #dbeafe; }
        .foto-section { margin-top: 10px; }
        .foto-section strong { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--blue-dark); display: block; margin-bottom: 4px; }
        .catatan-admin-box {
            background: #fff5f5;
            border: 1.5px solid #e74c3c;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 14px;
        }
        .catatan-admin-box h3 { color: #e74c3c; margin: 0 0 6px; font-size: 0.92rem; }
        .catatan-admin-box p { color: #333; font-weight: 600; font-style: italic; margin: 0; font-size: 0.92rem; }
        .page-body-narrow { padding: 24px 40px; }
        @media (max-width: 600px) {
            .info-grid-detail { grid-template-columns: 1fr; }
            .info-grid-detail .full-width { grid-column: 1; }
            .page-body-narrow { padding: 16px; }
        }
    </style>
</head>
<body>

    <nav class="site-navbar">
        <a href="petugas.php" class="brand"><span>Lapor</span>Fasum</a>
        <nav>
            <a href="petugas.php">Dashboard</a>
            <a href="pengaturan_akun.php">Akun</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </nav>
    </nav>

    <div class="page-header" style="padding:20px 40px;">
        <h1 style="font-size:1.4rem;">Tugas #<?= $row['id_laporan'] ?></h1>
        <p>Tinjau detail dan kirim bukti penyelesaian pekerjaan.</p>
    </div>

    <div class="page-body-narrow">

        <!-- STATUS BAR -->
        <div class="status-bar">
            <h2>Status Tugas</h2>
            <span class="badge <?= $badge_class ?>"><?= $teks_status ?></span>
        </div>

        <!-- CATATAN ADMIN -->
        <?php if (!empty($row['pesan_admin'])): ?>
        <div class="catatan-admin-box">
            <h3>Catatan Admin:</h3>
            <p>"<?= $row['pesan_admin'] ?>"</p>
        </div>
        <?php endif; ?>

        <!-- INFO LAPORAN -->
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

            <!-- FOTO GRID -->
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

        <!-- ACTION: KIRIM BUKTI -->
        <?php if ($row['status'] == 'diproses'): ?>
        <div class="compact-action-box">
            <h2 style="color:#27ae60;">Kirim Laporan Selesai</h2>
            <form action="proses_selesai.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                <div class="form-group" style="margin-bottom:10px;">
                    <label style="font-size:0.88rem;">Unggah Foto Bukti Perbaikan (Maks 10 MB):</label>
                    <input type="file" name="foto_bukti" accept="image/*" required>
                </div>
                <button type="submit" class="btn-terima"> Kirim Bukti ke Admin</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- TOMBOL KEMBALI -->
        <div style="margin-top:16px;">
            <button type="button" onclick="history.back()" class="btn-kembali" style="margin-top:0;">&#8592; Kembali</button>
        </div>

    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>