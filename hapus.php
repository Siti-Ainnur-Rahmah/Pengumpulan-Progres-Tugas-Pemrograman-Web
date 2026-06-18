<?php
require_once 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Bersihkan file fisik yang tersimpan di dalam folder img/ Laragon
    $result = mysqli_query($koneksi, "SELECT bukti_files FROM peminjaman WHERE id = $id");
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $files = array_filter(explode(',', $data['bukti_files']));
        foreach ($files as $file) {
            $filepath = "img/" . $file;
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }

    // Hapus records transaksi dari database
    mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id = $id");
}

header("Location: index.php");
exit;
?>