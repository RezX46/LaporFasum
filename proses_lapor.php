<?php
require 'koneksi.php';
require 'helper_notif.php';
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

// Batas ukuran awal 5 MB
$batas_ukuran = 5 * 1024 * 1024; 
if ($ukuran_file > $batas_ukuran) {
    echo "<script>
            alert('Gagal! Ukuran foto terlalu besar, pastikan foto tidak lebih besar dari 5 MB.');
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
// Membuat kode lacak unik (Contoh: LP-202605-A1B2)
$kode_lacak = "LP-" . date('Ym') . "-" . strtoupper(substr(uniqid(), -4));

if ($upload_sukses) {
    $query = "INSERT INTO laporan (kode_lacak, foto, keluhan, id_kategori, metode_lokasi, latitude, longitude, alamat_manual) 
              VALUES ('$kode_lacak', '$nama_foto_baru', '$keluhan', $id_kategori, '$metode_lokasi', '$latitude', '$longitude', $alamat_manual)";
    
    $simpan = mysqli_query($koneksi, $query);
    
    if ($simpan) {
        $id_laporan_baru = mysqli_insert_id($koneksi);
        $q_instansi = mysqli_query($koneksi, "SELECT id_instansi, nama_kategori FROM kategori WHERE id_kategori = $id_kategori");
        $dt_inst = mysqli_fetch_assoc($q_instansi);
        kirim_notif_ke_admin_instansi($koneksi, $dt_inst['id_instansi'], $id_laporan_baru, "Laporan Baru Masuk", "Laporan #$id_laporan_baru kategori ".$dt_inst['nama_kategori']." menunggu tindak lanjut Anda.", "laporan_baru");
        echo "<script>
                alert('Terima kasih! Laporan berhasil dikirim. CATAT KODE LACAK ANDA: $kode_lacak');
                window.location.href = 'cek_status.php?kode=$kode_lacak';
            </script>";
    } else {
        echo "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
    }
} else {
    echo "<script>
            alert('Gagal mengunggah dan memproses foto. Silakan coba lagi.');
            window.history.back();
          </script>";
}
?>