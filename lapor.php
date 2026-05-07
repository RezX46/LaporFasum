<?php 
require 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Laporan Fasilitas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            z-index: 1;
        }
        .koordinat-box {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .koordinat-box input {
            background-color: #e9ecef;
            cursor: not-allowed;
            width: 50%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select, textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: inherit;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .btn-gps {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
            width: 100%;
            font-weight: bold;
        }
        .btn-gps:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Formulir Laporan Baru</h1>
        <p style="margin-bottom: 20px; font-size: 0.9em;">Laporan Anda bersifat anonim. Silakan isi data di bawah ini.</p>
        
        <form action="proses_lapor.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="foto">1. Unggah Foto Kerusakan:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>
            </div>

            <div class="form-group">
                <label for="keluhan">2. Deskripsi Keluhan:</label>
                <textarea id="keluhan" name="keluhan" rows="3" placeholder="Contoh: Ada lubang besar yang membahayakan..." required></textarea>
            </div>

            <div class="form-group">
                <label for="kategori">3. Jenis Fasilitas yang Bermasalah:</label>
                <select id="kategori" name="id_kategori" required>
                    <option value="" disabled selected>-- Pilih Jenis Fasilitas --</option>
                    <?php
                    // Mengambil data kategori dari database
                    $query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                    while($data = mysqli_fetch_assoc($query_kategori)){
                        echo "<option value='{$data['id_kategori']}'>{$data['nama_kategori']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>4. Titik Lokasi :</label>
                <p style="font-size: 0.8em; color: #666; margin-bottom: 5px;">*Klik tombol GPS di bawah atau tandai manual pada peta.</p>
                
                <button type="button" class="btn-gps" onclick="getLocation()"> Gunakan Lokasi Saya Saat Ini </button>

                <div id="map"></div>
                
                <div class="koordinat-box">
                    <input type="text" id="latitude" name="latitude" placeholder="Latitude" readonly required>
                    <input type="text" id="longitude" name="longitude" placeholder="Longitude" readonly required>
                </div>

                <label for="alamat_manual" style="margin-top: 10px; display: block;">5. Lokasi / Alamat (Opsional):</label>
                <textarea id="alamat_manual" name="alamat_manual" rows="2" placeholder="Contoh: Jalan Serayu V/08...."></textarea>
            </div>

            <button type="submit" class="btn">Kirim Laporan</button>
        </form>

        <a href="index.html" class="back-link">← Kembali ke Halaman Utama</a>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // mataram
        var map = L.map('map').setView([-8.5877, 116.0965], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker;

        // Fungsi klik pada peta
        map.on('click', function(e) {
            setMarker(e.latlng.lat, e.latlng.lng);
        });

        // Fungsi Fitur GPS
        function getLocation() {
            if (navigator.geolocation) {
                // Meminta akses lokasi
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true
                });
            } else {
                alert("GPS tidak didukung oleh browser Anda.");
            }
        }

        function showPosition(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            setMarker(lat, lng);
            // Menggeser peta ke lokasi pengguna
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

        // Fungsi untuk menempatkan pin dan mengisi input
        function setMarker(lat, lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }
    </script>
</body>
</html>