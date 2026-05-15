<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.html");
    exit();
}
require 'koneksi.php';

if (!isset($_GET['id'])) { die("ID User tidak ditemukan."); }

$id_target = (int)$_GET['id'];
$id_instansi_admin = $_SESSION['id_instansi'];

$query = mysqli_query($koneksi, "SELECT u.*, i.nama_instansi FROM users u JOIN instansi i ON u.id_instansi = i.id_instansi WHERE u.id_user = $id_target");
$user = mysqli_fetch_assoc($query);

if (!$user || ($id_instansi_admin != 1 && $user['id_instansi'] != $id_instansi_admin)) {
    die("Akses Ditolak!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Personil - LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <div class="container admin-detail-container" style="max-width: 600px;">
        <div class="header-admin">
            <h1 style="margin: 0;">Edit Data Personil</h1>
            <a href="personil_detail.php?id=<?= $id_target ?>" class="btn-detail" style="background-color: #95a5a6;">Batal</a>
        </div>

        <div class="form-section" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
            <form action="proses_personil.php?aksi=update_manual&id=<?= $id_target ?>" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group" style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #ccc;">
                    <label style="color: #e67e22;">Reset Password:</label>
                    <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-terima" style="width: 100%;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
        
        <p style="font-size: 0.8em; color: #7f8c8d; margin-top: 15px; text-align: center;">

        </p>
    </div>
</body>
</html>