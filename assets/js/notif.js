function bukaNotif() {
    var modal = document.getElementById("notifModal");
    if (modal) { modal.style.display = "block"; }
}

function tutupNotif() {
    var modal = document.getElementById("notifModal");
    if (modal) { modal.style.display = "none"; }
}

window.onclick = function(event) {
    var modal = document.getElementById("notifModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.addEventListener('click', function(e) {
    var link = e.target.closest('.notif-item-link');
    if (link && link.classList.contains('unread')) {
        var id_notifikasi = link.getAttribute('data-id');
        if (id_notifikasi) {
            // Kita biarkan navigasi default berjalan, tapi panggil fetch di background
            fetch('proses_hapus_notif.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'aksi=baca_satu&id_notifikasi=' + id_notifikasi
            });
            link.classList.remove('unread');
            updateBadgeNotif();
        }
    }
});

function hapusNotif(id_notifikasi, element) {
    fetch('proses_hapus_notif.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'aksi=hapus_satu&id_notifikasi=' + id_notifikasi
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // Hapus elemen notifikasi dari layar secara halus
            element.style.opacity = '0';
            setTimeout(() => {
                element.remove();
                updateBadgeNotif();
                cekNotifKosong();
            }, 200);
        }
    })
    .catch(error => console.error('Error:', error));
}

function hapusSemuaNotif() {
    if(confirm('Yakin ingin menghapus semua notifikasi?')) {
        fetch('proses_hapus_notif.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'aksi=hapus_semua'
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Bersihkan panel notifikasi
                var container = document.getElementById('notifContainer');
                if (container) {
                    container.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 30px;">Tidak ada pesan baru.</p>';
                }
                // Sembunyikan tombol "Bersihkan Semua"
                var btnBersih = document.getElementById('btnBersihkanSemua');
                if(btnBersih) btnBersih.style.display = 'none';
                
                updateBadgeNotif(true); // Reset angka jadi 0
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function updateBadgeNotif(isReset = false) {
    // 
}

function cekNotifKosong() {
    var container = document.getElementById('notifContainer');
    if (container && container.querySelectorAll('.notif-wrapper').length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 30px;">Tidak ada pesan baru.</p>';
        var btnBersih = document.getElementById('btnBersihkanSemua');
        if(btnBersih) btnBersih.style.display = 'none';
    }
}

// Variabel untuk melacak jumlah notifikasi (agar tidak perlu ambil dari DOM)
var currentNotifCount = -1;

// Auto-polling notifikasi baru setiap 10 detik
setInterval(function() {
    fetch('cek_notif_baru.php')
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            var newJml = parseInt(data.jumlah) || 0;
            
            // Jika ada perubahan jumlah notifikasi, perbarui isi modal
            if (currentNotifCount !== newJml) {
                currentNotifCount = newJml;
                var container = document.getElementById('notifContainer');
                if (container && data.html) {
                    container.innerHTML = data.html;
                }
                var btnBersih = document.getElementById('btnBersihkanSemua');
                if (btnBersih) {
                    btnBersih.style.display = data.html.includes('notif-wrapper') ? 'inline-block' : 'none';
                }
            }
        }
    })
    .catch(err => console.error('Error fetching new notif:', err));
}, 10000);