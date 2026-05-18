<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.html");
    exit();
}
require 'koneksi.php';

$id_user = (int)$_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = $id_user");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <nav class="site-navbar">
        <a href="<?= ($_SESSION['role'] == 'admin') ? 'admin.php' : 'petugas.php' ?>" class="brand"><span>Lapor</span>Fasum</a>
        <nav>
            <a href="<?= ($_SESSION['role'] == 'admin') ? 'admin.php' : 'petugas.php' ?>">Dashboard</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </nav>
    </nav>

    <div class="page-header">
        <h1>Pengaturan Akun</h1>
        <p>Kelola informasi profil dan keamanan akun Anda.</p>
    </div>

    <div class="page-body-narrow">

        <div class="form-section">
            <h3 class="section-title">Foto Profil</h3>
            <div class="flex-upload">
                <img src="<?= !empty($user['foto_profil']) ? 'uploads/profil/'.$user['foto_profil'] : 'assets/img/default-user.png' ?>" class="foto-preview">
                <form action="proses_pengaturan_akun.php" method="POST" enctype="multipart/form-data" style="flex: 1;">
                    <input type="file" name="foto_profil" accept="image/jpeg, image/png, image/jpg" required style="margin-bottom: 10px; display: block;">
                    <button type="submit" name="aksi" value="update_foto" class="btn-update" style="margin-top: 0; width: auto;">Unggah Foto Baru</button>
                </form>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Informasi Identitas</h3>
            
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <form action="proses_pengaturan_akun.php" method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap (Sesuai ID):</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username Akses:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <button type="submit" name="aksi" value="update_identitas_langsung" class="btn-terima" style="width: auto;">Simpan Perubahan</button>
                </form>

            <?php else: ?>
                <?php if (!empty($user['pending_nama']) || !empty($user['pending_username'])): ?>
                    <div class="info-pending">
                        <strong>Menunggu Verifikasi:</strong> Anda telah mengajukan perubahan data diri. Perubahan akan diterapkan setelah disetujui oleh Admin.
                    </div>
                <?php endif; ?>

                <form action="proses_pengaturan_akun.php" method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap (Sesuai ID):</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username Akses:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <button type="submit" name="aksi" value="ajukan_perubahan" class="btn-update" style="width: auto;">Ajukan Perubahan Data</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="form-section">
            <h3 class="section-title">Keamanan Sandi</h3>
            <form action="proses_pengaturan_akun.php" method="POST">
                <div class="form-group">
                    <label>Password Saat Ini:</label>
                    <input type="password" name="pw_lama" required>
                </div>
                <div class="form-group">
                    <label>Password Baru:</label>
                    <input type="password" name="pw_baru" required>
                </div>
                <button type="submit" name="aksi" value="update_password" class="btn-terima" style="width: auto;">Perbarui Password</button>
            </form>
        </div>

        <div style="margin-top: 16px;">
            <button type="button" onclick="history.back()" style="padding: 8px 18px; background: #f0f0f0; color: #333; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; cursor: pointer;">&#8592; Kembali</button>
        </div>
    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>
</body>
</html>