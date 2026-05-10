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

$query = "SELECT l.*, k.nama_kategori, k.id_instansi, u.nama_lengkap as nama_petugas 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          LEFT JOIN petugas p ON l.id_petugas = p.id_petugas
          LEFT JOIN users u ON p.id_user = u.id_user
          WHERE l.id_laporan = $id_laporan";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) { die("Data tidak ditemukan."); }
$row = mysqli_fetch_assoc($result);

if ($row['id_instansi'] != $id_instansi_admin) {
    die("Akses Ditolak! Laporan ini bukan wilayah kewenangan instansi Anda.");
}

if ($id_instansi_admin == 1) {
    // Jika Admin Pusat, ambil kategori beserta grup instansinya
    $query_opsi = mysqli_query($koneksi, "
        SELECT k.id_kategori, k.nama_kategori, i.nama_instansi 
        FROM kategori k 
        JOIN instansi i ON k.id_instansi = i.id_instansi 
        WHERE k.id_instansi != 1 
        ORDER BY i.nama_instansi ASC, k.nama_kategori ASC
    ");
} else {
    // Admin lain ambil daftar petugas lapangannya
    $query_opsi = mysqli_query($koneksi, "
        SELECT p.id_petugas, u.nama_lengkap 
        FROM petugas p 
        JOIN users u ON p.id_user = u.id_user 
        WHERE p.id_instansi = '$id_instansi_admin'
    ");
}
// Simpan hasil query ke array agar bisa digunakan lebih dari satu kali di halaman ini
$opsi_data = [];
while($opt = mysqli_fetch_assoc($query_opsi)) {
    $opsi_data[] = $opt;
}

$badge_class = 'badge-kuning';
if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
elseif ($row['status'] == 'menunggu verifikasi') { $badge_class = 'badge-oranye'; }
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
                <?php if($row['latitude'] != NULL && $row['longitude'] != NULL): ?>
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map">📍 Buka di Google Maps</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($row['nama_petugas'])): ?>
            <div class="detail-item">
                <strong>Ditugaskan Kepada:</strong>
                <span style="background-color: #3498db; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em;"> <?= $row['nama_petugas'] ?></span>
            </div>
            <?php endif; ?>

            <div class="detail-item">
                <strong>Foto Laporan:</strong>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Laporan Warga" class="foto-laporan">
            </div>

            <?php if ($row['status'] == 'selesai' && !empty($row['foto_bukti'])): ?>
            <div class="detail-item" style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #ccc;">
                <strong style="color: #27ae60;">Foto Bukti:</strong>
                <img src="uploads/<?= $row['foto_bukti'] ?>" alt="Foto Bukti Perbaikan" class="foto-laporan" style="border: 2px solid #2ecc71;">
            </div>
            <?php endif; ?>
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
                        foreach($opsi_data as $k): 
                            if ($current_instansi != $k['nama_instansi']) {
                                if ($current_instansi != '') { echo "</optgroup>"; }
                                $current_instansi = $k['nama_instansi'];
                                echo "<optgroup label='➡️ Teruskan ke: " . strtoupper($current_instansi) . "'>";
                            }
                        ?>
                            <option value="<?= $k['id_kategori'] ?>">-- Kategori: <?= $k['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                        <?php if ($current_instansi != '') echo "</optgroup>"; ?>
                    </select>
                    <button type="submit" name="aksi" value="forward" class="btn-terima">➡️ Teruskan ke Dinas Terkait</button>
                    <button type="submit" name="aksi" value="tolak" class="btn-tolak" style="margin-top: 10px;" formnovalidate>❌ Tolak (Spam)</button>
                </form>
            <?php else: ?>
                <h2 style="margin-top: 0;">Tindak Lanjut Laporan</h2>
                <p style="font-size: 0.9em; margin-bottom: 15px;">Pilih tim lapangan untuk mengerjakan laporan ini.</p>
                <form action="proses_validasi.php" method="POST">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <select name="id_petugas" required>
                        <option value="" disabled selected>-- Pilih Tim / Petugas Lapangan --</option>
                        <?php foreach($opsi_data as $p): ?>
                            <option value="<?= $p['id_petugas'] ?>"><?= $p['nama_lengkap'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="btn-group">
                        <button type="submit" name="aksi" value="terima" class="btn-terima">✔️ Terima & Tugaskan</button>
                        <button type="submit" name="aksi" value="tolak" class="btn-tolak" formnovalidate>❌ Tolak Laporan</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($row['status'] == 'diproses' && $id_instansi_admin != 1): ?>
        <div class="action-box" style="border-color: #3498db;">
            <h2 style="margin-top: 0; color: #2980b9;">Ganti Penugasan</h2>
            <p style="font-size: 0.9em; margin-bottom: 10px;">Gunakan formulir ini jika ingin mengalihkan laporan ke petugas lain sebelum pekerjaan selesai.</p>
            <form action="proses_validasi.php" method="POST">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                <select name="id_petugas" required>
                    <option value="" disabled selected>-- Pilih Petugas Baru --</option>
                    <?php foreach($opsi_data as $p): ?>
                        <option value="<?= $p['id_petugas'] ?>" <?= ($p['id_petugas'] == $row['id_petugas']) ? 'disabled' : '' ?>>
                            <?= $p['nama_lengkap'] ?> <?= ($p['id_petugas'] == $row['id_petugas']) ? '(Petugas Saat Ini)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="aksi" value="update_petugas" class="btn-update">🔄 Simpan Perubahan Petugas</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($row['status'] == 'menunggu verifikasi'): ?>
        <div class="action-box" style="border-color: #e67e22;">
            <h2 style="margin-top: 0; color: #e67e22;">Verifikasi Pekerjaan Petugas</h2>
            <p>Petugas <strong><?= $row['nama_petugas'] ?></strong> telah mengirimkan bukti perbaikan. Silakan periksa foto di bawah ini:</p>
            
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="uploads/<?= $row['foto_bukti'] ?>" class="foto-laporan" style="border: 3px solid #2ecc71;">
            </div>

            <form action="proses_validasi.php" method="POST">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                
                <div class="btn-group">
                    <button type="submit" name="aksi" value="verifikasi_terima" class="btn-terima">✔️ Verifikasi </button>
                </div>

                <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                    <label style="font-weight: bold; color: #e74c3c;">Opsi Tolak Bukti:</label>
                    
                    <select name="id_petugas_baru" id="select_petugas_baru" onchange="updateTombolTolak()">
                        <option value="">-- Tetap di Petugas Lama (<?= $row['nama_petugas'] ?>) --</option>
                        
                        <?php foreach($opsi_data as $p): ?>
                            <?php if($p['id_petugas'] == $row['id_petugas']): ?>
                                <option value="" disabled><?= $p['nama_lengkap'] ?> (Petugas Saat Ini)</option>
                            <?php else: ?>
                                <option value="<?= $p['id_petugas'] ?>"><?= $p['nama_lengkap'] ?> (Alihkan Tugas)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                    </select>
                    
                    <button type="submit" name="aksi" value="verifikasi_tolak" id="btn_tolak" class="btn-tolak">❌ Tolak Bukti & Kembalikan</button>
                </div>
            </form>
        </div>

        <script>
        function updateTombolTolak() {
            var select = document.getElementById('select_petugas_baru');
            var btn = document.getElementById('btn_tolak');
            
            if (select.value === "") {
                // Jika pilihannya kosong (Kembali ke petugas lama)
                btn.innerHTML = "❌ Tolak Bukti & Kembalikan";
            } else {
                // Jika memilih petugas baru, ambil namanya dan pasang di tombol
                var nama = select.options[select.selectedIndex].text.split(' (')[0];
                btn.innerHTML = "❌ Tolak & Alihkan Tugas ke " + nama;
            }
        }
        </script>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <a href="admin.php" class="btn-kembali" style="margin-top: 0;">← Kembali ke Dashboard</a>
        </div>

    </div>
</body>
</html>