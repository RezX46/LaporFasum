<?php
function kirim_notif($koneksi, $id_user, $id_laporan, $judul, $pesan, $kategori) {
    $id_lap = $id_laporan ? $id_laporan : "NULL";
    $sql = "INSERT INTO notifikasi (id_user, id_laporan, judul, pesan, kategori_notif, is_read) 
            VALUES ('$id_user', $id_lap, '$judul', '$pesan', '$kategori', '0')";
    mysqli_query($koneksi, $sql);
}

function kirim_notif_ke_admin_instansi($koneksi, $id_instansi, $id_laporan, $judul, $pesan, $kategori) {
    $query = mysqli_query($koneksi, "SELECT id_user FROM users WHERE role = 'admin' AND id_instansi = '$id_instansi'");
    while ($admin = mysqli_fetch_assoc($query)) {
        kirim_notif($koneksi, $admin['id_user'], $id_laporan, $judul, $pesan, $kategori);
    }
}
?>