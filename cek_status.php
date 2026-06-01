<?php
require 'koneksi.php';
// FUNGSI AUTO-LINK UNTUK KODE LACAK (Regex format: LP-YYYYMM-XXXX)
function buatLinkKodeLacak($teks) {
    $pola = '/(LP-\d{6}-[A-Z0-9]{4})/i';
    $ganti = '<a href="cek_status.php?kode=$1" style="color:#2980b9; font-weight:bold; text-decoration:underline; background:#e8f4f8; padding:2px 6px; border-radius:4px;">$1</a>';
    return preg_replace($pola, $ganti, $teks);
}

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
</head>
<body>

    <nav class="site-navbar">
        <a href="index.html" class="brand"><span class="brand-lapor">Lapor</span><span class="brand-fasum">Fasum</span></a>
        <nav>
            <a href="lapor.php">Buat Laporan</a>
            <a href="cek_status.php" class="active">Cek Status</a>
            <a href="login.html">Login Petugas</a>
        </nav>
    </nav>

    <div class="page-header" style="padding:20px 40px;">
        <h1 style="font-size:1.4rem;">Cek Status Laporan</h1>
        <p>Masukkan kode lacak yang Anda terima setelah membuat laporan.</p>
    </div>

    <div class="page-body-narrow">

        <div class="card" style="padding:14px 18px;margin-bottom:14px;">
            <form action="" method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <input type="text" name="kode" placeholder="Masukkan Kode Lacak (Contoh: LP-XXXXXX-XXXX)" required
                       value="<?= isset($_GET['kode']) ? htmlspecialchars($_GET['kode']) : '' ?>"
                       style="flex:1;min-width:200px;margin-bottom:0;">
                <button type="submit" class="btn" style="width:auto;margin-top:0;padding:11px 24px;">Cari</button>
            </form>
        </div>

        <?php if ($error): ?>
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:10px;font-weight:600;margin-bottom:14px;font-size:0.92rem;">
                <?= $error ?>
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

                <?php if ($status_asli == 'ditolak'): ?>
                    <?php 
                        $id_laporan_ini = $data_laporan['id_laporan'];
                        $q_tolak = mysqli_query($koneksi, "SELECT keterangan FROM riwayat_laporan WHERE id_laporan = '$id_laporan_ini' AND aksi = 'tolak' ORDER BY tanggal_aksi DESC LIMIT 1");
                        
                        if ($q_tolak && mysqli_num_rows($q_tolak) > 0) {
                            $data_tolak = mysqli_fetch_assoc($q_tolak);
                            // Terapkan fungsi Auto-Link pada teks keterangan
                            $alasan_terformat = buatLinkKodeLacak(htmlspecialchars($data_tolak['keterangan']));
                        } else {
                            $alasan_terformat = "Laporan tidak memenuhi kriteria pelaporan fasilitas umum.";
                        }
                    ?>
                    <div style="background-color: #fee2e2; border-left: 5px solid #ef4444; padding: 15px; border-radius: 6px; margin-top: 15px;">
                        <strong style="color: #b91c1c; font-size: 0.9rem; display: block; margin-bottom: 5px;">Alasan Penolakan:</strong>
                        <span style="color: #7f1d1d; line-height: 1.5;">
                            <?= $alasan_terformat ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($status_asli == 'selesai'): ?>
                    <?php 
                        $id_laporan_ini = $data_laporan['id_laporan'];
                        $q_selesai = mysqli_query($koneksi, "SELECT keterangan FROM riwayat_laporan WHERE id_laporan = '$id_laporan_ini' AND aksi = 'verifikasi_terima' ORDER BY tanggal_aksi DESC LIMIT 1");
                        
                        if ($q_selesai && mysqli_num_rows($q_selesai) > 0) {
                            $data_selesai = mysqli_fetch_assoc($q_selesai);
                            $keterangan_admin = $data_selesai['keterangan'];                         
                            // Hanya tampilkan kotak pesan JIKA admin mengisi keterangannya 
                            if (!empty(trim($keterangan_admin)) && $keterangan_admin !== 'Bukti perbaikan disetujui. Tugas selesai.') {
                                ?>
                                <div style="background-color: #f0fdf4; border-left: 5px solid #22c55e; padding: 15px; border-radius: 6px; margin-top: 15px;">
                                    <strong style="color: #16a34a; font-size: 0.9rem; display: block; margin-bottom: 5px;">Pesan dari Admin:</strong>
                                    <span style="color: #15803d; line-height: 1.5; font-style: italic;">
                                        "<?= htmlspecialchars($keterangan_admin) ?>"
                                    </span>
                                </div>
                                <?php
                            }
                        }
                    ?>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        <a href="index.html" class="back-link">← Kembali ke Halaman Utama</a>
    </div>

    <footer class="site-footer">© 2025 <span>LaporFasum</span> — Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>