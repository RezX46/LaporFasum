<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada sesi aktif']);
    exit();
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];
$id_instansi_admin = $_SESSION['id_instansi'] ?? 0;

// Ambil jumlah notif belum dibaca
$q_jml = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM notifikasi WHERE id_user = '$id_user' AND is_read = '0'");
$jml_notif = 0;
if ($q_jml) {
    $jml_notif = mysqli_fetch_assoc($q_jml)['jml'];
}

// Ambil daftar notifikasi
$query_notif = mysqli_query($koneksi, "SELECT * FROM notifikasi WHERE id_user = '$id_user' ORDER BY tanggal DESC LIMIT 15");

ob_start();
if (mysqli_num_rows($query_notif) == 0) {
    echo '<p style="text-align: center; color: #7f8c8d; padding: 30px;">Belum ada notifikasi.</p>';
} else {
    while ($n = mysqli_fetch_assoc($query_notif)) {
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
        } elseif ($n['kategori_notif'] == 'akun_disetujui' || $n['kategori_notif'] == 'akun_ditolak') {
            $url = "pengaturan_akun.php";
        } else {
            $id_lap = $n['id_laporan'];
            if (empty($id_lap)) {
                $url = "javascript:void(0)";
                $onclick = "onclick=\"alert('Data laporan tidak valid.'); return false;\"";
            } else {
                if ($role == 'admin') {
                    $cek_lap = mysqli_query($koneksi, "SELECT k.id_instansi FROM laporan l JOIN kategori k ON l.id_kategori = k.id_kategori WHERE l.id_laporan = '$id_lap'");
                    $data_lap = mysqli_fetch_assoc($cek_lap);
                    if (!$data_lap) {
                        $url = "javascript:void(0)";
                        $onclick = "onclick=\"alert('Laporan ini sudah tidak tersedia atau telah dihapus permanen.'); return false;\"";
                    } elseif ($data_lap['id_instansi'] != $id_instansi_admin) {
                        $url = "javascript:void(0)";
                        $onclick = "onclick=\"alert('Akses Ditolak! Laporan ini telah diteruskan ke instansi lain atau diserahkan ke pusat.'); return false;\"";
                    } else {
                        $url = "admin_detail.php?id=" . $id_lap;
                    }
                } else {
                    $cek_lap = mysqli_query($koneksi, "SELECT id_petugas FROM laporan WHERE id_laporan = '$id_lap'");
                    $data_lap = mysqli_fetch_assoc($cek_lap);
                    if (!$data_lap) {
                        $url = "javascript:void(0)";
                        $onclick = "onclick=\"alert('Laporan ini sudah tidak tersedia atau telah dihapus permanen.'); return false;\"";
                    } elseif ($data_lap['id_petugas'] != $id_user) {
                        $url = "javascript:void(0)";
                        $onclick = "onclick=\"alert('Akses Ditolak! Tugas ini telah ditarik atau dialihkan ke petugas lain.'); return false;\"";
                    } else {
                        $url = "petugas_detail.php?id=" . $id_lap;
                    }
                }
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
        <?php
    }
}
$html = ob_get_clean();

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'jumlah' => $jml_notif,
    'html' => $html
]);
?>
