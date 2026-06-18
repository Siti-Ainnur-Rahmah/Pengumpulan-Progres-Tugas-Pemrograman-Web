<?php
require_once 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Mengambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query mengambil data peminjaman spesifik beserta nama produknya
$query = "SELECT peminjaman.*, produk.nama AS nama_alat FROM peminjaman 
          LEFT JOIN produk ON peminjaman.id_produk = produk.id 
          WHERE peminjaman.id = $id";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$row = mysqli_fetch_assoc($result);
$files = array_filter(explode(',', $row['bukti_files']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - LabBorrow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-sky-50/50 min-h-screen p-6 md:p-10 text-gray-800">

    <div class="max-w-6xl mx-auto">
        <a href="index.php" class="inline-flex items-center space-x-2 text-sky-600 hover:text-sky-700 font-bold text-sm mb-6 transition">
            <i class="fa-solid fa-arrow-left"></i> <span>Kembali ke Dashboard</span>
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white rounded-2xl border border-sky-100 shadow-sm p-6 md:p-8">
                <h2 class="text-2xl font-black text-gray-800 tracking-tight border-b border-gray-100 pb-4 mb-6">Informasi Lengkap Pengajuan</h2>
                
                <div class="space-y-4 mb-8">
                    <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                        <span class="font-bold text-gray-500">Peminjam</span>
                        <span class="col-span-2 font-semibold text-gray-700">: <?= htmlspecialchars($row['nama_peminjam']) ?></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                        <span class="font-bold text-gray-500">NIM / Kelas</span>
                        <span class="col-span-2 font-medium text-gray-600">: <?= htmlspecialchars($row['nim']) ?> / <?= htmlspecialchars($row['kelas']) ?></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                        <span class="font-bold text-gray-500">Komponen Lab</span>
                        <span class="col-span-2 font-bold text-sky-600">: <?= htmlspecialchars($row['nama_alat'] ?? 'Alat Dihapus') ?></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                        <span class="font-bold text-gray-500">Tanggal Pinjam</span>
                        <span class="col-span-2 font-medium text-gray-600">: <?= date('d F Y', strtotime($row['tanggal_pinjam'])) ?></span>
                    </div>
                    <div class="grid grid-cols-3 py-1.5 text-sm">
                        <span class="font-bold text-gray-500">Keperluan</span>
                        <span class="col-span-2 font-medium text-gray-600">: <?= htmlspecialchars($row['keperluan']) ?></span>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4 text-sm uppercase tracking-wider text-sky-600">
                        <i class="fa-solid fa-images"></i> Berkas Lampiran:
                    </h3>
                    
                    <?php if(count($files) > 0): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <?php foreach($files as $file): 
                                $nama_file = trim($file);
                                // Menghapus teks bawaan "img/" atau "uploads/" jika tidak sengaja tersimpan di database
                                $nama_file = str_replace(['uploads/', 'img/'], '', $nama_file);
                                $path_gambar = "img/" . $nama_file;
                            ?>
                                <div class="group relative aspect-[4/3] rounded-xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm">
                                    <img src="<?= $path_gambar ?>" alt="Lampiran" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <a href="<?= $path_gambar ?>" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition duration-300">
                                        <i class="fa-solid fa-magnifying-glass-plus mr-1"></i> Lihat Penuh
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs italic text-gray-400 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200">Tidak ada berkas berkas lampiran yang diunggah.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-sky-100 shadow-sm p-6 text-center">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4">Tanda Tangan Digital</h3>
                    
                    <div class="bg-sky-50/50 border border-sky-100 rounded-xl p-2 flex justify-center items-center h-48 shadow-inner">
                        <?php if(!empty($row['tanda_tangan'])): ?>
                            <img src="<?= $row['tanda_tangan'] ?>" alt="Tanda Tangan" class="max-h-full max-w-full object-contain mix-blend-multiply">
                        <?php else: ?>
                            <span class="text-xs text-red-400 italic">Belum ditandatangani</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>