<?php
require 'koneksi.php';

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

$nama_file = $_FILES['foto']['name'];
$tmp_file  = $_FILES['foto']['tmp_name'];
$ukuran_file = $_FILES['foto']['size']; // Tangkap ukuran filenya

$batas_ukuran = 2 * 1024 * 1024; //  2 MB
if ($ukuran_file > $batas_ukuran) {
    echo "<script>
            alert('Gagal! Ukuran foto terlalu besar, pastikan foto tidak lebih besar dari 2 MB.');
            window.history.back();
          </script>";
    exit(); 
}

$nama_foto_baru = time() . '_' . str_replace(" ", "_", $nama_file);
$folder_tujuan  = "uploads/" . $nama_foto_baru;
// ... (kode ke bawahnya tetap sama)

if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

if (move_uploaded_file($tmp_file, $folder_tujuan)) {
    
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
            alert('Gagal mengunggah foto! Pastikan file dipilih benar dan ukurannya tidak terlalu besar.');
            window.history.back();
          </script>";
}
?>