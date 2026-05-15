<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    die("Akses Ditolak!"); 
}

$aksi = $_GET['aksi'] ?? '';
$id_target = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_target == 0) {
    die("ID tidak valid.");
}

if ($aksi == 'setujui') {
    $res = mysqli_query($koneksi, "SELECT pending_nama, pending_username FROM users WHERE id_user = $id_target");
    $data = mysqli_fetch_assoc($res);

    $nama_baru = mysqli_real_escape_string($koneksi, $data['pending_nama']);
    $user_baru = mysqli_real_escape_string($koneksi, $data['pending_username']);

    $query = "UPDATE users SET 
              nama_lengkap = '$nama_baru', 
              username = '$user_baru', 
              pending_nama = NULL, 
              pending_username = NULL 
              WHERE id_user = $id_target";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Perubahan profil telah disetujui!'); window.location.href='personil_detail.php?id=$id_target';</script>";
    }

} elseif ($aksi == 'tolak') {
    $query = "UPDATE users SET pending_nama = NULL, pending_username = NULL WHERE id_user = $id_target";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Perubahan profil berhasil ditolak.'); window.location.href='personil_detail.php?id=$id_target';</script>";
    }

} elseif ($aksi == 'status') {
    $status_baru = $_GET['status'] == 'aktif' ? 'aktif' : 'nonaktif';
    
    $cek_target = mysqli_query($koneksi, "SELECT role FROM users WHERE id_user = $id_target");
    $data_target = mysqli_fetch_assoc($cek_target);

    if ($_SESSION['id_instansi'] == 1 && $data_target['role'] == 'admin') {
        echo "<script>alert('Akses Ditolak!'); window.location.href='personil_detail.php?id=$id_target';</script>";
    } else {
        $query = "UPDATE users SET status_akun = '$status_baru' WHERE id_user = $id_target";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Status akun berhasil diperbarui menjadi $status_baru!'); window.location.href='personil_detail.php?id=$id_target';</script>";
        }
    }
} else {
    header("Location: personil.php");
}
?>