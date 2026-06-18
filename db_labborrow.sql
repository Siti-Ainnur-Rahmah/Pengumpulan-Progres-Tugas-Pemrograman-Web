CREATE DATABASE IF NOT EXISTS db_labborrow;
USE db_labborrow;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users(username,password)
VALUES('admin','admin123');

CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_peminjam VARCHAR(100),
    barang VARCHAR(100),
    jumlah INT,
    tanggal_pinjam DATE,
    tanggal_kembali DATE
);