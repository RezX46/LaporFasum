<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location.href = 'login.html';</script>";
    exit();
}
require 'koneksi.php';

$id_instansi_admin = $_SESSION['id_instansi'];

// Admin Pusat melihat semua akun kecuali dirinya sendiri
if ($id_instansi_admin == 1) {
    $query = "SELECT u.*, i.nama_instansi 
              FROM users u 
              JOIN instansi i ON u.id_instansi = i.id_instansi 
              WHERE u.id_instansi != 1
              ORDER BY i.nama_instansi ASC, u.role ASC, u.nama_lengkap ASC";
    $judul_halaman = "Manajemen Seluruh Personil";
} else {
    // Admin Dinas hanya melihat petugas di instansinya
    $query = "SELECT u.*, i.nama_instansi 
              FROM users u 
              JOIN instansi i ON u.id_instansi = i.id_instansi 
              WHERE u.role = 'petugas' AND u.id_instansi = '$id_instansi_admin'
              ORDER BY u.nama_lengkap ASC";
    $judul_halaman = "Daftar Petugas";
}

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= $judul_halaman ?> – LaporFasum</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
</head>

<body>
    <nav class="site-navbar">
        <a href="admin.php" class="brand"><span>Lapor</span>Fasum</a>
        <nav>
            <a href="admin.php">Dashboard</a>
            <a href="personil_tambah.php">+ Tambah Personil</a>
            <a href="logout.php" class="btn-logout">Keluar</a>
        </nav>
    </nav>

    <div class="page-header">
        <h1><?= $judul_halaman ?></h1>
        <p>Kelola data akun admin dan petugas di sistem LaporFasum.</p>
    </div>

    <div class="page-body" style="max-width:1100px;">
        <div class="card">
            <div class="card-title"><?= $judul_halaman ?></div>

            <!-- Toolbar Search / Filter / Sort -->
            <div class="table-toolbar">
                <div class="toolbar-search">
                    <span class="search-icon"></span>
                    <input type="text" id="personilSearch" placeholder="Cari nama, username, peran, instansi...">
                </div>
                <select class="toolbar-select" id="personilFilterPeran">
                    <option value="">Semua Peran</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
                <select class="toolbar-select" id="personilSort">
                    <option value="">Urutan Default</option>
                    <option value="nama-asc">Nama A-Z</option>
                    <option value="nama-desc">Nama Z-A</option>
                </select>
            </div>

            <div style="overflow-x:auto;">

                <table id="personilTable">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama & Username</th>
                            <th>Peran</th>
                            <?php if ($id_instansi_admin == 1): ?>
                                <th>Instansi</th>
                            <?php endif; ?>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Belum ada personil yang terdaftar.</td>
                            </tr>
                        <?php endif; ?>

                        <?php while ($user = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td style="width: 70px; text-align: center;">
                                    <?php
                                    $foto = !empty($user['foto_profil']) ? "uploads/profil/" . $user['foto_profil'] : "assets/img/default-user.png";
                                    ?>
                                    <img src="<?= $foto ?>" class="foto-profil-kecil" alt="Profil">
                                </td>
                                <td>
                                    <strong><?= $user['nama_lengkap'] ?></strong><br>
                                    <span class="text-username">@<?= $user['username'] ?></span>
                                </td>
                                <td>
                                    <?php if ($user['role'] == 'admin'): ?>
                                        <span class="role-badge role-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="role-badge role-petugas">Petugas</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($id_instansi_admin == 1): ?>
                                    <td style="font-size: 0.9em;"><?= $user['nama_instansi'] ?></td>
                                <?php endif; ?>
                                <td>
                                    <a href="personil_detail.php?id=<?= $user['id_user'] ?>" class="btn-detail">Lihat
                                        Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="margin-top: 16px;">
            <button type="button" onclick="history.back()" style="padding: 8px 18px; background: #f0f0f0; color: #333; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; cursor: pointer;">&#8592; Kembali</button>
        </div>
    </div>
    </div>


    <footer class="site-footer">&copy; 2025 <span>LaporFasum</span> &mdash; Sistem Pelaporan Fasilitas Umum</footer>

    <script>
        (function () {
            // Kolom: 0=Foto, 1=Nama&Username, 2=Peran, [3=Instansi jika admin pusat], Aksi terakhir
            var namaCol = 1;
            var peranCol = 2;

            const searchInput = document.getElementById('personilSearch');
            const filterSelect = document.getElementById('personilFilterPeran');
            const sortSelect = document.getElementById('personilSort');
            const table = document.getElementById('personilTable');

            // Buat elemen pesan kosong
            var emptyMsg = document.createElement('p');
            emptyMsg.className = 'toolbar-empty-msg';
            emptyMsg.id = 'personilEmptyMsg';
            emptyMsg.innerHTML = 'Tidak ada personil yang cocok dengan pencarian/filter Anda.';
            table.parentNode.insertBefore(emptyMsg, table.nextSibling);

            function getRows() {
                return Array.from(table.querySelectorAll('tbody tr'));
            }

            function applyAll() {
                const keyword = searchInput.value.toLowerCase().trim();
                const peran = filterSelect.value.toLowerCase();
                const sort = sortSelect.value;

                let rows = getRows();

                rows.forEach(function (row) {
                    const text = row.textContent.toLowerCase();
                    const matchSearch = !keyword || text.includes(keyword);
                    const matchPeran = !peran || text.includes(peran);
                    row.style.display = (matchSearch && matchPeran) ? '' : 'none';
                });

                if (sort) {
                    const tbody = table.querySelector('tbody');
                    const visible = rows.filter(r => r.style.display !== 'none');
                    visible.sort(function (a, b) {
                        const cells_a = a.querySelectorAll('td');
                        const cells_b = b.querySelectorAll('td');
                        if (sort === 'nama-asc') return cells_a[namaCol].textContent.trim().localeCompare(cells_b[namaCol].textContent.trim());
                        if (sort === 'nama-desc') return cells_b[namaCol].textContent.trim().localeCompare(cells_a[namaCol].textContent.trim());
                        if (sort === 'peran-asc') return cells_a[peranCol].textContent.trim().localeCompare(cells_b[peranCol].textContent.trim());
                        if (sort === 'peran-desc') return cells_b[peranCol].textContent.trim().localeCompare(cells_a[peranCol].textContent.trim());
                        return 0;
                    });
                    visible.forEach(function (r) { tbody.appendChild(r); });
                }

                const anyVisible = rows.some(r => r.style.display !== 'none');
                emptyMsg.style.display = anyVisible ? 'none' : 'block';
            }

            searchInput.addEventListener('input', applyAll);
            filterSelect.addEventListener('change', applyAll);
            sortSelect.addEventListener('change', applyAll);
        })();
    </script>
</body>

</html>