<?php 
require 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Laporan – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

    <!-- NAVBAR -->
    <nav class="site-navbar">
        <a href="index.html" class="brand"><span>Lapor</span>Fasum</a>
        <nav>
            <a href="lapor.php" class="active">Buat Laporan</a>
            <a href="cek_status.php">Cek Status</a>
            <a href="login.html">Login Petugas</a>
        </nav>
    </nav>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1>Formulir Laporan Baru</h1>
        <p>Laporan Anda bersifat anonim. Isi data di bawah ini dengan lengkap dan benar.</p>
    </div>

    <!-- CONTENT -->
    <div class="page-body-narrow">
        <div class="card">
            <form action="proses_lapor.php" method="POST" enctype="multipart/form-data" onsubmit="return validasiForm()">

                <div class="form-group">
                    <label for="foto">1. Unggah Foto Kerusakan <small style="font-weight:400;color:#78909c;">(Maks. 5 MB)</small></label>
                    <input type="file" id="foto" name="foto" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="keluhan">2. Deskripsi Keluhan</label>
                    <textarea id="keluhan" name="keluhan" rows="3" placeholder="Contoh: Ada lubang besar di tengah jalan yang membahayakan..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="kategori">3. Jenis Fasilitas yang Bermasalah</label>
                    <select id="kategori" name="id_kategori" required>
                        <option value="" disabled selected>-- Pilih Jenis Fasilitas --</option>
                        <?php
                        $query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while($data = mysqli_fetch_assoc($query_kategori)){
                            echo "<option value='{$data['id_kategori']}'>{$data['nama_kategori']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>4. Titik Lokasi</label>
                    <p style="font-size:0.82rem;color:#78909c;margin-bottom:10px;">*Klik tombol GPS atau tandai manual pada peta.</p>
                    <button type="button" class="btn-gps" onclick="getLocation()">Gunakan Lokasi Saya Saat Ini</button>
                    <div id="map"></div>
                    <div class="koordinat-box">
                        <input type="text" id="latitude" name="latitude" placeholder="Latitude" readonly required>
                        <input type="text" id="longitude" name="longitude" placeholder="Longitude" readonly required>
                    </div>

                    <label for="alamat_manual" style="margin-top:10px;">5. Lokasi / Alamat <small style="font-weight:400;color:#78909c;">(Opsional)</small></label>
                    <textarea id="alamat_manual" name="alamat_manual" rows="2" placeholder="Contoh: Jalan Serayu V/08...."></textarea>
                </div>

                <button type="submit" class="btn">Kirim Laporan</button>
            </form>
        </div>

        <a href="index.html" class="back-link">← Kembali ke Halaman Utama</a>
    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([-8.5877, 116.0965], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker;

        map.on('click', function(e) {
            setMarker(e.latlng.lat, e.latlng.lng);
        });

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError, { enableHighAccuracy: true });
            } else {
                alert("GPS tidak didukung oleh browser Anda.");
            }
        }

        function showPosition(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            setMarker(lat, lng);
            map.setView([lat, lng], 16);
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("Anda menolak permintaan akses lokasi, silakan tandai peta secara manual.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Informasi lokasi tidak tersedia.");
                    break;
                case error.TIMEOUT:
                    alert("Waktu permintaan lokasi habis.");
                    break;
            }
        }

        function setMarker(lat, lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        function validasiForm() {
            var lat = document.getElementById('latitude').value;
            var lng = document.getElementById('longitude').value;
            var fotoInput = document.getElementById('foto');

            if (fotoInput.files.length > 0) {
                var ukuranFile = fotoInput.files[0].size;
                var batasUkuran = 5 * 1024 * 1024;
                if (ukuranFile > batasUkuran) {
                    alert("Gagal! Ukuran foto terlalu besar. Pastikan ukuran foto maksimal 5 MB.");
                    fotoInput.value = "";
                    return false;
                }
            }
            if (lat === "" || lng === "") {
                alert("Gagal! Anda belum menentukan titik lokasi.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>