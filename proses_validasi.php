<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

if (isset($_POST['aksi']) && isset($_POST['id_laporan'])) {
    
    $id_laporan = mysqli_real_escape_string($koneksi, $_POST['id_laporan']);
    $id_admin   = $_SESSION['id_user']; 
    $aksi       = $_POST['aksi']; 
    
    $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($koneksi, $_POST['keterangan']) : '-';

    $data_lama = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_petugas FROM laporan WHERE id_laporan = '$id_laporan'"));
    $id_petugas_lama = $data_lama['id_petugas'];
    
    $id_petugas_penerima = NULL; 
    $query = "";

    if ($aksi == 'terima') {
        $id_petugas = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
        $query = "UPDATE laporan SET status = 'diproses', id_petugas = '$id_petugas', pesan_admin = NULL WHERE id_laporan = '$id_laporan'";
        $log_keterangan = "Laporan diterima dan ditugaskan ke petugas.";

    } elseif ($aksi == 'forward') {
        $id_kat = mysqli_real_escape_string($koneksi, $_POST['id_kategori_baru']);
        $query = "UPDATE laporan SET id_kategori = '$id_kat' WHERE id_laporan = '$id_laporan'";
        $log_keterangan = "Laporan diteruskan ke kategori/dinas baru.";

    } elseif ($aksi == 'tolak') {
        $query = "UPDATE laporan SET status = 'ditolak' WHERE id_laporan = '$id_laporan'";
        $log_keterangan = "Laporan ditolak oleh admin.";

    } elseif ($aksi == 'kembalikan') {
        $query = "UPDATE laporan SET id_kategori = 1, status = 'menunggu', id_petugas = NULL, pesan_admin = '$keterangan' WHERE id_laporan = '$id_laporan'";
        $log_keterangan = $keterangan;

    } elseif ($aksi == 'update_petugas') {
        $id_petugas_baru = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
        $query = "UPDATE laporan SET id_petugas = '$id_petugas_baru', pesan_admin = NULL WHERE id_laporan = '$id_laporan'";
        $log_keterangan = "Tugas dialihkan. Alasan: " . $keterangan;
        $id_petugas_penerima = $id_petugas_lama;

    } elseif ($aksi == 'verifikasi_terima') {
        $query = "UPDATE laporan SET status = 'selesai', pesan_admin = NULL WHERE id_laporan = '$id_laporan'";
        $log_keterangan = "Bukti perbaikan disetujui. Tugas selesai.";

    } elseif ($aksi == 'verifikasi_tolak') {
        $id_petugas_baru = isset($_POST['id_petugas_baru']) ? mysqli_real_escape_string($koneksi, $_POST['id_petugas_baru']) : '';
        
        if (!empty($id_petugas_baru)) {
            $query = "UPDATE laporan SET status = 'diproses', id_petugas = '$id_petugas_baru', pesan_admin = NULL, foto_bukti = NULL WHERE id_laporan = '$id_laporan'";
            $id_petugas_penerima = $id_petugas_lama;
        } else {
            $query = "UPDATE laporan SET status = 'diproses', pesan_admin = '$keterangan', foto_bukti = NULL WHERE id_laporan = '$id_laporan'";
        }
        $log_keterangan = "Bukti ditolak. Alasan: " . $keterangan;
    }

    if (!empty($query)) {
        if (mysqli_query($koneksi, $query)) {
            $sql_log = "INSERT INTO riwayat_laporan (id_laporan, id_user, id_petugas_penerima, aksi, keterangan) 
                        VALUES ('$id_laporan', '$id_admin', " . ($id_petugas_penerima ? "'$id_petugas_penerima'" : "NULL") . ", '$aksi', '$log_keterangan')";
            mysqli_query($koneksi, $sql_log);
            
            echo "<script>alert('Aksi berhasil diproses!'); window.location.href = 'admin.php';</script>";
        } else {
            echo "Gagal memperbarui data: " . mysqli_error($koneksi);
        }
    } else {
        echo "Aksi tidak dikenal.";
    }

} else {
    echo "Akses tidak sah!";
}
?>