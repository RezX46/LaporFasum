<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}
require 'koneksi.php';

$id_instansi_admin = $_SESSION['id_instansi'];

// Admin Pusat melihat semua akun kecuali dirinya sendiri
if ($id_instansi_admin == 1) {
    $query = "SELECT u.*, i.nama_instansi 
              FROM users u 
              JOIN instansi i ON u.id_instansi = i.id_instansi 
              WHERE u.id_instansi != 1
              ORDER BY i.nama_instansi ASC, u.role ASC, u.nama_lengkap ASC";
    $judul_halaman = "Manajemen Seluruh Personil";
} else {
    // Admin Dinas hanya melihat petugas di instansinya
    $query = "SELECT u.*, i.nama_instansi 
              FROM users u 
              JOIN instansi i ON u.id_instansi = i.id_instansi 
              WHERE u.role = 'petugas' AND u.id_instansi = '$id_instansi_admin'
              ORDER BY u.nama_lengkap ASC";
    $judul_halaman = "Daftar Petugas";
}

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judul_halaman ?> - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container admin-container">
        <div class="header-admin">
            <h1 style="margin: 0; font-size: 1.5em;"><?= $judul_halaman ?></h1>
            <a href="admin.php" class="btn-detail" style="background-color: #95a5a6; width: auto; padding: 8px 15px;">Kembali</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama & Username</th>
                    <th>Peran</th>
                    <?php if($id_instansi_admin == 1): ?>
                        <th>Instansi</th>
                    <?php endif; ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr><td colspan="5" style="text-align:center;">Belum ada personil yang terdaftar.</td></tr>
                <?php endif; ?>

                <?php while($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td style="width: 70px; text-align: center;">
                        <?php 
                        $foto = !empty($user['foto_profil']) ? "uploads/profil/" . $user['foto_profil'] : "assets/img/default-user.png";
                        ?>
                        <img src="<?= $foto ?>" class="foto-profil-kecil" alt="Profil">
                    </td>
                    <td>
                        <strong><?= $user['nama_lengkap'] ?></strong><br>
                        <span class="text-username">@<?= $user['username'] ?></span>
                    </td>
                    <td>
                        <?php if($user['role'] == 'admin'): ?>
                            <span class="role-badge role-admin">Admin</span>
                        <?php else: ?>
                            <span class="role-badge role-petugas">Petugas</span>
                        <?php endif; ?>
                    </td>
                    <?php if($id_instansi_admin == 1): ?>
                        <td style="font-size: 0.9em;"><?= $user['nama_instansi'] ?></td>
                    <?php endif; ?>
                    <td>
                        <a href="personil_detail.php?id=<?= $user['id_user'] ?>" class="btn-detail">Lihat Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>