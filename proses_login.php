<?php
session_start();
require 'koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) > 0) {
    
    $akun = mysqli_fetch_assoc($result);
    
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
    echo "<script>
            alert('Username atau Password salah');
            window.location.href = 'login.html';
          </script>";
}
?>