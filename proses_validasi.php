<?php
require 'koneksi.php';

if (isset($_POST['aksi']) && isset($_POST['id_laporan'])) {
    
    $id_laporan = mysqli_real_escape_string($koneksi, $_POST['id_laporan']);
    $aksi       = $_POST['aksi']; 

    if ($aksi == 'terima') {
        // Admin instansi menerima laporan
        $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
        
        $query = "UPDATE laporan SET status = 'diproses', id_petugas = '$id_petugas' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Sukses! Laporan telah diterima dan ditugaskan ke tim lapangan.";
        
    } elseif ($aksi == 'forward') {
        // Admin pusat meneruskan laporan
        $id_kategori_baru = mysqli_real_escape_string($koneksi, $_POST['id_kategori_baru']);
        
        $query = "UPDATE laporan SET id_kategori = '$id_kategori_baru' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Sukses! Laporan berhasil diteruskan ke dinas terkait.";

    } elseif ($aksi == 'tolak') {
        // Laporan awal ditolak
        $query = "UPDATE laporan SET status = 'ditolak' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Laporan telah ditolak.";

    } elseif ($aksi == 'update_petugas') {
        // Mengganti petugas saat laporan sedang diproses
        $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
        
        $query = "UPDATE laporan SET id_petugas = '$id_petugas' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Sukses! Petugas lapangan berhasil diganti.";

    } elseif ($aksi == 'verifikasi_terima') {
        // Bukti diterima
        $query = "UPDATE laporan SET status = 'selesai' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Laporan berhasil diverifikasi dan diselesaikan.";

    } elseif ($aksi == 'verifikasi_tolak') {
        // Bukti ditolak, cek apakah dialihkan ke petugas baru
        $id_petugas_baru = isset($_POST['id_petugas_baru']) ? mysqli_real_escape_string($koneksi, $_POST['id_petugas_baru']) : '';
        
        if (!empty($id_petugas_baru)) {
            $query = "UPDATE laporan SET status = 'diproses', id_petugas = '$id_petugas_baru' WHERE id_laporan = '$id_laporan'";
            $pesan_sukses = "Bukti ditolak. Laporan kini dialihkan ke petugas baru.";
        } else {
            $query = "UPDATE laporan SET status = 'diproses' WHERE id_laporan = '$id_laporan'";
            $pesan_sukses = "Bukti ditolak. Laporan dikembalikan ke petugas semula untuk diperbaiki.";
        }
    }

    // Eksekusi perintah SQL ke database
    $update = mysqli_query($koneksi, $query);

    if ($update) {
        echo "<script>
                alert('$pesan_sukses');
                window.location.href = 'admin.php'; 
              </script>";
    } else {
        echo "Gagal memproses validasi: " . mysqli_error($koneksi);
    }

} else {
    echo "Akses tidak sah!";
}
?>