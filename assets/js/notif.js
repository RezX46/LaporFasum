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
    var badge = document.getElementById('notifBadgeCount');
    if (badge) {
        if (isReset) {
            badge.innerText = '0';
        } else {
            var current = parseInt(badge.innerText);
            if (current > 0) badge.innerText = current - 1;
        }
    }
}

function cekNotifKosong() {
    var container = document.getElementById('notifContainer');
    if (container && container.querySelectorAll('.notif-wrapper').length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 30px;">Tidak ada pesan baru.</p>';
        var btnBersih = document.getElementById('btnBersihkanSemua');
        if(btnBersih) btnBersih.style.display = 'none';
    }
}