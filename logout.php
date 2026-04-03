<?php
session_start();
session_destroy(); // Menghancurkan semua kartu pengenal (session)

// Arahkan kembali ke halaman login
header("Location: login.html");
exit();
?>