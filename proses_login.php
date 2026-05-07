<?php
session_start();

require 'koneksi.php';

// ketikan dari form login
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// cek di database
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($koneksi, $query);

// Cek datanya ketemu 
if (mysqli_num_rows($result) > 0) {
    
    // Ambil datanya
    $akun = mysqli_fetch_assoc($result);

    //Buat Session untuk pengguna ini
    $_SESSION['id_user']      = $akun['id_user'];
    $_SESSION['nama_lengkap'] = $akun['nama_lengkap'];
    $_SESSION['role']         = $akun['role'];

    if ($akun['role'] == 'admin') {
        // Jika dia admin, lempar ke dashboard admin
        header("Location: admin.php");
        exit(); // Hentikan eksekusi script di bawahnya
    } elseif ($akun['role'] == 'petugas') {
        // Jika dia petugas, lempar ke dashboard petugas
        header("Location: petugas.php"); // (nanti ubah)
        exit();
    }

} else {
    // Jika datanya tidak ketemu (username/password salah)
    echo "<script>
            alert('Username atau Password salah');
            window.location.href = 'login.html';
          </script>";
}
?>