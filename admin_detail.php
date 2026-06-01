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
        <a href="admin.php" class="brand"><span class="brand-lapor">Lapor</span><span class="brand-fasum">Fasum</span></a>
        <nav>
            <a href="admin.php">Dashboard</a>
            <a href="personil.php">Personil</a>
            <a href="pengaturan_akun.php">Akun</a>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">Keluar</a>
        </nav>
    </nav>

    <div class="page-header" style="padding:20px 40px;">
        <h1 style="font-size:1.4rem;">Detail Laporan #<?= $row['id_laporan'] ?></h1>
        <p>Tinjau dan ambil tindakan atas laporan ini.</p>
    </div>

    <div class="page-body-narrow">

        <div class="status-bar">
            <h2>Status Laporan</h2>
            <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span>
        </div>

        <div class="compact-detail-box">
            <div class="info-grid-detail">
                <div class="info-row">
                    <strong>Kode Lacak</strong>
                    <span style="font-weight:bold; color:#e74c3c;"><?= $row['kode_lacak'] ?></span>
                </div>
                <div class="info-row">
                    <strong>Tanggal Masuk</strong>
                    <span><?= date('d M Y, H:i', strtotime($row['tanggal_lapor'])) ?> WITA</span>
                </div>
                <div class="info-row">
                    <strong>Kategori Fasilitas</strong>
                    <span><?= $row['nama_kategori'] ?></span>
                </div>
                <?php if (!empty($row['nama_petugas'])): ?>
                <div class="info-row">
                    <strong>Ditugaskan Kepada</strong>
                    <span><span style="background:#3498db;color:#fff;padding:2px 8px;border-radius:4px;font-size:0.88em;"><?= $row['nama_petugas'] ?></span></span>
                </div>
                <?php endif; ?>
                <div class="info-row <?= empty($row['nama_petugas']) ? 'full-width' : '' ?>">
                    <strong>Lokasi / Patokan</strong>
                    <span>
                        <?php if(!empty($row['alamat_manual'])) { echo $row['alamat_manual']; } ?>
                        <?php if(!empty($row['latitude']) && !empty($row['longitude'])): ?>
                            <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn-map" style="margin-top:5px;padding:4px 10px;font-size:0.8rem;display:inline-block;"> Maps</a>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-row full-width">
                    <strong>Deskripsi Keluhan</strong>
                    <span><?= $row['keluhan'] ?></span>
                </div>
            </div>

            <div class="foto-section">
                <strong>Foto Laporan</strong>
                <img src="uploads/<?= $row['foto'] ?>" alt="Foto Laporan Warga" class="foto-compact">
            </div>

            <?php if ($row['status'] == 'selesai' && !empty($row['foto_bukti'])): ?>
            <div class="foto-section" style="margin-top:12px;padding-top:12px;border-top:1px dashed #ccc;">
                <strong style="color:#27ae60;">Foto Bukti Perbaikan</strong>
                <img src="uploads/<?= $row['foto_bukti'] ?>" alt="Foto Bukti" class="foto-compact" style="border-color:#2ecc71;">
            </div>
            <?php endif; ?>
        </div>

        <?php if ($row['status'] == 'menunggu'): ?>
        <div class="compact-action-box">
            <?php if ($id_instansi_admin == 1): ?>
                <h2>Teruskan Laporan</h2>
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

                <form action="proses_validasi.php" method="POST" style="margin-top:15px; padding:15px; border:1.5px dashed #e74c3c; border-radius:8px; background:#fdf0ed;">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <label style="font-weight:700; color:#c0392b; font-size:0.88rem; display:block; margin-bottom:8px;">Alasan Penolakan (Sertakan Kode Lacak Utama jika duplikat):</label>
                    <textarea name="keterangan" placeholder="Contoh: Laporan duplikat. Silakan pantau laporan utama dengan kode LP-XXXXXX-XXXX" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #e74c3c; margin-bottom:10px; box-sizing:border-box; min-height:70px;"></textarea>
                    <button type="submit" name="aksi" value="tolak" class="btn-tolak-merah" onclick="return confirm('Yakin ingin menolak laporan ini?')"> Tolak Laporan </button>
                </form>
            <?php else: ?>
                <h2>Tindak Lanjut Laporan</h2>
                <form action="proses_validasi.php" method="POST" id="form-terima-tugaskan">
                    <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                    <select name="id_petugas" required style="margin-bottom:10px;">
                        <option value="" disabled selected>-- Pilih Tim / Petugas Lapangan --</option>
                        <?php foreach($opsi_data as $p): ?>
                            <option value="<?= $p['id_petugas'] ?>"><?= $p['nama_lengkap'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="action-btn-group">
                        <button type="submit" name="aksi" value="terima" class="btn-terima-hijau">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Terima & Tugaskan
                        </button>
                    </div>
                </form>

                <div style="margin-top:10px;">
                    <button type="button" class="btn-tolak-merah" id="btn-toggle-tolak" onclick="toggleTolak()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Tolak Laporan
                    </button>
                    <div class="serahkan-panel" id="panel-tolak" style="margin-top:8px;">
                        <form action="proses_validasi.php" method="POST">
                            <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                            <label style="font-weight:700; color:#c0392b; font-size:0.88rem; display:block; margin-bottom:8px;">Alasan Penolakan (Sertakan Kode Lacak Utama jika duplikat):</label>
                            <textarea name="keterangan" id="textarea-tolak" placeholder="Contoh: Laporan duplikat. Silakan pantau laporan utama dengan kode LP-XXXXXX-XXXX" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #e74c3c; margin-bottom:10px; box-sizing:border-box; min-height:70px;"></textarea>
                            <button type="submit" name="aksi" value="tolak" class="btn-tolak-merah" onclick="return confirm('Yakin ingin menolak laporan ini?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Konfirmasi Tolak Laporan
                            </button>
                        </form>
                    </div>
                </div>

                <div style="margin-top:10px;">
                    <button type="button" class="btn-serahkan-kuning" id="btn-toggle-serahkan" onclick="toggleSerahkan()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 10 20 15 15 20"/><path d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                        Serahkan ke Pusat
                    </button>
                    <div class="serahkan-panel" id="panel-serahkan" style="margin-top:8px;">
                        <form action="proses_validasi.php" method="POST">
                            <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                            <label>Alasan menyerahkan ke pusat:</label>
                            <textarea name="keterangan" id="textarea-serahkan" placeholder="Tuliskan alasan menyerahkan laporan ini ke pusat..." required></textarea>
                            <button type="submit" name="aksi" value="kembalikan" class="btn-kirim-serahkan">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                Kirim ke Pusat
                            </button>
                        </form>
                    </div>
                </div>

                <script>
                function toggleSerahkan() {
                    var panel = document.getElementById('panel-serahkan');
                    var btn   = document.getElementById('btn-toggle-serahkan');
                    if (panel.classList.contains('open')) {
                        panel.classList.remove('open');
                        btn.style.opacity = '1';
                    } else {
                        panel.classList.add('open');
                        btn.style.opacity = '0.8';
                        setTimeout(function(){ document.getElementById('textarea-serahkan').focus(); }, 420);
                    }
                }
                function toggleTolak() {
                    var panel = document.getElementById('panel-tolak');
                    var btn   = document.getElementById('btn-toggle-tolak');
                    if (panel.classList.contains('open')) {
                        panel.classList.remove('open');
                        btn.style.opacity = '1';
                    } else {
                        panel.classList.add('open');
                        btn.style.opacity = '0.8';
                        setTimeout(function(){ document.getElementById('textarea-tolak').focus(); }, 420);
                    }
                }
                </script>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($row['status'] == 'diproses' && $id_instansi_admin != 1): ?>
        <div class="compact-action-box" style="border-color:#3498db;">
            <h2 style="color:#2980b9;">Ganti Penugasan</h2>
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
                <label style="font-weight:700;font-size:0.88rem;margin-top:8px;display:block;">Alasan Pergantian:</label>
                <textarea name="keterangan" placeholder="Alasan mengganti petugas..." required></textarea>
                <button type="submit" name="aksi" value="update_petugas" class="btn-update"> Simpan Perubahan Petugas</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($row['status'] == 'menunggu verifikasi'): ?>
        <div class="compact-action-box" style="border-color:#e67e22;">
            <h2 style="color:#e67e22;">Verifikasi Pekerjaan Petugas</h2>
            <p style="font-size:0.9rem;margin:0 0 10px;">Petugas <strong><?= $row['nama_petugas'] ?></strong> telah mengirimkan bukti perbaikan.</p>
            <div style="text-align:center;margin-bottom:12px;">
                <img src="uploads/<?= $row['foto_bukti'] ?>" class="foto-compact" style="border:3px solid #2ecc71;max-height:220px;object-fit:cover;">
            </div>
            
            <?php 
            $q_catatan = mysqli_query($koneksi, "SELECT keterangan FROM riwayat_laporan WHERE id_laporan = '$id_laporan' AND aksi = 'kirim_bukti' ORDER BY tanggal_aksi DESC LIMIT 1");
            if ($q_catatan && mysqli_num_rows($q_catatan) > 0): 
                $data_catatan = mysqli_fetch_assoc($q_catatan);
            ?>
            <div style="background:#f0fdf4; padding:12px; border-left:4px solid #2ecc71; margin-bottom:12px; border-radius:4px;">
                <strong style="font-size:0.85rem; color:#27ae60;">Catatan Lapangan dari Petugas:</strong>
                <p style="margin:5px 0 0; font-size:0.9rem; color:#333; font-style:italic;">"<?= htmlspecialchars($data_catatan['keterangan']) ?>"</p>
            </div>
            <?php endif; ?>

            <form action="proses_validasi.php" method="POST">
                <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                
                <label style="font-weight:700;font-size:0.88rem;margin-bottom:8px;display:block;">Keterangan / Pesan untuk Warga (Opsional):</label>
                <textarea name="keterangan" placeholder="Contoh: Perbaikan telah selesai. Terima kasih atas laporannya..." style="width:100%; padding:10px; border-radius:6px; border:1px solid #dbeafe; margin-bottom:10px; box-sizing:border-box; min-height:70px; font-family:inherit;"></textarea>
                
                <button type="submit" name="aksi" value="verifikasi_terima" class="btn-terima"> Verifikasi & Selesaikan</button>
            </form>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #eee;">
                <button type="button" class="btn-tolak" id="btn-toggle-tolak-bukti" onclick="toggleTolakBukti()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Tolak Bukti & Kembalikan
                </button>
                <div class="serahkan-panel" id="panel-tolak-bukti" style="margin-top:8px;">
                    <form action="proses_validasi.php" method="POST">
                        <input type="hidden" name="id_laporan" value="<?= $row['id_laporan'] ?>">
                        <label style="font-weight:700;color:#e74c3c;font-size:0.88rem;">Opsi Tolak Bukti:</label>
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
                        <label style="font-weight:700;font-size:0.88rem;margin-top:8px;display:block;">Alasan Penolakan:</label>
                        <textarea name="keterangan" id="textarea-tolak-bukti" placeholder="Berikan alasan mengapa bukti ditolak..." required></textarea>
                        <button type="submit" name="aksi" value="verifikasi_tolak" id="btn_tolak" class="btn-tolak"> Tolak Bukti & Kembalikan</button>
                    </form>
                </div>
            </div>
        </div>
        <script>
        function updateTombolTolak() {
            var select = document.getElementById('select_petugas_baru');
            var btn = document.getElementById('btn_tolak');
            if (select.value === "") { btn.innerHTML = " Tolak Bukti & Kembalikan"; } 
            else { var nama = select.options[select.selectedIndex].text.split(' (')[0]; btn.innerHTML = " Tolak & Alihkan Tugas ke " + nama; }
        }
        function toggleTolakBukti() {
            var panel = document.getElementById('panel-tolak-bukti');
            var btn   = document.getElementById('btn-toggle-tolak-bukti');
            if (panel.classList.contains('open')) {
                panel.classList.remove('open');
                btn.style.opacity = '1';
            } else {
                panel.classList.add('open');
                btn.style.opacity = '0.8';
                setTimeout(function(){ document.getElementById('textarea-tolak-bukti').focus(); }, 420);
            }
        }
        </script>
        <?php endif; ?>

        <div class="section-label">Log Riwayat Laporan</div>
        <div style="overflow-x:auto;">
            <table class="log-table">
                <thead><tr><th>Waktu</th><th>Oleh</th><th>Aksi</th><th>Keterangan</th></tr></thead>
                <tbody>
                    <?php if(mysqli_num_rows($riwayat) == 0): ?>
                        <tr><td colspan="4" style="text-align:center;font-style:italic;color:#90a4ae;">Belum ada riwayat aktivitas.</td></tr>
                    <?php endif; ?>
                    <?php while($r = mysqli_fetch_assoc($riwayat)): ?>
                    <tr>
                        <td style="white-space:nowrap;"><?= date('d/m H:i', strtotime($r['tanggal_aksi'])) ?></td>
                        <td><strong><?= $r['nama_lengkap'] ?></strong></td>
                        <td><?= ucfirst(str_replace('_', ' ', $r['aksi'])) ?></td>
                        <td style="color:#666;font-style:italic;"><?= $r['keterangan'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            <button type="button" onclick="history.back()" class="btn-kembali" style="margin-top:0;">← Kembali</button>
        </div>
    </div>
    <footer class="site-footer">© 2025 <span>LaporFasum</span> — Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>