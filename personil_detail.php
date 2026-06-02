<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}
require 'koneksi.php';

if (!isset($_GET['id'])) { die("ID User tidak ditemukan."); }

$id_user_target = (int)$_GET['id'];
$id_instansi_admin = $_SESSION['id_instansi'];

$query = "SELECT u.*, i.nama_instansi 
          FROM users u 
          JOIN instansi i ON u.id_instansi = i.id_instansi 
          WHERE u.id_user = $id_user_target";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if (!$user || ($id_instansi_admin != 1 && $user['id_instansi'] != $id_instansi_admin)) {
    die("Akses Ditolak! Anda tidak memiliki wewenang melihat personil ini.");
}

$query_tugas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE id_petugas = $id_user_target");
$stat = mysqli_fetch_assoc($query_tugas);
$total_tugas = $stat['total'];

$query_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan WHERE id_petugas = $id_user_target AND status = 'selesai'");
$stat_selesai = mysqli_fetch_assoc($query_selesai);
$tugas_selesai = $stat_selesai['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Personil – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <nav class="site-navbar">
        <a href="admin.php" class="brand"><span class="brand-lapor">Lapor</span><span class="brand-fasum">Fasum</span></a>
        <nav>
            <a href="admin.php">Dashboard</a>
            <a href="personil.php">Personil</a>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');">Keluar</a>
        </nav>
    </nav>

    <div class="page-header">
        <h1>Profil Personil</h1>
        <p>Detail informasi dan manajemen akun personil.</p>
    </div>

    <div class="page-body-narrow">

        <?php if (!empty($user['pending_nama']) || !empty($user['pending_username'])): ?>
        <div class="action-box" style="border-style: solid; background-color: #fff9db; border-color: #f1c40f; margin-bottom: 25px;">
            <h3 style="margin-top: 0; color: #856404;">Permintaan Perubahan Profil</h3>
            <p style="font-size: 0.9em; color: #666;">Petugas ini mengajukan perubahan data diri sebagai berikut:</p>
            
            <table class="table-pengajuan">
                <tr>
                    <th>Data Spesifik</th>
                    <th>Data Saat Ini</th>
                    <th>Data Baru (Diajukan)</th>
                </tr>
                <tr>
                    <td><strong>Nama Lengkap</strong></td>
                    <td><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                    <td style="color: #27ae60; font-weight: bold;"><?= htmlspecialchars($user['pending_nama'] ?: $user['nama_lengkap']) ?></td>
                </tr>
                <tr>
                    <td><strong>Username</strong></td>
                    <td>@<?= htmlspecialchars($user['username']) ?></td>
                    <td style="color: #27ae60; font-weight: bold;">@<?= htmlspecialchars($user['pending_username'] ?: $user['username']) ?></td>
                </tr>
            </table>

            <div class="btn-group" style="gap: 10px;">
                <a href="proses_personil.php?aksi=setujui&id=<?= $user['id_user'] ?>" class="btn-terima" style="flex: 1; text-align: center; text-decoration: none;" onclick="return confirm('Setujui perubahan data ini?')">Setujui Perubahan</a>
                <a href="proses_personil.php?aksi=tolak&id=<?= $user['id_user'] ?>" class="btn-tolak" style="flex: 1; text-align: center; text-decoration: none; margin-top: 0;" onclick="return confirm('Tolak perubahan data?')">Tolak Pengajuan</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="profil-wrapper">
            <div class="profil-header">
                <div class="bingkai-foto">
                    <?php 
                    $foto = !empty($user['foto_profil']) ? "uploads/profil/" . $user['foto_profil'] : "assets/img/default-user.png";
                    ?>
                    <img src="<?= $foto ?>" class="foto-profil-presisi" alt="Foto Profil">
                </div>
                <h2 class="profil-nama">
                    <?= htmlspecialchars($user['nama_lengkap']) ?>
                    <?php if($user['status_akun'] == 'nonaktif'): ?>
                        <span style="color: #e74c3c; font-size: 0.6em; vertical-align: middle;">(NONAKTIF)</span>
                    <?php endif; ?>
                </h2>
                <span class="badge <?= $user['role'] == 'admin' ? 'badge-biru' : 'badge-hijau' ?>" style="font-size: 1em; padding: 6px 15px;">
                    <?= ucfirst($user['role']) ?> - <?= htmlspecialchars($user['nama_instansi']) ?>
                </span>
            </div>

            <div class="info-grid-2">
                <div class="info-box">
                    <span class="info-label">Username Akses</span>
                    <span class="info-value">@<?= htmlspecialchars($user['username']) ?></span>
                </div>
                <div class="info-box">
                    <span class="info-label">Nomor ID Pegawai</span>
                    <span class="info-value">#<?= $user['id_user'] ?></span>
                </div>
            </div>

            <?php if ($user['role'] == 'petugas'): ?>
            <div class="stat-container">
                <div class="stat-card">
                    <span class="stat-angka"><?= $total_tugas ?></span>
                    <span class="stat-teks">Total Tugas Diterima</span>
                </div>
                <div class="stat-card" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <span class="stat-angka" style="color: #16a34a;"><?= $tugas_selesai ?></span>
                    <span class="stat-teks" style="color: #22c55e;">Laporan Selesai</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="action-box" style="border-style: solid; background-color: #f8f9fa; border-color: #bdc3c7;">
            <h3 style="margin-top: 0; color: #2c3e50;">Opsi Manajemen Akun</h3>
            <p style="font-size: 0.9em; color: #555;">Status Akun Saat Ini: <strong><?= strtoupper($user['status_akun']) ?></strong></p>
            
            <div class="btn-group" style="gap: 10px;">
                <a href="personil_edit.php?id=<?= $user['id_user'] ?>" class="btn-update" style="background-color: #3498db; width: auto; flex: 1; text-decoration: none; text-align: center;">Edit Data</a>
                
                <?php 
                $bisa_dinonaktifkan = true;
                if ($_SESSION['id_instansi'] == 1 && $user['role'] == 'admin') {
                    $bisa_dinonaktifkan = false;
                }
                ?>

                <?php if ($bisa_dinonaktifkan): ?>
                    <?php if ($user['status_akun'] == 'aktif'): ?>
                        <a href="proses_personil.php?aksi=status&status=nonaktif&id=<?= $user['id_user'] ?>" class="btn-tolak" style="width: auto; flex: 1; margin-top: 0; text-decoration: none; text-align: center;" onclick="return confirm('Yakin ingin MENONAKTIFKAN akun ini?')">Nonaktifkan Akun</a>
                    <?php else: ?>
                        <a href="proses_personil.php?aksi=status&status=aktif&id=<?= $user['id_user'] ?>" class="btn-terima" style="width: auto; flex: 1; margin-top: 0; text-decoration: none; text-align: center;">Aktifkan Kembali</a>
                    <?php endif; ?>
                <?php endif; ?>
                </div>
        </div>

        <div style="margin-top:20px;">
            <button type="button" onclick="history.back()" class="btn-kembali" style="margin-top:0;">← Kembali</button>
        </div>

    </div>

    <footer class="site-footer">&copy; 2026 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>