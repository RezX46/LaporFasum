<?php
// 1. Panggil koneksi database
require 'koneksi.php';

// 2. Mengecek apakah file ini benar-benar diakses lewat form (ada tombol aksi yang ditekan)
if (isset($_POST['aksi']) && isset($_POST['id_laporan'])) {
    
    // Menangkap ID Laporan yang dikirim dari form tersembunyi
    $id_laporan = $_POST['id_laporan'];
    $aksi       = $_POST['aksi']; // Isinya bisa 'terima' atau 'tolak'

    // 3. Logika Percabangan Aksi
    if ($aksi == 'terima') {
        // Jika Admin klik Terima, tangkap ID petugas yang dipilih dari dropdown
        $id_petugas = $_POST['petugas'];
        
        // Perintah SQL: Ubah status jadi 'diproses' dan isi kolom id_petugas
        $query = "UPDATE laporan SET status = 'diproses', id_petugas = '$id_petugas' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Sukses! Laporan telah diterima dan ditugaskan ke petugas.";
        
    } elseif ($aksi == 'tolak') {
        // Jika Admin klik Tolak, cukup ubah statusnya saja
        $query = "UPDATE laporan SET status = 'ditolak' WHERE id_laporan = '$id_laporan'";
        $pesan_sukses = "Laporan telah ditolak.";
    }

    // 4. Eksekusi perintah SQL ke database
    $update = mysqli_query($koneksi, $query);

    // 5. Cek apakah update berhasil
    if ($update) {
        echo "<script>
                alert('$pesan_sukses');
                window.location.href = 'admin.php'; // Tendang kembali ke halaman dashboard admin
              </script>";
    } else {
        echo "Gagal memproses validasi: " . mysqli_error($koneksi);
    }

} else {
    // Jika seseorang iseng mencoba membuka file ini langsung lewat URL browser
    echo "Akses tidak sah!";
}
?>