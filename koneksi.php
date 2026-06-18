<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "db_labborrow";

// Melakukan koneksi langsung ke database db_labborrow di phpMyAdmin
$koneksi = mysqli_connect($host, $user, $pass, $db_name);

// Periksa apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi ke database gagal! Periksa phpMyAdmin Anda. Error: " . mysqli_connect_error());
}

// Mengatur charset ke utf8mb4 agar mendukung penyimpanan karakter secara optimal
mysqli_set_charset($koneksi, "utf8mb4");
?>