<?php
session_start();
require 'helper_notif.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}

require 'koneksi.php';
require 'helper_gambar.php'; 

if (isset($_POST['id_laporan']) && isset($_FILES['foto_bukti'])) {
    
    $id_laporan  = mysqli_real_escape_string($koneksi, $_POST['id_laporan']);
    $id_user     = $_SESSION['id_user']; // ID Petugas untuk keperluan Log Riwayat
    
    $tmp_file    = $_FILES['foto_bukti']['tmp_name'];
    $ukuran_file = $_FILES['foto_bukti']['size'];
    
    $batas_ukuran = 10 * 1024 * 1024;
    if ($ukuran_file > $batas_ukuran) {
        echo "<script>
                alert('Ukuran foto terlalu besar! Maksimal unggahan adalah 10 MB.');
                window.history.back();
              </script>";
        exit();
    }
    
    $nama_foto_baru = 'bukti_' . time() . '_' . uniqid() . '.jpg';
    $folder_tujuan  = "uploads/" . $nama_foto_baru;
    
    if (!is_dir('uploads')) { 
        mkdir('uploads', 0777, true); 
    }
    
    $upload_sukses = kompres_dan_resize_gambar($tmp_file, $folder_tujuan);
    
    if ($upload_sukses) {
        // Jika upload & kompresi berhasil, update status laporan
        $query = "UPDATE laporan 
                  SET status = 'menunggu verifikasi', foto_bukti = '$nama_foto_baru' 
                  WHERE id_laporan = '$id_laporan'";
                  
        $simpan = mysqli_query($koneksi, $query);
        
        if ($simpan) {
            // Menangkap keterangan_petugas dari form (atau fallback ke default)
            $keterangan_petugas = isset($_POST['keterangan_petugas']) ? mysqli_real_escape_string($koneksi, $_POST['keterangan_petugas']) : 'Petugas telah menyelesaikan pekerjaan dan mengirimkan foto bukti perbaikan.';
            
            $sql_log = "INSERT INTO riwayat_laporan (id_laporan, id_user, aksi, keterangan) 
                        VALUES ('$id_laporan', '$id_user', 'kirim_bukti', '$keterangan_petugas')";
            mysqli_query($koneksi, $sql_log);
            
            $id_instansi_petugas = $_SESSION['id_instansi'];
            kirim_notif_ke_admin_instansi($koneksi, $id_instansi_petugas, $id_laporan, "Bukti Perbaikan Dikirim", "Petugas telah mengirim bukti perbaikan laporan #$id_laporan.", "bukti_dikirim");

            echo "<script>
                    alert('Bukti perbaikan berhasil dikirim! Menunggu verifikasi dari Admin.');
                    window.location.href = 'petugas.php';
                  </script>";
        } else {
            echo "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>
                alert('Gagal memproses foto! Pastikan format file adalah gambar yang valid.');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Data tidak lengkap!');
            window.history.back();
          </script>";
}
?>