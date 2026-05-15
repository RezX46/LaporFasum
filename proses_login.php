<?php
session_start();
require 'koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password_input = $_POST['password']; 

$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) > 0) {
    
    $akun = mysqli_fetch_assoc($result);
    
    // Proses Verifikasi Hashing
    if (password_verify($password_input, $akun['password'])) {
        
        // Jika password cocok, buat sesi
        $_SESSION['id_user']      = $akun['id_user'];
        $_SESSION['nama_lengkap'] = $akun['nama_lengkap'];
        $_SESSION['role']         = $akun['role'];
        $_SESSION['id_instansi']  = $akun['id_instansi']; 

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