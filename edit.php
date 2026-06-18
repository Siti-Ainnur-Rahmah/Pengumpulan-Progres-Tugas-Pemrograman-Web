<?php
require_once 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id = $id");
$data = mysqli_fetch_assoc($result);

if (!$data) { header("Location: index.php"); exit; }

$produk_query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama ASC");

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $id_produk = mysqli_real_escape_string($koneksi, $_POST['id_produk']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
    
    $tanda_tangan = !empty($_POST['tanda_tangan']) ? $_POST['tanda_tangan'] : $data['tanda_tangan'];
    $bukti_files_str = $data['bukti_files'];

    if (!empty($_FILES['gambar']['name'][0])) {
        $uploaded = [];
        foreach ($_FILES['gambar']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['gambar']['name'][$key];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
            if (in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($tmp_name, "img/" . $new_file_name)) { $uploaded[] = $new_file_name; }
            }
        }
        if(count($uploaded) > 0) {
            $bukti_files_str = !empty($data['bukti_files']) ? $data['bukti_files'] . ',' . implode(',', $uploaded) : implode(',', $uploaded);
        }
    }

    $update_query = "UPDATE peminjaman SET nama_peminjam='$nama', nim='$nim', kelas='$kelas', id_produk='$id_produk', tanggal_pinjam='$tanggal', keperluan='$keperluan', bukti_files='$bukti_files_str', tanda_tangan='$tanda_tangan' WHERE id=$id";
    if (mysqli_query($koneksi, $update_query)) { header("Location: index.php"); exit; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Peminjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white border rounded-xl p-6 shadow-sm">
        <h2 class="text-xl font-bold mb-4">Edit Transaksi</h2>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="prepareSubmit()">
            <div class="space-y-4">
                <div><label class="block text-xs font-bold mb-1">Nama Peminjam</label><input type="text" name="nama" value="<?= htmlspecialchars($data['nama_peminjam']) ?>" required class="border rounded-lg w-full py-2 px-3"></div>
                <div><label class="block text-xs font-bold mb-1">NIM</label><input type="text" name="nim" value="<?= htmlspecialchars($data['nim']) ?>" required class="border rounded-lg w-full py-2 px-3"></div>
                <div><label class="block text-xs font-bold mb-1">Kelas</label><input type="text" name="kelas" value="<?= htmlspecialchars($data['kelas']) ?>" required class="border rounded-lg w-full py-2 px-3"></div>
                <div>
                    <label class="block text-xs font-bold mb-1">Alat Lab</label>
                    <select name="id_produk" class="border rounded-lg w-full py-2 px-3">
                        <?php mysqli_data_seek($produk_query, 0); while($prod = mysqli_fetch_assoc($produk_query)): ?>
                            <option value="<?= $prod['id'] ?>" <?= $prod['id'] == $data['id_produk'] ? 'selected' : '' ?>><?= htmlspecialchars($prod['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div><label class="block text-xs font-bold mb-1">Tanggal</label><input type="date" name="tanggal" value="<?= $data['tanggal_pinjam'] ?>" class="border rounded-lg w-full py-2 px-3"></div>
                <div><label class="block text-xs font-bold mb-1">Tambah Lampiran Berkas Gambar Baru</label><input type="file" name="gambar[]" multiple class="border rounded-lg w-full py-1.5 px-3"></div>
                <div><label class="block text-xs font-bold mb-1">Keperluan</label><textarea name="keperluan" rows="3" class="border rounded-lg w-full py-2 px-3"><?= htmlspecialchars($data['keperluan']) ?></textarea></div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold mb-1">TTD Saat Ini</label><div class="border rounded p-2 bg-gray-50 h-28 flex justify-center"><img src="<?= $data['tanda_tangan'] ?>" class="max-h-full"></div></div>
                    <div>
                        <label class="block text-xs font-bold mb-1">Ganti TTD Baru (Opsional)</label>
                        <div class="border rounded p-2 bg-white flex flex-col items-center">
                            <canvas id="signatureCanvas" style="width:100%; height:80px; background:#f9fafb;" class="border rounded"></canvas>
                            <button type="button" onclick="clearSig()" class="text-[10px] bg-gray-400 text-white px-2 py-0.5 mt-1 rounded">Clear</button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="tanda_tangan" id="signatureData">
            <button type="submit" name="update" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 mt-6 rounded-lg shadow">Perbarui Data</button>
        </form>
    </div>
    <script>
        const canvas = document.getElementById('signatureCanvas'); const ctx = canvas.getContext('2d'); let draw = false, active = false;
        function init() { canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight; ctx.strokeStyle = '#1e3a8a'; ctx.lineWidth = 2.5; }
        window.addEventListener('load', init);
        function pos(e) { const r = canvas.getBoundingClientRect(); return { x: (e.touches ? e.touches[0].clientX : e.clientX) - r.left, y: (e.touches ? e.touches[0].clientY : e.clientY) - r.top }; }
        canvas.addEventListener('mousedown', (e) => { draw = true; active = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove', (e) => { if(!draw) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup', () => draw = false);
        canvas.addEventListener('touchstart', (e) => { draw = true; active = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('touchmove', (e) => { if(!draw) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('touchend', () => draw = false);
        function clearSig() { ctx.clearRect(0,0,canvas.width,canvas.height); active = false; }
        function prepareSubmit() { if(active) document.getElementById('signatureData').value = canvas.toDataURL(); }
    </script>
</body>
</html>