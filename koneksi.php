<?php
$host       = "localhost";
$username   = "root";    
$password   = "";         // Password default
$database   = "db_lapor_fasum";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
} else {
    //echo "Koneksi ke database berhasil";
}
?>