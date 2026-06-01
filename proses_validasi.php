<?php
session_start();
require 'koneksi.php';
require 'helper_notif.php';

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
        $log_keterangan = "Laporan diteruskan ke dinas baru.";

    } elseif ($aksi == 'tolak') {
        $query = "UPDATE laporan SET status = 'ditolak' WHERE id_laporan = '$id_laporan'";
        $log_keterangan = $keterangan;

    } elseif ($aksi == 'kembalikan') {
        $query = "UPDATE laporan SET id_kategori = 1, status = 'menunggu', id_petugas = NULL, pesan_admin = '$keterangan' WHERE id_laporan = '$id_laporan'";
        $log_keterangan = $keterangan;

    } elseif ($aksi == 'update_petugas') {
        $id_petugas_baru = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
        $query = "UPDATE laporan SET id_petugas = '$id_petugas_baru', pesan_admin = NULL WHERE id_laporan = '$id_laporan'";
        $log_keterangan = "Tugas dialihkan. Alasan: " . $keterangan;
        $id_petugas_penerima = $id_petugas_lama;

    } elseif ($aksi == 'verifikasi_terima') {
        $query = "UPDATE laporan SET status = 'selesai' WHERE id_laporan = '$id_laporan'";    
        // Memeriksa apakah keterangan opsional dari admin diisi
        if (!empty(trim($_POST['keterangan']))) {
            $log_keterangan = $keterangan;
        } else {
            $log_keterangan = "Bukti perbaikan disetujui. Tugas selesai.";
        }

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

            if ($aksi == 'terima') {
                $id_ptg = mysqli_real_escape_string($koneksi, $_POST['id_petugas']); 
                kirim_notif($koneksi, $id_ptg, $id_laporan, "Tugas Baru", "Anda ditugaskan menangani laporan #$id_laporan.", "tugas_baru");
            
            } elseif ($aksi == 'forward') {
                $id_kat_tujuan = mysqli_real_escape_string($koneksi, $_POST['id_kategori_baru']);
                $q_inst = mysqli_query($koneksi, "SELECT id_instansi FROM kategori WHERE id_kategori = '$id_kat_tujuan'");
                $id_inst_baru = mysqli_fetch_assoc($q_inst)['id_instansi'];
                kirim_notif_ke_admin_instansi($koneksi, $id_inst_baru, $id_laporan, "Laporan Diteruskan", "Laporan #$id_laporan diteruskan ke instansi Anda.", "laporan_baru");
            
            } elseif ($aksi == 'kembalikan') {
                kirim_notif_ke_admin_instansi($koneksi, 1, $id_laporan, "Laporan Dikembalikan", "Laporan #$id_laporan diserahkan ke pusat. Alasan: $keterangan", "laporan_baru");
            
            } elseif ($aksi == 'update_petugas') {
                $id_ptg_baru = mysqli_real_escape_string($koneksi, $_POST['id_petugas']);
                kirim_notif($koneksi, $id_ptg_baru, $id_laporan, "Tugas Dialihkan", "Anda ditugaskan menggantikan petugas sebelumnya untuk laporan #$id_laporan.", "tugas_baru");
                if (!empty($id_petugas_lama)) {
                    kirim_notif($koneksi, $id_petugas_lama, $id_laporan, "Tugas Dibatalkan", "Tugas laporan #$id_laporan dialihkan ke petugas lain.", "laporan_ditolak");
                }
            
            } elseif ($aksi == 'verifikasi_terima') {
                kirim_notif($koneksi, $id_petugas_lama, $id_laporan, "Tugas Selesai", "Pekerjaan Anda untuk laporan #$id_laporan telah diverifikasi dan disetujui Admin.", "tugas_selesai");

            } elseif ($aksi == 'verifikasi_tolak') {
                $id_ptg_alihan = isset($_POST['id_petugas_baru']) ? mysqli_real_escape_string($koneksi, $_POST['id_petugas_baru']) : '';
                
                if (!empty($id_ptg_alihan)) {
                    kirim_notif($koneksi, $id_petugas_lama, $id_laporan, "Bukti Ditolak & Dialihkan", "Bukti perbaikan laporan #$id_laporan ditolak dan tugas ditarik. Alasan: $keterangan", "laporan_ditolak");
                    kirim_notif($koneksi, $id_ptg_alihan, $id_laporan, "Tugas Baru (Alihan)", "Anda ditugaskan menangani laporan #$id_laporan melanjutkan petugas sebelumnya.", "tugas_baru");
                } else {
                    kirim_notif($koneksi, $id_petugas_lama, $id_laporan, "Bukti Ditolak", "Bukti perbaikan laporan #$id_laporan ditolak Admin. Alasan: $keterangan", "laporan_ditolak");
                }
            }

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