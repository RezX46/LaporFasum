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

// Mengambil ID Instansi dari Admin yang sedang login
$id_instansi_admin = $_SESSION['id_instansi'];

$query = "SELECT l.*, k.nama_kategori 
          FROM laporan l 
          JOIN kategori k ON l.id_kategori = k.id_kategori 
          WHERE k.id_instansi = '$id_instansi_admin' 
          ORDER BY l.id_laporan DESC";
          
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="site-navbar">
        <a href="admin.php" class="brand"><span>Lapor</span>Fasum</a>
        <nav>
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

            <!-- Toolbar Search / Filter / Sort -->
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

    function getRows() {
        return Array.from(table.querySelectorAll('tbody tr'));
    }

    function applyAll() {
        const keyword = searchInput.value.toLowerCase().trim();
        const status  = filterSelect.value.toLowerCase();
        const sort    = sortSelect.value;

        let rows = getRows();

        // Filter & Search
        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            const matchSearch = !keyword || text.includes(keyword);
            const matchStatus = !status  || text.includes(status);
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });

        // Sort
        if (sort) {
            const tbody = table.querySelector('tbody');
            const visible = rows.filter(r => r.style.display !== 'none');
            visible.sort(function(a, b) {
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
            visible.forEach(function(r) { tbody.appendChild(r); });
        }

        // Empty message
        const anyVisible = rows.some(r => r.style.display !== 'none');
        emptyMsg.style.display = anyVisible ? 'none' : 'block';
    }

    searchInput.addEventListener('input', applyAll);
    filterSelect.addEventListener('change', applyAll);
    sortSelect.addEventListener('change', applyAll);
})();
</script>
    <?php if (isset($_SESSION['popup_notif'])): ?>
    <script>
        alert("<?= $_SESSION['popup_notif'] ?>");
    </script>
    <?php unset($_SESSION['popup_notif']); endif; ?>
</body>
</html>