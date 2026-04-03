<?php
// 1. Wajib dipanggil paling atas untuk memulai "Kartu Pengenal" (Session)
session_start();

// 2. Hubungkan ke database
require 'koneksi.php';

// 3. Tangkap ketikan dari form login
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// 4. Cari ke database: "Apakah ada baris di tabel users yang username dan password-nya persis seperti ini?"
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($koneksi, $query);

// 5. Cek apakah datanya ketemu (jumlah barisnya lebih dari 0)
if (mysqli_num_rows($result) > 0) {
    
    // Ambil datanya
    $akun = mysqli_fetch_assoc($result);

    // 6. Buat Session (Kartu Pengenal) untuk pengguna ini
    $_SESSION['id_user']      = $akun['id_user'];
    $_SESSION['nama_lengkap'] = $akun['nama_lengkap'];
    $_SESSION['role']         = $akun['role'];

    // 7. Pintu Pemisah (Redirect berdasarkan Role)
    if ($akun['role'] == 'admin') {
        // Jika dia admin, lempar ke dashboard admin
        header("Location: admin.php");
        exit(); // Hentikan eksekusi script di bawahnya
    } elseif ($akun['role'] == 'petugas') {
        // Jika dia petugas, lempar ke dashboard petugas
        header("Location: petugas.php"); // (File ini akan kita ubah sebentar lagi)
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