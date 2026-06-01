<?php
session_start();
require 'koneksi.php';
// mengirim respon berupa JSON
header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses Ditolak!']);
    exit();
}

$id_user = $_SESSION['id_user'];
$aksi = $_POST['aksi'] ?? '';

if ($aksi == 'hapus_semua') {
    mysqli_query($koneksi, "DELETE FROM notifikasi WHERE id_user = '$id_user'");
    echo json_encode(['status' => 'success']);
} 
elseif ($aksi == 'hapus_satu') {
    $id_notif = (int)$_POST['id_notifikasi'];
    mysqli_query($koneksi, "DELETE FROM notifikasi WHERE id_notifikasi = '$id_notif' AND id_user = '$id_user'");
    echo json_encode(['status' => 'success']);
} 
else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid!']);
}
exit();
?>