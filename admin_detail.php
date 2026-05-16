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

$query = "SELECT l.*, k.nama_kategori, k.id_instansi, u_petugas.nama_lengkap as nama_petugas 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          LEFT JOIN users u_petugas ON l.id_petugas = u_petugas.id_user
          WHERE l.id_laporan = $id_laporan";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) { die("Data tidak ditemukan."); }
$row = mysqli_fetch_assoc($result);

if ($row['id_instansi'] != $id_instansi_admin) {
    die("Akses Ditolak! Laporan ini bukan wilayah kewenangan instansi Anda.");
}

if ($id_instansi_admin == 1) {
    $query_opsi = mysqli_query($koneksi, "
        SELECT k.id_kategori, k.nama_kategori, i.nama_instansi 
        FROM kategori k 
        JOIN instansi i ON k.id_instansi = i.id_instansi 
        WHERE k.id_instansi != 1 
        ORDER BY i.nama_instansi ASC, k.nama_kategori ASC
    ");
} else {
    $query_opsi = mysqli_query($koneksi, "
        SELECT id_user as id_petugas, nama_lengkap 
        FROM users 
        WHERE role = 'petugas' AND id_instansi = '$id_instansi_admin'
    ");
}

$opsi_data = [];
while($opt = mysqli_fetch_assoc($query_opsi)) {
    $opsi_data[] = $opt;
}

$query_riwayat = "SELECT r.*, u.nama_lengkap 
                  FROM riwayat_laporan r 
                  JOIN users u ON r.id_user = u.id_user 
                  WHERE r.id_laporan = $id_laporan 
                  ORDER BY r.tanggal_aksi DESC";
$riwayat = mysqli_query($koneksi, $query_riwayat);

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
    <title>Detail Laporan – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

    <nav class="site-navbar">
        <a href="admin.php" class="brand">&#128205; <span>Lapor</span>Fasum</a>
        <nav>
            <a href="admin.php">&#128203; Dashboard</a>
            <a href="personil.php">&#128101; Personil</a>
            <a href="pengaturan_akun.php">&#9881; Akun</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </nav>
    </nav>

    <div class="page-header">
        <h1>&#128203; Detail Laporan #<?= $row['id_laporan'] ?></h1>
        <p>Tinjau dan ambil tindakan atas laporan ini.</p>
    </div>

    <div class="page-body-narrow">

        <div class="detail-box" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <h2 style="margin:0;color:#0d47a1;">Status Laporan</h2>
            <span class="badge <?= $badge_class ?>">Status: <?= ucfirst($row['status']) ?></span>
        </div>

        <div class="detail-box">
            <div class="detail-item"><strong>Tanggal Masuk:</strong> <?= date('d M Y, H:i', strtotime($row['tanggal_lapor'])) ?> WITA</div>
            <div class="detail-item"><strong>Kategori Fasilitas:</strong> <?= $row['nama_kategori'] ?></div>
            <div class="detail-item"><strong>Deskripsi Keluhan:</strong> <?= $row['keluhan'] ?></div>
            <div class="detail-item">
                <strong>Lokasi / Patokan:</strong>
                <?php if(!empty($row['alamat_manual'])) { echo $row['alamat_manual'] . "<br>"; } ?>
                <?php if(!empty($row['latitude']) && !empty($row['longitude'])): ?>
                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map"> Buka di Google Maps</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($row['nama_petugas'])): ?>
            <div class="detail-item">
                <strong>Ditugaskan Kepada:</strong>
                <span style="background-color: #3498db; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.9em;"> <?= $row['nama_petugas'] ?></span>
            </div>
            <?php endif; ?>

            <div class="detail-item">
                <strong>Foto Laporan:</strong><br>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Laporan Warga" class="foto-laporan">
            </div>

            <?php if ($row['status'] == 'selesai' && !empty($row['foto_bukti'])): ?>
            <div class="detail-item" style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #ccc;">
                <strong style="color: #27ae60;">Foto Bukti:</strong><br>
                <img src="uploads/<?= $row['foto_bukti'] ?>" alt="Foto Bukti Perbaikan" class="foto-laporan" style="border: 2px solid #2ecc71;">
            </div>
            <?php endif; ?>
        </div>

        <?php if ($row['status'] == 'menunggu'): ?>
        <div class="action-box">
            <?php if ($id_instansi_admin == 1): ?>
                <h2 style="margin-top: 0;">Teruskan Laporan</h2>
                <form action="proses_validasi.php" method="POST">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <select name="id_kategori_baru" required>
                        <option value="" disabled selected>-- Pilih Instansi Tujuan --</option>
                        <?php 
                        $current_instansi = '';
                        foreach($opsi_data as $k): 
                            if ($current_instansi != $k['nama_instansi']) {
                                if ($current_instansi != '') { echo "</optgroup>"; }
                                $current_instansi = $k['nama_instansi'];
                                echo "<optgroup label=' Teruskan ke: " . strtoupper($current_instansi) . "'>";
                            }
                        ?>
                            <option value="<?= $k['id_kategori'] ?>">-- Kategori: <?= $k['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="aksi" value="forward" class="btn-terima"> Teruskan ke Dinas Terkait</button>
                </form>
                
                <form action="proses_validasi.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <button type="submit" name="aksi" value="tolak" class="btn-tolak"> Tolak </button>
                </form>
            <?php else: ?>
                <h2 style="margin-top: 0;">Tindak Lanjut Laporan</h2>
                <form action="proses_validasi.php" method="POST">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <select name="id_petugas" required>
                        <option value="" disabled selected>-- Pilih Tim / Petugas Lapangan --</option>
                        <?php foreach($opsi_data as $p): ?>
                            <option value="<?= $p['id_petugas'] ?>"><?= $p['nama_lengkap'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="aksi" value="terima" class="btn-terima"> Terima & Tugaskan</button>
                </form>

                <form action="proses_validasi.php" method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <button type="submit" name="aksi" value="tolak" class="btn-tolak"> Tolak Laporan</button>
                </form>
                
                <form action="proses_validasi.php" method="POST" style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <label style="font-weight: bold; color: #e74c3c;">Alasan Pengembalian Ke Pusat:</label>
                    <textarea name="keterangan" placeholder="Alasan dikembalikan..." style="margin-top: 10px;" required></textarea>
                    <button type="submit" name="aksi" value="kembalikan" class="btn-tolak"> Kembalikan ke Pusat</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($row['status'] == 'diproses' && $id_instansi_admin != 1): ?>
        <div class="action-box" style="border-color: #3498db;">
            <h2 style="margin-top: 0; color: #2980b9;">Ganti Penugasan</h2>
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
                <label style="font-weight: bold; margin-top: 10px; display: block;">Alasan Pergantian:</label>
                <textarea name="keterangan" placeholder="Alasan mengganti petugas..." required></textarea>
                <button type="submit" name="aksi" value="update_petugas" class="btn-update"> Simpan Perubahan Petugas</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($row['status'] == 'menunggu verifikasi'): ?>
        <div class="action-box" style="border-color: #e67e22;">
            <h2 style="margin-top: 0; color: #e67e22;">Verifikasi Pekerjaan Petugas</h2>
            <p>Petugas <strong><?= $row['nama_petugas'] ?></strong> telah mengirimkan bukti perbaikan.</p>
            
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="uploads/<?= $row['foto_bukti'] ?>" class="foto-laporan" style="border: 3px solid #2ecc71;">
            </div>

            <form action="proses_validasi.php" method="POST">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                <button type="submit" name="aksi" value="verifikasi_terima" class="btn-terima"> Verifikasi </button>
            </form>

            <form action="proses_validasi.php" method="POST" style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                <label style="font-weight: bold; color: #e74c3c;">Opsi Tolak Bukti:</label>
                
                <select name="id_petugas_baru" id="select_petugas_baru" onchange="updateTombolTolak()">
                    <option value="">-- Tetap di Petugas Lama (<?= $row['nama_petugas'] ?>) --</option>
                    <?php foreach($opsi_data as $p): ?>
                        <?php if($p['id_petugas'] == $row['id_petugas']): ?>
                            <option value="" disabled><?= $p['nama_lengkap'] ?> (Petugas Saat Ini)</option>
                        <?php else: ?>
                            <option value="<?= $p['id_petugas'] ?>"><?= $p['nama_lengkap'] ?> (Alihkan)</option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>

                <label style="font-weight: bold; margin-top: 10px; display: block;">Alasan Penolakan:</label>
                <textarea name="keterangan" placeholder="Berikan alasan mengapa bukti ditolak..." required></textarea>
                
                <button type="submit" name="aksi" value="verifikasi_tolak" id="btn_tolak" class="btn-tolak"> Tolak Bukti & Kembalikan</button>
            </form>
        </div>

        <script>
        function updateTombolTolak() {
            var select = document.getElementById('select_petugas_baru');
            var btn = document.getElementById('btn_tolak');
            
            if (select.value === "") {
                btn.innerHTML = "Tolak Bukti & Kembalikan";
            } else {
                var nama = select.options[select.selectedIndex].text.split(' (')[0];
                btn.innerHTML = " Tolak & Alihkan Tugas ke " + nama;
            }
        }
        </script>
        <?php endif; ?>

        <h2 class="section-title" style="margin-top: 40px;">Log Riwayat Laporan</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($riwayat) == 0): ?>
                        <tr><td colspan="4" style="text-align:center; font-style:italic;">Belum ada riwayat aktivitas.</td></tr>
                    <?php endif; ?>
                    <?php while($r = mysqli_fetch_assoc($riwayat)): ?>
                    <tr>
                        <td style="font-size: 0.85em;"><?= date('d/m H:i', strtotime($r['tanggal_aksi'])) ?></td>
                        <td><strong><?= $r['nama_lengkap'] ?></strong></td>
                        <td><?= ucfirst(str_replace('_', ' ', $r['aksi'])) ?></td>
                        <td style="color: #666; font-size: 0.9em; font-style: italic;"><?= $r['keterangan'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            <a href="admin.php" class="btn-kembali" style="margin-top:0;">← Kembali ke Dashboard</a>
        </div>

    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>