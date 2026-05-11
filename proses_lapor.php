<?php
require 'koneksi.php';
require 'helper_gambar.php'; // untuk kecilkan ukuruan file

$keluhan       = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
$id_kategori   = (int)$_POST['id_kategori']; 
$latitude      = mysqli_real_escape_string($koneksi, $_POST['latitude']);
$longitude     = mysqli_real_escape_string($koneksi, $_POST['longitude']);

$metode_lokasi = 'peta';

if (!empty($_POST['alamat_manual'])) {
    $alamat_manual = "'" . mysqli_real_escape_string($koneksi, $_POST['alamat_manual']) . "'";
} else {
    $alamat_manual = "NULL";
}

$nama_file   = $_FILES['foto']['name'];
$tmp_file    = $_FILES['foto']['tmp_name'];
$ukuran_file = $_FILES['foto']['size']; 

// Batas ukuran awal 4 MB
$batas_ukuran = 5 * 1024 * 1024; 
if ($ukuran_file > $batas_ukuran) {
    echo "<script>
            alert('Gagal! Ukuran foto terlalu besar, pastikan foto tidak lebih besar dari 4 MB.');
            window.history.back();
          </script>";
    exit(); 
}

$nama_foto_baru = time() . '_' . uniqid() . '.jpg';
$folder_tujuan  = "uploads/" . $nama_foto_baru;

if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

$upload_sukses = kompres_dan_resize_gambar($tmp_file, $folder_tujuan);

if ($upload_sukses) {
    $query = "INSERT INTO laporan (foto, keluhan, id_kategori, metode_lokasi, latitude, longitude, alamat_manual) 
              VALUES ('$nama_foto_baru', '$keluhan', $id_kategori, '$metode_lokasi', '$latitude', '$longitude', $alamat_manual)";
    
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        echo "<script>
                alert('Terima kasih! Laporan Anda berhasil dikirim dan akan segera diproses.');
                window.location.href = 'index.html';
              </script>";
    } else {
        echo "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
    }

} else {
    echo "<script>
            alert('Gagal memproses foto! Pastikan file yang diunggah berupa citra yang valid.');
            window.history.back();
          </script>";
}
?>