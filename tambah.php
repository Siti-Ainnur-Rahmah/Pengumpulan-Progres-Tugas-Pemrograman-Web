<?php
require_once 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$produk_query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama ASC");

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $id_produk = mysqli_real_escape_string($koneksi, $_POST['id_produk']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
    $tanda_tangan = $_POST['tanda_tangan'];

    $target_dir = "img/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $uploaded_images = [];
    if (!empty($_FILES['gambar']['name'][0])) {
        foreach ($_FILES['gambar']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['gambar']['name'][$key];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
            
            if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($tmp_name, $target_dir . $new_file_name)) {
                    $uploaded_images[] = $new_file_name;
                }
            }
        }
    }

    $bukti_files_str = implode(',', $uploaded_images);

    $query = "INSERT INTO peminjaman (id_produk, nama_peminjam, nim, kelas, tanggal_pinjam, keperluan, bukti_files, tanda_tangan) 
              VALUES ('$id_produk', '$nama', '$nim', '$kelas', '$tanggal', '$keperluan', '$bukti_files_str', '$tanda_tangan')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Baru - LabBorrow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

    <nav class="bg-white border-b px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="text-blue-600 hover:text-blue-800 font-bold text-sm"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
        <span class="text-lg font-bold">Form Transaksi Baru</span>
        <div></div>
    </nav>

    <div class="max-w-4xl mx-auto p-6 md:p-8">
        <div class="bg-white rounded-xl shadow border p-6 md:p-8">
            <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama" required class="border rounded-lg w-full py-2 px-3 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">NIM *</label>
                        <input type="text" name="nim" required class="border rounded-lg w-full py-2 px-3 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Kelas *</label>
                        <input type="text" name="kelas" placeholder="Misal: TI-4B" required class="border rounded-lg w-full py-2 px-3 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Pilih Komponen/Alat Lab *</label>
                        <select name="id_produk" required class="border rounded-lg w-full py-2 px-3 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            <?php while($prod = mysqli_fetch_assoc($produk_query)): ?>
                                <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Tanggal Pinjam *</label>
                        <input type="date" name="tanggal" required class="border rounded-lg w-full py-2 px-3 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-xs font-bold mb-1">Upload File Lampiran Gambar *</label>
                        <input type="file" name="gambar[]" id="fileImages" multiple required accept="image/*" class="border rounded-lg w-full py-1.5 px-2 text-sm">
                        <div id="previewContainer" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Tujuan / Keperluan *</label>
                    <textarea name="keperluan" rows="3" required class="border rounded-lg w-full py-2 px-3 focus:outline-none"></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Gores Tanda Tangan Digital *</label>
                    <div class="border rounded-lg p-3 bg-gray-50 flex flex-col items-center">
                        <canvas id="signatureCanvas" class="border bg-white rounded shadow-inner" style="width: 100%; max-width: 500px; height: 160px; touch-action: none;"></canvas>
                        <button type="button" onclick="clearSignature()" class="mt-2 text-xs bg-gray-500 text-white font-bold py-1 px-3 rounded"><i class="fa-solid fa-eraser"></i> Bersihkan Canvas</button>
                    </div>
                </div>

                <input type="hidden" name="tanda_tangan" id="signatureData">
                <button type="submit" name="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition">Kirim Pengajuan</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('fileImages').addEventListener('change', function() {
            const container = document.getElementById('previewContainer'); container.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img'); img.src = e.target.result;
                    img.className = 'w-14 h-14 object-cover rounded border shadow-sm'; container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        });

        const canvas = document.getElementById('signatureCanvas'); const ctx = canvas.getContext('2d');
        let drawing = false;
        function resize() { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; ctx.strokeStyle = '#1d4ed8'; ctx.lineWidth = 3; ctx.lineCap = 'round'; }
        window.addEventListener('load', resize); window.addEventListener('resize', resize);

        function getPos(e) { const r = canvas.getBoundingClientRect(); if(e.touches) return {x: e.touches[0].clientX - r.left, y: e.touches[0].clientY - r.top}; return {x: e.clientX - r.left, y: e.clientY - r.top}; }
        canvas.addEventListener('mousedown', (e) => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove', (e) => { if(!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('touchmove', (e) => { e.preventDefault(); if(!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('touchend', () => drawing = false);

        function clearSignature() { ctx.clearRect(0, 0, canvas.width, canvas.height); }
        function validateForm() {
            const blank = document.createElement('canvas'); blank.width = canvas.width; blank.height = canvas.height;
            if(canvas.toDataURL() === blank.toDataURL()) { alert('Tanda tangan wajib diisi!'); return false; }
            document.getElementById('signatureData').value = canvas.toDataURL(); return true;
        }
    </script>
</body>
</html>