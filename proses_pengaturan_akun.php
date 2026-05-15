<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_user'])) { 
    die("Akses Ilegal!"); 
}

$id_user = (int)$_SESSION['id_user'];
$aksi = $_POST['aksi'] ?? '';

if ($aksi == 'update_foto') {
    $nama_file = $_FILES['foto_profil']['name'];
    $tmp_file = $_FILES['foto_profil']['tmp_name'];
    
    $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
    $nama_baru = "user_" . $id_user . "_" . time() . "." . $ekstensi;
    $tujuan = "uploads/profil/" . $nama_baru;

    if (!is_dir('uploads/profil')) { 
        mkdir('uploads/profil', 0777, true); 
    }

    if (move_uploaded_file($tmp_file, $tujuan)) {
        // Hapus foto lama agar server tidak penuh
        $query_lama = mysqli_query($koneksi, "SELECT foto_profil FROM users WHERE id_user = $id_user");
        $data_lama = mysqli_fetch_assoc($query_lama);
        
        if (!empty($data_lama['foto_profil']) && file_exists("uploads/profil/" . $data_lama['foto_profil'])) {
            unlink("uploads/profil/" . $data_lama['foto_profil']);
        }

        mysqli_query($koneksi, "UPDATE users SET foto_profil = '$nama_baru' WHERE id_user = $id_user");
        echo "<script>alert('Foto profil berhasil diperbarui!'); window.location.href='pengaturan_akun.php';</script>";
    } else {
        echo "<script>alert('Gagal mengunggah foto.'); window.history.back();</script>";
    }

} elseif ($aksi == 'update_identitas_langsung') {
    if ($_SESSION['role'] == 'admin') {
        $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
        $user_baru = mysqli_real_escape_string($koneksi, trim($_POST['username']));

        mysqli_query($koneksi, "UPDATE users SET nama_lengkap = '$nama', username = '$user_baru' WHERE id_user = $id_user");
        echo "<script>alert('Data identitas berhasil diperbarui!'); window.location.href='pengaturan_akun.php';</script>";
    } else {
        echo "<script>alert('Akses Ditolak!'); window.history.back();</script>";
    }

} elseif ($aksi == 'ajukan_perubahan') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $user_baru = mysqli_real_escape_string($koneksi, trim($_POST['username']));

    mysqli_query($koneksi, "UPDATE users SET pending_nama = '$nama', pending_username = '$user_baru' WHERE id_user = $id_user");
    echo "<script>alert('Permintaan perubahan data telah dikirim ke Admin dan menunggu persetujuan.'); window.location.href='pengaturan_akun.php';</script>";

} elseif ($aksi == 'update_password') {
    $pw_lama = $_POST['pw_lama'];
    $pw_baru = $_POST['pw_baru'];

    $query = mysqli_query($koneksi, "SELECT password FROM users WHERE id_user = $id_user");
    $data = mysqli_fetch_assoc($query);

    if (password_verify($pw_lama, $data['password'])) {
        $pw_hash = password_hash($pw_baru, PASSWORD_DEFAULT);
        mysqli_query($koneksi, "UPDATE users SET password = '$pw_hash' WHERE id_user = $id_user");
        echo "<script>alert('Password berhasil diganti! Gunakan password baru untuk login berikutnya.'); window.location.href='pengaturan_akun.php';</script>";
    } else {
        echo "<script>alert('Gagal! Password lama Anda salah.'); window.history.back();</script>";
    }
} else {
    header("Location: pengaturan_akun.php");
}
?>