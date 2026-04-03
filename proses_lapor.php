<?php
// 1. Panggil file koneksi agar terhubung ke database
require 'koneksi.php';

// 2. Menangkap data teks dari formulir (lapor.html)
// mysqli_real_escape_string berguna untuk mengamankan karakter aneh agar tidak merusak database
$keluhan       = mysqli_real_escape_string($koneksi, $_POST['keluhan']);
$metode_lokasi = $_POST['metode_lokasi'];

// 3. Logika Pemilihan Kategori (Jika pilih "Lainnya", ambil dari ketikan manual)
$kategori = $_POST['kategori'];
if ($kategori === 'Lainnya') {
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori_manual']);
}

// 4. Logika Pemilihan Lokasi (Peta vs Manual)
// Jika variabel diset "NULL" (tanpa tanda kutip), database akan mencatatnya sebagai data kosong yang sah
if ($metode_lokasi == 'peta') {
    $latitude      = "'" . mysqli_real_escape_string($koneksi, $_POST['latitude']) . "'";
    $longitude     = "'" . mysqli_real_escape_string($koneksi, $_POST['longitude']) . "'";
    $alamat_manual = "NULL"; 
} else {
    $latitude      = "NULL";
    $longitude     = "NULL";
    $alamat_manual = "'" . mysqli_real_escape_string($koneksi, $_POST['alamat_manual']) . "'";
}

// 5. Mengurus File Foto yang Diunggah
$nama_file = $_FILES['foto']['name'];
$tmp_file  = $_FILES['foto']['tmp_name'];

// Membuat nama file baru yang unik menggunakan waktu saat ini (agar foto dengan nama sama tidak saling timpa)
$nama_foto_baru = time() . '_' . str_replace(" ", "_", $nama_file);
$folder_tujuan  = "uploads/" . $nama_foto_baru;

// Mengecek apakah folder 'uploads' sudah ada. Jika belum, PHP akan membuatkannya otomatis!
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// 6. Eksekusi: Pindahkan foto ke folder, lalu simpan datanya ke MySQL
if (move_uploaded_file($tmp_file, $folder_tujuan)) {
    
    // Menyusun perintah SQL untuk memasukkan data
    $query = "INSERT INTO laporan (foto, keluhan, kategori, metode_lokasi, latitude, longitude, alamat_manual) 
              VALUES ('$nama_foto_baru', '$keluhan', '$kategori', '$metode_lokasi', $latitude, $longitude, $alamat_manual)";
    
    // Menjalankan perintah SQL
    $simpan = mysqli_query($koneksi, $query);

    // Jika berhasil tersimpan
    if ($simpan) {
        echo "<script>
                alert('Terima kasih! Laporan Anda berhasil dikirim dan akan segera diproses.');
                window.location.href = 'index.html'; // Kembali ke halaman utama
              </script>";
    } else {
        echo "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
    }

} else {
    // Jika foto gagal diunggah (misalnya karena ukuran terlalu besar)
    echo "<script>
            alert('Gagal mengunggah foto! Pastikan ukuran file tidak terlalu besar.');
            window.history.back(); // Kembali ke halaman sebelumnya
          </script>";
}
?>