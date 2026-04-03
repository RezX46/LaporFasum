<?php
session_start();

// 1. Keamanan: Pastikan hanya petugas yang bisa mengeksekusi file ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    die("Akses Ditolak! Anda bukan petugas.");
}

require 'koneksi.php';

// 2. Mengecek apakah form benar-benar dikirim
if (isset($_POST['id_laporan'])) {
    
    $id_laporan = $_POST['id_laporan'];

    // 3. Mengurus unggahan file foto bukti dari petugas
    $nama_file = $_FILES['foto_bukti']['name'];
    $tmp_file  = $_FILES['foto_bukti']['tmp_name'];

    // Menambahkan awalan 'bukti_' agar mudah dibedakan dari foto laporan warga
    $nama_foto_baru = "bukti_" . time() . '_' . str_replace(" ", "_", $nama_file);
    $folder_tujuan  = "uploads/" . $nama_foto_baru;

    // 4. Proses memindahkan file ke folder uploads/
    if (move_uploaded_file($tmp_file, $folder_tujuan)) {
        
        // 5. Perintah SQL: Ubah status menjadi 'selesai' dan simpan nama file buktinya
        $query = "UPDATE laporan SET status = 'selesai', foto_bukti = '$nama_foto_baru' WHERE id_laporan = '$id_laporan'";
        
        $update = mysqli_query($koneksi, $query);

        if ($update) {
            echo "<script>
                    alert('Kerja bagus! Tugas telah berhasil diselesaikan.');
                    window.location.href = 'petugas.php'; // Kembali ke dashboard petugas
                  </script>";
        } else {
            echo "Gagal menyimpan ke database: " . mysqli_error($koneksi);
        }

    } else {
        echo "<script>
                alert('Gagal mengunggah foto bukti! Pastikan ukuran gambar tidak terlalu besar.');
                window.history.back();
              </script>";
    }

} else {
    echo "Akses tidak sah!";
}
?>