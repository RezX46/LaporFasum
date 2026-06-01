<?php
session_start();
require 'koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password_input = $_POST['password']; 

$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) > 0) {
    
    $akun = mysqli_fetch_assoc($result);
    
    if (password_verify($password_input, $akun['password'])) {
        if ($akun['status_akun'] == 'nonaktif') {
            echo "<script>
                    alert('Akun Anda telah dinonaktifkan. Silakan hubungi Admin Pusat.');
                    window.location.href = 'login.html';
                  </script>";
            exit();
}
        // Jika password cocok, buat sesi
        $_SESSION['id_user']      = $akun['id_user'];
        $_SESSION['nama_lengkap'] = $akun['nama_lengkap'];
        $_SESSION['role']         = $akun['role'];
        $_SESSION['id_instansi']  = $akun['id_instansi']; 

        // notif popup
        $id_u = $akun['id_user'];
        $q_notif = mysqli_query($koneksi, "SELECT kategori_notif, COUNT(*) as jumlah FROM notifikasi WHERE id_user = $id_u AND is_read = '0' GROUP BY kategori_notif");
        
       $ringkasan = [];
        while($n = mysqli_fetch_assoc($q_notif)) {
            $jml = $n['jumlah'];
            switch($n['kategori_notif']) {
                case 'laporan_baru':    $ringkasan[] = "$jml Laporan Baru Masuk"; break;
                case 'tugas_baru':      $ringkasan[] = "$jml Tugas Baru Menunggu"; break;
                case 'tugas_selesai':   $ringkasan[] = "$jml Tugas Selesai Diverifikasi"; break; // Tambahan untuk notif verifikasi
                case 'laporan_ditolak': $ringkasan[] = "$jml Laporan/Bukti Ditolak"; break;
                case 'bukti_dikirim':   $ringkasan[] = "$jml Bukti Perbaikan Menunggu Verifikasi"; break;
                case 'akun_pengajuan':  $ringkasan[] = "$jml Pengajuan Perubahan Identitas"; break;
                case 'akun_disetujui':  $ringkasan[] = "Perubahan Data Akun Anda Telah Disetujui"; break;
                case 'akun_ditolak':    $ringkasan[] = "Perubahan Data Akun Anda Ditolak Admin"; break;
            }
        }
        
        if (count($ringkasan) > 0) {
            $pesan_popup = "Pemberitahuan Sistem:\\n\\n- " . implode("\\n- ", $ringkasan);
            $_SESSION['popup_notif'] = $pesan_popup;
            // Removed automatic is_read = 1 update to allow badge count to persist
        }

        if ($akun['role'] == 'admin') {
            header("Location: admin.php");
            exit(); 
        } elseif ($akun['role'] == 'petugas') {
            header("Location: petugas.php"); 
            exit();
        }
        
    } else {
        // Jika password salah
        echo "<script>
                alert('Username atau Password salah!');
                window.location.href = 'login.html';
              </script>";
    }

} else {
    // Jika username tidak ditemukan
    echo "<script>
            alert('Username tidak ditemukan!');
            window.location.href = 'login.html';
          </script>";
}
?>