<?php
/**
 * Fungsi untuk mengompres dan meresize gambar menggunakan GD Library
 * @param string $source_path File sumber (biasanya tmp_name dari $_FILES)
 * @param string $destination_path Lokasi dan nama file tujuan disimpan
 * @param int $max_dimensi Batas resolusi maksimal (Dinaikkan menjadi 2560px untuk mempertahankan kualitas tinggi)
 * @param int $kualitas Tingkat kualitas kompresi JPEG (Dinaikkan menjadi 85 untuk ukuran file maksimal ~3MB)
 * @return bool Mengembalikan true jika berhasil, false jika gagal
 */
function kompres_dan_resize_gambar($source_path, $destination_path, $max_dimensi = 2560, $kualitas = 85) {
    $info_citra = getimagesize($source_path);
    
    if (!$info_citra) {
        return false;
    }

    $mime = $info_citra['mime'];
    $citra_asli = null;

    // Membaca citra berdasarkan MIME type
    if ($mime == 'image/jpeg') {
        $citra_asli = imagecreatefromjpeg($source_path);
    } elseif ($mime == 'image/png') {
        $citra_asli = imagecreatefrompng($source_path);
        
        // Menghapus kanal transparansi (alpha) pada PNG menjadi latar putih
        $bg_putih = imagecreatetruecolor(imagesx($citra_asli), imagesy($citra_asli));
        $warna_putih = imagecolorallocate($bg_putih, 255, 255, 255);
        imagefill($bg_putih, 0, 0, $warna_putih);
        imagecopy($bg_putih, $citra_asli, 0, 0, 0, 0, imagesx($citra_asli), imagesy($citra_asli));
        imagedestroy($citra_asli);
        $citra_asli = $bg_putih;
    }

    if ($citra_asli !== null) {
        $lebar_asli = imagesx($citra_asli);
        $tinggi_asli = imagesy($citra_asli);

        // Kalkulasi ukuran baru jika melebihi batas dimensi
        if ($lebar_asli > $max_dimensi || $tinggi_asli > $max_dimensi) {
            if ($lebar_asli > $tinggi_asli) {
                $lebar_baru = $max_dimensi;
                $tinggi_baru = floor($tinggi_asli * ($max_dimensi / $lebar_asli));
            } else {
                $tinggi_baru = $max_dimensi;
                $lebar_baru = floor($lebar_asli * ($max_dimensi / $tinggi_asli));
            }
            
            $citra_baru = imagecreatetruecolor($lebar_baru, $tinggi_baru);
            imagecopyresampled($citra_baru, $citra_asli, 0, 0, 0, 0, $lebar_baru, $tinggi_baru, $lebar_asli, $tinggi_asli);
            imagedestroy($citra_asli);
            $citra_asli = $citra_baru;
        }

        // Menyimpan citra
        $hasil = imagejpeg($citra_asli, $destination_path, $kualitas);
        imagedestroy($citra_asli);
        return $hasil;
    }

    // Jika format bukan JPG/PNG yang didukung, coba pindahkan file mentahnya saja
    return move_uploaded_file($source_path, $destination_path);
}
?>