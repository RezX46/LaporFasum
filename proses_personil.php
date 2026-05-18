<?php
session_start();
require 'koneksi.php';
require 'helper_notif.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    die("Akses Ditolak!"); 
}

$aksi = $_GET['aksi'] ?? '';
$id_target = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($aksi == 'tambah') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
        $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
        $password = trim($_POST['password']);
        $role = mysqli_real_escape_string($koneksi, $_POST['role']); 
        $id_instansi = (int)$_POST['id_instansi'];

        $cek_user = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            echo "<script>alert('Gagal! Username sudah digunakan. Silakan pilih username lain.'); window.history.back();</script>";
            exit();
        }

        $pw_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (id_instansi, nama_lengkap, username, password, role, status_akun) 
                  VALUES ('$id_instansi', '$nama', '$username', '$pw_hash', '$role', 'aktif')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Petugas baru berhasil didaftarkan!'); window.location.href='personil.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat menyimpan data.'); window.history.back();</script>";
        }
    }
    exit(); 
}

if ($id_target == 0) {
    die("ID tidak valid.");
}

if ($aksi == 'setujui') {
    $res = mysqli_query($koneksi, "SELECT pending_nama, pending_username FROM users WHERE id_user = $id_target");
    $data = mysqli_fetch_assoc($res);

    $nama_baru = mysqli_real_escape_string($koneksi, $data['pending_nama']);
    $user_baru = mysqli_real_escape_string($koneksi, $data['pending_username']);

    $query = "UPDATE users SET nama_lengkap = '$nama_baru', username = '$user_baru', pending_nama = NULL, pending_username = NULL WHERE id_user = $id_target";
    if (mysqli_query($koneksi, $query)){
        kirim_notif($koneksi, $id_target, null, "Perubahan Disetujui", "Pengajuan perubahan nama/username Anda telah disetujui.", "akun_disetujui");
        echo "<script>alert('Perubahan profil telah disetujui!'); window.location.href='personil_detail.php?id=$id_target';</script>"; }

} elseif ($aksi == 'tolak') {
    $query = "UPDATE users SET pending_nama = NULL, pending_username = NULL WHERE id_user = $id_target";
    if (mysqli_query($koneksi, $query)) { 
        kirim_notif($koneksi, $id_target, null, "Perubahan Ditolak", "Pengajuan perubahan profil Anda ditolak oleh Admin.", "akun_ditolak");
        echo "<script>alert('Perubahan profil berhasil ditolak.'); window.location.href='personil_detail.php?id=$id_target';</script>"; }

} elseif ($aksi == 'status') {
    $status_baru = $_GET['status'] == 'aktif' ? 'aktif' : 'nonaktif';
    $cek_target = mysqli_query($koneksi, "SELECT role FROM users WHERE id_user = $id_target");
    $data_target = mysqli_fetch_assoc($cek_target);

    if ($_SESSION['id_instansi'] == 1 && $data_target['role'] == 'admin') {
        echo "<script>alert('Dilarang! Admin Pusat tidak memiliki wewenang mengubah status akun Admin Dinas.'); window.location.href='personil_detail.php?id=$id_target';</script>";
    } else {
        $query = "UPDATE users SET status_akun = '$status_baru' WHERE id_user = $id_target";
        if (mysqli_query($koneksi, $query)) { echo "<script>alert('Status akun diperbarui menjadi $status_baru!'); window.location.href='personil_detail.php?id=$id_target';</script>"; }
    }

} elseif ($aksi == 'update_manual') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
        $user_baru = mysqli_real_escape_string($koneksi, trim($_POST['username']));
        $password_baru = trim($_POST['password_baru']);

        $query_dasar = "UPDATE users SET nama_lengkap = '$nama', username = '$user_baru', pending_nama = NULL, pending_username = NULL WHERE id_user = $id_target";
        $sukses = mysqli_query($koneksi, $query_dasar);

        if ($sukses && !empty($password_baru)) {
            $pw_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET password = '$pw_hash' WHERE id_user = $id_target");
        }

        if ($sukses) { echo "<script>alert('Data personil berhasil diperbarui!'); window.location.href='personil_detail.php?id=$id_target';</script>"; } 
        else { echo "<script>alert('Gagal memperbarui data.'); window.history.back();</script>"; }
    }
} else {
    header("Location: personil.php");
}
?>