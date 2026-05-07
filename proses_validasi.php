<?php
require 'koneksi.php';

if (isset($_POST['aksi']) && isset($_POST['id_laporan'])) {
    
    $id_laporan = mysqli_real_escape_string($koneksi, $_POST['id_laporan']);
    $aksi       = $_POST['aksi']; 

    if ($aksi == 'terima') {
        // Admin instansi
        $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
        
        // Ubah status jadi 'diproses' dan isi kolom id_petugas
        $query = "UPDATE laporan SET status = 'diproses', id_petugas = '$id_petugas' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Sukses! Laporan telah diterima dan ditugaskan ke tim lapangan.";
        
    } elseif ($aksi == 'forward') {
        // atmin pusat
        $id_kategori_baru = mysqli_real_escape_string($koneksi, $_POST['id_kategori_baru']);
        
        // Ubah id_kategori saja. Status tetap 'menunggu' agar tampil di dashboard Dinas yang baru.
        $query = "UPDATE laporan SET id_kategori = '$id_kategori_baru' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Sukses! Laporan berhasil diteruskan ke dinas terkait.";

    } elseif ($aksi == 'tolak') {
        $query = "UPDATE laporan SET status = 'ditolak' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Laporan telah ditolak.";

    } elseif ($aksi == 'verifikasi_terima') {
        $query = "UPDATE laporan SET status = 'selesai' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Laporan berhasil diverifikasi ";

    } elseif ($aksi == 'verifikasi_tolak') {
        $query = "UPDATE laporan SET status = 'diproses' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Bukti ditolak. Laporan dikembalikan ke petugas.";
    }
    // Eksekusi perintah SQL ke database
    $update = mysqli_query($koneksi, $query);

    // Cek apakah update berhasil
    if ($update) {
        echo "<script>
                alert('$pesan_sukses');
                window.location.href = 'admin.php'; // Tendang kembali ke halaman dashboard admin
              </script>";
    } else {
        echo "Gagal memproses validasi: " . mysqli_error($koneksi);
    }

} else {
    echo "Akses tidak sah!";
}
?>