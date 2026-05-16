<?php
require 'koneksi.php';

$data_laporan = null;
$error = "";

if (isset($_GET['kode'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['kode']);
    
    $query = "SELECT l.*, k.nama_kategori, i.nama_instansi 
              FROM laporan l 
              JOIN kategori k ON l.id_kategori = k.id_kategori 
              JOIN instansi i ON k.id_instansi = i.id_instansi
              WHERE l.kode_lacak = '$kode'";
              
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data_laporan = mysqli_fetch_assoc($result);
        
        $status_asli = $data_laporan['status'];
        if ($status_asli == 'menunggu' || $status_asli == 'diproses' || $status_asli == 'menunggu verifikasi') {
            $tampilan_status = "Sedang Diproses";
            $badge_class = "badge-biru";
        } elseif ($status_asli == 'selesai') {
            $tampilan_status = "Selesai Dikerjakan";
            $badge_class = "badge-hijau";
        } else {
            $tampilan_status = "Laporan Ditolak";
            $badge_class = "badge-merah";
        }
    } else {
        $error = "Kode lacak tidak ditemukan. Pastikan kode yang Anda masukkan benar.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Laporan – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
    <style>
        .info-grid-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 14px;
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
        .status-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .status-bar h2 { margin: 0; font-size: 1rem; color: #0d47a1; }
        .compact-result-box {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid #dbeafe;
            padding: 16px 18px;
            margin-bottom: 14px;
            box-shadow: var(--shadow-sm);
        }
        .page-body-narrow { padding: 24px 40px; }
        @media (max-width: 600px) {
            .info-grid-detail { grid-template-columns: 1fr; }
            .info-grid-detail .full-width { grid-column: 1; }
            .page-body-narrow { padding: 16px; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="site-navbar">
        <a href="index.html" class="brand">&#128205; <span>Lapor</span>Fasum</a>
        <nav>
            <a href="lapor.php">Buat Laporan</a>
            <a href="cek_status.php" class="active">Cek Status</a>
            <a href="login.html">Login Petugas</a>
        </nav>
    </nav>

    <!-- PAGE HEADER -->
    <div class="page-header" style="padding:20px 40px;">
        <h1 style="font-size:1.4rem;">&#128269; Cek Status Laporan</h1>
        <p>Masukkan kode lacak yang Anda terima setelah membuat laporan.</p>
    </div>

    <!-- CONTENT -->
    <div class="page-body-narrow">

        <!-- FORM CEK -->
        <div class="card" style="padding:14px 18px;margin-bottom:14px;">
            <form action="" method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <input type="text" name="kode" placeholder="Masukkan Kode Lacak (Contoh: LP-XXXXXX)" required
                       value="<?= isset($_GET['kode']) ? htmlspecialchars($_GET['kode']) : '' ?>"
                       style="flex:1;min-width:200px;margin-bottom:0;">
                <button type="submit" class="btn" style="width:auto;margin-top:0;padding:11px 24px;">&#128269; Cari</button>
            </form>
        </div>

        <?php if ($error): ?>
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:10px;font-weight:600;margin-bottom:14px;font-size:0.92rem;">
                &#9888; <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($data_laporan): ?>
            <div class="compact-result-box">
                <div class="status-bar">
                    <h2>Laporan #<?= $data_laporan['kode_lacak'] ?></h2>
                    <span class="badge <?= $badge_class ?>"><?= $tampilan_status ?></span>
                </div>
                <hr style="margin:0 0 14px;border:0;border-top:1px solid #dbeafe;">

                <div class="info-grid-detail">
                    <div class="info-row">
                        <strong>Instansi Penanggung Jawab</strong>
                        <span><?= $data_laporan['nama_instansi'] ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Kategori</strong>
                        <span><?= $data_laporan['nama_kategori'] ?></span>
                    </div>
                    <div class="info-row full-width">
                        <strong>Keluhan Anda</strong>
                        <span><?= $data_laporan['keluhan'] ?></span>
                    </div>
                    <div class="info-row full-width">
                        <strong>Lokasi / Alamat</strong>
                        <span>
                            <?= !empty($data_laporan['alamat_manual']) ? $data_laporan['alamat_manual'] : 'Lokasi berdasarkan titik GPS' ?>
                            <?php if(!empty($data_laporan['latitude']) && !empty($data_laporan['longitude'])): ?>
                                <a href="https://www.google.com/maps?q=<?= $data_laporan['latitude'] ?>,<?= $data_laporan['longitude'] ?>" target="_blank" class="btn-map" style="margin-top:5px;padding:4px 10px;font-size:0.8rem;display:inline-block;"> Buka di Google Maps</a>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- FOTO GRID — posisi tetap sama -->
                <div class="foto-grid">
                    <div class="foto-box">
                        <span>Foto Laporan</span>
                        <img src="uploads/<?= $data_laporan['foto'] ?>" class="foto-laporan">
                    </div>
                    <?php if ($status_asli == 'selesai' && !empty($data_laporan['foto_bukti'])): ?>
                    <div class="foto-box" style="border-color:#2ecc71;">
                        <span style="color:#16a34a;">Hasil Perbaikan</span>
                        <img src="uploads/<?= $data_laporan['foto_bukti'] ?>" class="foto-laporan">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="index.html" class="back-link">← Kembali ke Halaman Utama</a>
    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>