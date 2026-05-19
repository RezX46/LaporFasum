<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    die("Akses Ditolak!");
}

$id_user = $_SESSION['id_user'];
$aksi = $_POST['aksi'] ?? '';

// Jika tombol "Bersihkan Semua" ditekan
if ($aksi == 'hapus_semua') {
    mysqli_query($koneksi, "DELETE FROM notifikasi WHERE id_user = '$id_user'");
} 
// Jika tombol "silang (x)" pada satu notifikasi ditekan
elseif ($aksi == 'hapus_satu') {
    $id_notif = (int)$_POST['id_notifikasi'];
    mysqli_query($koneksi, "DELETE FROM notifikasi WHERE id_notifikasi = '$id_notif' AND id_user = '$id_user'");
}

// Kembali ke halaman sebelumnya secara otomatis
$referer = $_SERVER['HTTP_REFERER'] ?? 'index.html';
header("Location: " . $referer);
exit();
?>