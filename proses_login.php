<?php
session_start();
require 'koneksi.php';

// Menangkap inputan dari form login
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// cek tabel user
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($koneksi, $query);

// Cek apakah datanya ketemu 
if (mysqli_num_rows($result) > 0) {
    
    // Ambil data dasar
    $akun = mysqli_fetch_assoc($result);
    $id_user = $akun['id_user'];

    // Buat Session 
    $_SESSION['id_user']      = $id_user;
    $_SESSION['nama_lengkap'] = $akun['nama_lengkap'];
    $_SESSION['role']         = $akun['role'];

    //  Ambil id_instansi dari tabel bawah kalau role-nya sesuai
    if ($akun['role'] == 'admin') {
        
        $query_admin = mysqli_query($koneksi, "SELECT id_instansi FROM admin WHERE id_user = '$id_user'");
        $data_admin = mysqli_fetch_assoc($query_admin);
        
        // Simpan id_instansi ke session agar bisa dipakai di halaman admin
        $_SESSION['id_instansi'] = $data_admin['id_instansi'];
        
        header("Location: admin.php");
        exit(); 

    } elseif ($akun['role'] == 'petugas') {
        
        $query_petugas = mysqli_query($koneksi, "SELECT id_instansi FROM petugas WHERE id_user = '$id_user'");
        $data_petugas = mysqli_fetch_assoc($query_petugas);
        
        // Simpan id_instansi ke session agar bisa dipakai di halaman petugas
        $_SESSION['id_instansi'] = $data_petugas['id_instansi'];
        
        header("Location: petugas.php"); 
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