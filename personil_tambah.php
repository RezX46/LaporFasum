<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}
require 'koneksi.php';

$id_instansi_admin = $_SESSION['id_instansi'];
$is_admin_pusat = ($id_instansi_admin == 1);

$query_instansi = mysqli_query($koneksi, "SELECT nama_instansi FROM instansi WHERE id_instansi = $id_instansi_admin");
$data_instansi = mysqli_fetch_assoc($query_instansi);

$query_semua_instansi = mysqli_query($koneksi, "SELECT * FROM instansi WHERE id_instansi != 1 ORDER BY nama_instansi ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Petugas – LaporFasum</title>
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
        <h1>Tambah Petugas Lapangan</h1>
        <p>Daftarkan akun petugas baru ke dalam sistem.</p>
    </div>

    <div class="page-body-narrow">
    <div class="card">

        <div class="form-section" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
            
            <?php if (!$is_admin_pusat): ?>
                <div class="info-pending" style="background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; margin-bottom: 20px;">
                    <strong>Informasi:</strong> Akun baru ini otomatis terdaftar sebagai Petugas Lapangan di <strong><?= htmlspecialchars($data_instansi['nama_instansi']) ?></strong>.
                </div>
            <?php else: ?>
                <div class="info-pending" style="background: #eef7fd; color: #2980b9; border-color: #cce3f6; margin-bottom: 20px;">
                    Silakan pilih penempatan instansi untuk petugas baru ini.
                </div>
            <?php endif; ?>

            <form action="proses_personil.php?aksi=tambah" method="POST">
                
                <div class="form-group">
                    <label>Nama Lengkap Petugas:</label>
                    <input type="text" name="nama_lengkap" required placeholder="Masukkan nama sesuai KTP/ID">
                </div>
                
                <div class="form-group">
                    <label>Username (Untuk Login):</label>
                    <input type="text" name="username" required placeholder="Contoh: petugas_baru">
                </div>

                <div class="form-group">
                    <label>Password Awal:</label>
                    <input type="password" name="password" required placeholder="Buatkan password awal">
                </div>

                <?php if ($is_admin_pusat): ?>
                    <div class="form-group" style="margin-top: 20px;">
                        <label>Penempatan Instansi:</label>
                        <select name="id_instansi" required>
                            <option value="">-- Pilih Instansi Penempatan --</option>
                            <?php while($instansi = mysqli_fetch_assoc($query_semua_instansi)): ?>
                                <option value="<?= $instansi['id_instansi'] ?>"><?= htmlspecialchars($instansi['nama_instansi']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="id_instansi" value="<?= $id_instansi_admin ?>">
                <?php endif; ?>

                <input type="hidden" name="role" value="petugas">

                <div style="margin-top: 25px;">
                    <button type="submit" class="btn-terima" style="width: 100%;">Daftarkan Petugas</button>
                </div>
            </form>
        </div>
    </div>
    <div style="margin-top: 16px;">
        <button type="button" onclick="history.back()" class="btn-kembali" style="margin-top:0;">&#8592; Kembali</button>
    </div>
    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>