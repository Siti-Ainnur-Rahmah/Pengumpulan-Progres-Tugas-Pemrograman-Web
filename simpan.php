<?php
include 'koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_produk = $_POST['id_produk'];
    $nama = $_POST['nama_mahasiswa'];
    $ttd = $_POST['tanda_tangan_base64'];
    
    // Pastikan folder img ada
    if (!is_dir('img')) mkdir('img', 0777, true);
    
    // Sederhanakan query
    $query = "INSERT INTO peminjaman (id_produk, nama_peminjam, tanda_tangan) VALUES ('$id_produk', '$nama', '$ttd')";
    
    if(mysqli_query($koneksi, $query)){
        header("Location: index.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>