<?php
session_start();

// Hanya Admin yang boleh menghapus data
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='login.html';</script>";
    exit();
}

require 'koneksi.php';

// Mengecek apakah ada parameter 'id' yang dikirim melalui URL 
if (isset($_GET['id'])) {
    $id_laporan = $_GET['id'];

    // Ambil nama file foto dari database sebelum datanya dihapus
    $query_foto = "SELECT foto, foto_bukti FROM laporan WHERE id_laporan = '$id_laporan'";
    $hasil_foto = mysqli_query($koneksi, $query_foto);
    $data_foto = mysqli_fetch_assoc($hasil_foto);

    // Hapus file foto pelapor dari folder uploads/ (jika ada)
    if ($data_foto['foto'] != "" && file_exists("uploads/" . $data_foto['foto'])) {
        unlink("uploads/" . $data_foto['foto']); // unlink adalah fungsi PHP untuk menghapus file
    }

    // Hapus file foto bukti perbaikan dari folder uploads/ (jika ada)
    if ($data_foto['foto_bukti'] != "" && file_exists("uploads/" . $data_foto['foto_bukti'])) {
        unlink("uploads/" . $data_foto['foto_bukti']);
    }

    // Eksekusi query DELETE untuk menghapus baris laporan dari database
    $query_hapus = "DELETE FROM laporan WHERE id_laporan = '$id_laporan'";
    $hapus = mysqli_query($koneksi, $query_hapus);

    if ($hapus) {
        echo "<script>
                alert('Laporan beserta fotonya berhasil dihapus!');
                window.location.href = 'admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus laporan!');
                window.location.href = 'admin.php';
              </script>";
    }
} else {
    // Jika tidak ada ID di URL
    header("Location: admin.php");
}
?>