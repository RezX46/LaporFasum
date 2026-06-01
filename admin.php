<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>
            alert('Akses Ditolak! Anda harus login sebagai Admin.');
            window.location.href = 'login.html';
          </script>";
    exit();
}
require 'koneksi.php';

$id_instansi_admin = $_SESSION['id_instansi'];
$id_admin_aktif = $_SESSION['id_user'];

$query = "SELECT l.*, k.nama_kategori 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE k.id_instansi = '$id_instansi_admin' 
          ORDER BY l.id_laporan DESC";
          
$result = mysqli_query($koneksi, $query);

$query_notif = mysqli_query($koneksi, "SELECT * FROM notifikasi WHERE id_user = '$id_admin_aktif' ORDER BY tanggal DESC LIMIT 15");
$jumlah_notif = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM notifikasi WHERE id_user = '$id_admin_aktif' AND is_read = '0'");
$jml_notif = mysqli_fetch_assoc($jumlah_notif)['jml'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>
<body>

    <nav class="site-navbar">
        <a href="admin.php" class="brand"><span>Lapor</span>Fasum</a>
        <nav>
            <button class="btn-notif" onclick="bukaNotif()">Notifikasi (<span id="notifBadgeCount"><?= $jml_notif ?></span>)</button>
            <a href="personil.php">Manajemen Personil</a>
            <a href="pengaturan_akun.php">Pengaturan Akun</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </nav>
    </nav>

    <div class="page-header">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <strong><?= $_SESSION['nama_lengkap'] ?></strong></p>
    </div>

    <div class="page-body" style="max-width:1100px;">
        <div class="card">
            <div class="card-title">Daftar Laporan Masuk</div>

            <div class="table-toolbar">
                <div class="toolbar-search">
                    <span class="search-icon"></span>
                    <input type="text" id="adminSearch" placeholder="Cari ID, kategori, status...">
                </div>
                <select class="toolbar-select" id="adminFilterStatus">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="diproses">Diproses</option>
                    <option value="menunggu verifikasi">Menunggu Verifikasi</option>
                    <option value="selesai">Selesai</option>
                    <option value="ditolak">Ditolak</option>
                </select>
                <select class="toolbar-select" id="adminSort">
                    <option value="">Urutan Default</option>
                    <option value="id-desc">ID Terbaru</option>
                    <option value="id-asc">ID Terlama</option>
                    <option value="tanggal-desc">Tanggal Terbaru</option>
                    <option value="tanggal-asc">Tanggal Terlama</option>
                    <option value="kategori-asc">Kategori A-Z</option>
                    <option value="kategori-desc">Kategori Z-A</option>
                </select>
            </div>

            <div style="overflow-x:auto;">
            <table id="adminTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='5' style='text-align:center;padding:30px;color:#78909c;'>Belum ada laporan masuk untuk instansi Anda.</td></tr>";
                    }
                    while($row = mysqli_fetch_assoc($result)) { 
                        $badge_class = 'badge-kuning'; 
                        if ($row['status'] == 'diproses') { $badge_class = 'badge-biru'; }
                        elseif ($row['status'] == 'menunggu verifikasi') { $badge_class = 'badge-oranye'; }
                        elseif ($row['status'] == 'selesai') { $badge_class = 'badge-hijau'; }
                        elseif ($row['status'] == 'ditolak') { $badge_class = 'badge-merah'; }
                    ?>
                    <tr>
                        <td>#<?= $row['id_laporan'] ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_lapor'])) ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span></td>
                        <td>
                            <a href="admin_detail.php?id=<?= $row['id_laporan'] ?>" class="btn-detail">Lihat Detail</a>
                            <?php if ($row['status'] == 'ditolak'): ?>
                                <a href="hapus.php?id=<?= $row['id_laporan'] ?>" onclick="return confirm('Yakin hapus permanen?');" class="btn-hapus">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <p class="toolbar-empty-msg" id="adminEmptyMsg">Tidak ada data yang cocok dengan pencarian/filter Anda.</p>
            </div>
        </div>
    </div>

    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>

<script>
(function(){
    const searchInput  = document.getElementById('adminSearch');
    const filterSelect = document.getElementById('adminFilterStatus');
    const sortSelect   = document.getElementById('adminSort');
    const table        = document.getElementById('adminTable');
    const emptyMsg     = document.getElementById('adminEmptyMsg');

    const initialRows = Array.from(table.querySelectorAll('tbody tr'));

    function applyAll() {
        const keyword = searchInput.value.toLowerCase().trim();
        const status  = filterSelect.value.toLowerCase();
        const sort    = sortSelect.value;
        let rows = initialRows.slice();

        if (sort) {
            rows.sort(function(a, b) {
                const cells_a = a.querySelectorAll('td');
                const cells_b = b.querySelectorAll('td');
                if (sort === 'id-asc')          return parseInt(cells_a[0].textContent.replace('#','')) - parseInt(cells_b[0].textContent.replace('#',''));
                if (sort === 'id-desc')         return parseInt(cells_b[0].textContent.replace('#','')) - parseInt(cells_a[0].textContent.replace('#',''));
                if (sort === 'tanggal-asc')     return cells_a[1].textContent.localeCompare(cells_b[1].textContent);
                if (sort === 'tanggal-desc')    return cells_b[1].textContent.localeCompare(cells_a[1].textContent);
                if (sort === 'kategori-asc')    return cells_a[2].textContent.trim().localeCompare(cells_b[2].textContent.trim());
                if (sort === 'kategori-desc')   return cells_b[2].textContent.trim().localeCompare(cells_a[2].textContent.trim());
                return 0;
            });
        }

        const tbody = table.querySelector('tbody');
        let anyVisible = false;

        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            const matchSearch = !keyword || text.includes(keyword);
            const matchStatus = !status  || text.includes(status);
            
            if (matchSearch && matchStatus) {
                row.style.display = '';
                anyVisible = true;
            } else {
                row.style.display = 'none';
            }
            tbody.appendChild(row);
        });

        emptyMsg.style.display = anyVisible ? 'none' : 'block';
    }

    searchInput.addEventListener('input', applyAll);
    filterSelect.addEventListener('change', applyAll);
    sortSelect.addEventListener('change', applyAll);
})();
</script>

    <div id="notifModal" class="modal">
        <div class="modal-content" style="padding: 0;"> 
            <div style="padding: 20px 24px; border-bottom: 2px solid #f39c12; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: #fff; border-radius: 12px 12px 0 0; z-index: 10;">
                <h2 style="margin: 0; color: #34495e; font-size: 1.2rem;">Pusat Notifikasi Admin</h2>
                <div class="notif-header-actions">
                    <?php if (mysqli_num_rows($query_notif) > 0): ?>
                        <button type="button" id="btnBersihkanSemua" class="btn-bersihkan-notif" onclick="hapusSemuaNotif()">Bersihkan Semua</button>
                    <?php endif; ?>
                    <span class="close-btn" onclick="tutupNotif()" style="margin: 0; line-height: 1;">&times;</span>
                </div>
            </div>
            
            <div id="notifContainer" style="max-height: 60vh; overflow-y: auto; padding: 0;">
                <?php if (mysqli_num_rows($query_notif) == 0): ?>
                    <p style="text-align: center; color: #7f8c8d; padding: 30px;">Belum ada notifikasi.</p>
                <?php else: ?>
                    <?php while ($n = mysqli_fetch_assoc($query_notif)): 
                        
                        $url = "#";
                        $onclick = "";
                        
                        if ($n['kategori_notif'] == 'akun_pengajuan') {
                            $id_petugas_pengaju = $n['id_laporan']; 
                            
                            if (empty($id_petugas_pengaju)) {
                                $url = "javascript:void(0)";
                                $onclick = "onclick=\"alert('Ini adalah notifikasi versi lama (Data ID kosong). Silakan cek menu Manajemen Personil secara manual.'); return false;\"";
                            } else {
                                $cek_user = mysqli_query($koneksi, "SELECT pending_nama FROM users WHERE id_user = '$id_petugas_pengaju'");
                                $data_user = mysqli_fetch_assoc($cek_user);
                                
                                if (!$data_user || empty($data_user['pending_nama'])) {
                                    $url = "javascript:void(0)";
                                    $onclick = "onclick=\"alert('Pengajuan perubahan data diri ini sudah Anda setujui atau tolak sebelumnya.'); return false;\"";
                                } else {
                                    $url = "personil_detail.php?id=" . $id_petugas_pengaju;
                                }
                            }
                            
                        } else {
                            $id_lap = $n['id_laporan'];
                            $cek_lap = mysqli_query($koneksi, "SELECT k.id_instansi FROM laporan l JOIN kategori k ON l.id_kategori = k.id_kategori WHERE l.id_laporan = '$id_lap'");
                            $data_lap = mysqli_fetch_assoc($cek_lap);

                            if (!$data_lap) {
                                $url = "javascript:void(0)";
                                $onclick = "onclick=\"alert('Laporan ini sudah tidak tersedia atau telah dihapus permanen.'); return false;\"";
                            } elseif ($data_lap['id_instansi'] != $id_instansi_admin) {
                                $url = "javascript:void(0)";
                                $onclick = "onclick=\"alert('Akses Ditolak! Laporan ini telah diteruskan ke instansi lain atau diserahkan ke pusat.'); return false;\"";
                            } else {
                                $url = "admin_detail.php?id=" . $n['id_laporan'];
                            }
                        }
                    ?>
                        <div class="notif-wrapper" style="transition: opacity 0.2s;">
                            <a href="<?= $url ?>" <?= $onclick ?> data-id="<?= $n['id_notifikasi'] ?>" class="notif-item-link <?= $n['is_read'] == '0' ? 'unread' : '' ?>">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span class="notif-title"><?= htmlspecialchars($n['judul']) ?></span>
                                    <span class="notif-time"><?= date('d M, H:i', strtotime($n['tanggal'])) ?></span>
                                </div>
                                <p class="notif-msg" style="color: #555; font-style: normal; margin-top: 4px;"><?= htmlspecialchars($n['pesan']) ?></p>
                            </a>
                            <button type="button" class="btn-hapus-notif-single" title="Hapus Notifikasi" onclick="hapusNotif(<?= $n['id_notifikasi'] ?>, this.closest('.notif-wrapper'))">&times;</button>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="assets/js/notif.js?v=<?= time(); ?>"></script>

    <?php if (isset($_SESSION['popup_notif'])): ?>
    <script>
        alert("<?= $_SESSION['popup_notif'] ?>");
    </script>
    <?php unset($_SESSION['popup_notif']); endif; ?>
</body>
</html>