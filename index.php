<?php
require_once 'koneksi.php';
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$query = "SELECT peminjaman.*, produk.nama AS nama_alat FROM peminjaman 
          LEFT JOIN produk ON peminjaman.id_produk = produk.id ORDER BY peminjaman.id DESC";
$result = mysqli_query($koneksi, $query);
$total_produk_res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
$total_produk = mysqli_fetch_assoc($total_produk_res)['total'];

// Logika penentuan halaman aktif di dashboard
$page = isset($_GET['p']) ? $_GET['p'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard LabBorrow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <style>
        /* Penyesuaian warna tombol export DataTables agar serasi dengan tema biru muda */
        .dt-buttons .dt-button {
            background: #e0f2fe !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
        }
        .dt-buttons .dt-button:hover {
            background: #bae6fd !important;
        }
    </style>
</head>
<body class="bg-sky-50/50 min-h-screen flex flex-col text-gray-800">

    <nav class="bg-white border-b border-sky-100 sticky top-0 z-40 shadow-sm px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2 font-bold text-xl text-sky-600">
            <i class="fa-solid fa-flask-vial text-2xl"></i> <span>LabBorrow</span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600 font-medium">Hai, <span class="text-sky-600 font-bold"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></span></span>
            <a href="logout.php" onclick="return confirm('Keluar dari aplikasi?')" class="bg-red-500 text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-red-600 transition shadow-sm"><i class="fa-solid fa-power-off mr-1"></i> Keluar</a>
        </div>
    </nav>

    <div class="flex flex-1">
        
        <aside class="w-64 bg-white border-r border-sky-100 flex flex-col justify-between p-4 min-h-[calc(100vh-73px)] shadow-sm">
            <div class="space-y-2">
                <span class="text-[10px] font-bold text-sky-400 uppercase tracking-wider px-3">Menu Navigasi</span>
                
                <a href="index.php?p=dashboard" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition <?= $page === 'dashboard' ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-600' ?>">
                    <i class="fa-solid fa-table-list text-base w-5"></i>
                    <span>Log Peminjaman</span>
                </a>

                <a href="index.php?p=tutorial" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition <?= $page === 'tutorial' ? 'bg-sky-100 text-sky-700' : 'text-gray-600 hover:bg-sky-50 hover:text-sky-600' ?>">
                    <i class="fa-solid fa-circle-play text-base w-5"></i>
                    <span>Video Tutorial Lab</span>
                </a>
            </div>

            <div class="p-2 text-center bg-sky-50 rounded-xl border border-sky-100/50">
                <span class="text-[11px] text-sky-600 font-bold"><i class="fa-solid fa-server mr-1 text-emerald-500"></i> Laragon Active Engine</span>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-8">
            
            <?php if ($page === 'dashboard'): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Katalog Komponen</p>
                            <h3 class="text-4xl font-black text-sky-900 mt-1"><?= $total_produk ?> <span class="text-sm font-medium text-gray-500">Alat</span></h3>
                        </div>
                        <div class="text-sky-400 text-4xl bg-sky-50 p-3 rounded-xl"><i class="fa-solid fa-microchip"></i></div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Riwayat Pinjam</p>
                            <h3 class="text-4xl font-black text-sky-900 mt-1"><?= mysqli_num_rows($result) ?> <span class="text-sm font-medium text-gray-500">Transaksi</span></h3>
                        </div>
                        <div class="text-sky-400 text-4xl bg-sky-50 p-3 rounded-xl"><i class="fa-solid fa-file-signature"></i></div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Log Peminjaman Laboratorium</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Kelola data peminjaman inventaris alat lab secara real-time.</p>
                    </div>
                    <a href="tambah.php" class="bg-sky-600 text-white text-sm font-bold px-5 py-3 rounded-xl shadow-md shadow-sky-200 hover:bg-sky-700 transition flex items-center justify-center"><i class="fa-solid fa-plus mr-2"></i> Tambah Transaksi</a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-sky-100 p-6">
                    <table id="tabelBorrow" class="display w-full text-sm text-left">
                        <thead class="bg-sky-50/70 text-sky-900 uppercase text-xs border-b border-sky-100">
                            <tr>
                                <th class="p-3">No</th>
                                <th class="p-3">Nama Peminjam</th>
                                <th class="p-3">NIM / Kelas</th>
                                <th class="p-3">Alat Lab</th>
                                <th class="p-3">Tanggal Pinjam</th>
                                <th class="p-3">Berkas</th>
                                <th class="p-3 text-center">TTD</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($result)): $files = array_filter(explode(',', $row['bukti_files'])); ?>
                            <tr class="border-b border-gray-100 hover:bg-sky-50/30 transition">
                                <td class="p-3 font-semibold text-gray-500"><?= $no++ ?></td>
                                <td class="p-3 font-bold text-sky-600"><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                                <td class="p-3 font-medium text-gray-600"><?= htmlspecialchars($row['nim']) ?> / <?= htmlspecialchars($row['kelas']) ?></td>
                                <td class="p-3"><span class="bg-sky-50 text-sky-700 px-3 py-1 rounded-full font-bold text-xs border border-sky-100"><?= htmlspecialchars($row['nama_alat'] ?? 'Alat Dihapus') ?></span></td>
                                <td class="p-3 font-medium text-gray-600"><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td class="p-3"><span class="text-xs bg-gray-100 px-2.5 py-1 rounded-lg text-gray-600 font-semibold"><i class="fa-solid fa-paperclip mr-1"></i> <?= count($files) ?> File</span></td>
                                <td class="p-3 text-center">
                                    <?php if(!empty($row['tanda_tangan'])): ?>
                                        <button onclick="tampilTTD('<?= $row['tanda_tangan'] ?>')" class="text-sky-600 hover:text-sky-800 text-lg transition"><i class="fa-solid fa-signature"></i></button>
                                    <?php else: ?>
                                        <span class="text-red-400 text-xs italic">Kosong</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="inline-flex rounded-xl shadow-sm text-xs bg-white border border-gray-200 overflow-hidden">
                                        <button onclick="bukaModalDetail(<?= htmlspecialchars(json_encode($row)) ?>)" class="px-3 py-2 font-bold text-sky-600 hover:bg-sky-50 border-r border-gray-100 transition">Detail</button>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="px-3 py-2 font-bold text-amber-600 hover:bg-amber-50 border-r border-gray-100 transition">Edit</a>
                                        <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data transaksi ini?')" class="px-3 py-2 font-bold text-red-600 hover:bg-red-50 transition">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($page === 'tutorial'): ?>
                <div class="mb-6">
                    <h2 class="text-2xl font-black text-gray-800 tracking-tight">Video Tutorial Peminjaman Alat Lab</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Pelajari alur operasional tata cara peminjaman, penggunaan fitur, dan prosedur keselamatan laboratorium.</p>
                </div>

                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-2xl border border-sky-100 shadow-sm overflow-hidden p-5">
                        <div class="relative aspect-video rounded-xl overflow-hidden bg-black shadow-inner border border-sky-100">
                            <video class="w-full h-full object-contain" controls preload="metadata" controlsList="nodownload">
                                <source src="video/tutorial.mp4" type="video/mp4">
                                Browser Anda tidak mendukung pemutar video HTML5. Silakan perbarui browser Anda.
                            </video>
                        </div>
                        <div class="mt-5 border-t border-gray-50 pt-4">
                            <span class="bg-sky-100 text-sky-800 text-xs font-bold px-3 py-1 rounded-full border border-sky-200">
                                <i class="fa-solid fa-video mr-1"></i> Video Panduan Resmi
                            </span>
                            <h3 class="text-xl font-bold text-gray-800 mt-3">Panduan Lengkap Alur Sistem Informasi Peminjaman LabBorrow</h3>
                            <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                                Video ini menyajikan instruksi komprehensif mengenai tata cara mengajukan pinjaman alat lab, mengisi form identitas mahasiswa, mengunggah berkas gambar lampiran, hingga membubuhkan goresan tanda tangan digital secara valid langsung pada sistem aplikasi.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <div id="modalTTD" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-sky-100">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h4 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-signature text-sky-600"></i> Goresan Tanda Tangan</h4>
                <button onclick="tutupTTD()" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 w-7 h-7 rounded-full flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="bg-sky-50/50 border border-sky-100 rounded-xl p-2 flex justify-center items-center h-48 shadow-inner">
                <img id="imgTTD" src="" alt="TTD" class="max-h-full object-contain">
            </div>
        </div>
    </div>

    <div id="modalDetail" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div onclick="tutupModalDetail()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-sky-100 max-h-[90vh] overflow-y-auto p-6 md:p-8">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-6">
                <h2 class="text-xl font-black text-gray-800 tracking-tight">Informasi Lengkap Pengajuan</h2>
                <button onclick="tutupModalDetail()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-50 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="space-y-4 mb-6">
                <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                    <span class="font-bold text-gray-500">Peminjam</span>
                    <span id="modal_peminjam" class="col-span-2 font-semibold text-gray-700">: -</span>
                </div>
                <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                    <span class="font-bold text-gray-500">NIM / Kelas</span>
                    <span id="modal_nim_kelas" class="col-span-2 font-medium text-gray-600">: -</span>
                </div>
                <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                    <span class="font-bold text-gray-500">Komponen Lab</span>
                    <span id="modal_alat" class="col-span-2 font-bold text-sky-600">: -</span>
                </div>
                <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                    <span class="font-bold text-gray-500">Tanggal Pinjam</span>
                    <span id="modal_tanggal" class="col-span-2 font-medium text-gray-600">: -</span>
                </div>
                <div class="grid grid-cols-3 py-1.5 border-b border-gray-50 text-sm">
                    <span class="font-bold text-gray-500">Keperluan</span>
                    <span id="modal_keperluan" class="col-span-2 font-medium text-gray-600">: -</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wider text-sky-600 mb-3"><i class="fa-solid fa-images mr-1"></i> Berkas Lampiran:</h3>
                    <div id="modal_container_berkas" class="grid grid-cols-2 gap-2"></div>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-xs uppercase tracking-wider text-sky-600 mb-3"><i class="fa-solid fa-signature mr-1"></i> Tanda Tangan:</h3>
                    <div class="bg-sky-50/50 border border-sky-100 rounded-xl p-2 flex justify-center items-center h-32 shadow-inner">
                        <img id="modal_ttd" src="" alt="Tanda Tangan" class="max-h-full max-w-full object-contain mix-blend-multiply hidden">
                        <span id="modal_ttd_kosong" class="text-xs text-red-400 italic hidden">Belum ditandatangani</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables jika ada elemen tabel di halaman aktif
            if ($('#tabelBorrow').length) {
                $('#tabelBorrow').DataTable({
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'print', text: '<i class="fa-solid fa-print mr-1"></i> Cetak' },
                        { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel mr-1"></i> Excel' },
                        { extend: 'pdfHtml5', text: '<i class="fa-solid fa-file-pdf mr-1"></i> PDF' }
                    ],
                    language: { 
                        search: "Cari Data:", 
                        zeroRecords: "Belum ada log transaksi peminjaman", 
                        paginate: { next: "<i class='fa-solid fa-chevron-right text-xs'></i>", previous: "<i class='fa-solid fa-chevron-left text-xs'></i>" } 
                    }
                });
            }
        });

        // Kontroler Tanda Tangan Modal
        function tampilTTD(uri) { document.getElementById('imgTTD').src = uri; document.getElementById('modalTTD').classList.remove('hidden'); }
        function tutupTTD() { document.getElementById('modalTTD').classList.add('hidden'); }

        // JAVASCRIPT KONTROLER UTAMA POPUP MODAL DETAIL
        function bukaModalDetail(data) {
            const modal = document.getElementById('modalDetail');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            document.getElementById('modal_peminjam').innerText = ": " + (data.nama_peminjam || '-');
            document.getElementById('modal_nim_kelas').innerText = ": " + (data.nim || '-') + " / " + (data.kelas || '-');
            document.getElementById('modal_alat').innerText = ": " + (data.nama_alat || 'Alat Dihapus');
            document.getElementById('modal_keperluan').innerText = ": " + (data.keperluan || '-');
            
            if(data.tanggal_pinjam) {
                const opsi = { day: 'numeric', month: 'long', year: 'numeric' };
                const tgl = new Date(data.tanggal_pinjam).toLocaleDateString('id-ID', opsi);
                document.getElementById('modal_tanggal').innerText = ": " + tgl;
            }

            const containerBerkas = document.getElementById('modal_container_berkas');
            containerBerkas.innerHTML = "";

            if (data.bukti_files && data.bukti_files.trim() !== "") {
                const files = data.bukti_files.split(',');
                files.forEach(file => {
                    let namaFile = file.trim();
                    if (namaFile !== "") {
                        namaFile = namaFile.replace('uploads/', '').replace('img/', '');
                        const pathGambar = "img/" + namaFile;

                        const div = document.createElement('div');
                        div.className = "group relative aspect-[4/3] rounded-xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm";
                        div.innerHTML = `
                            <img src="${pathGambar}" class="w-full h-full object-cover">
                            <a href="${pathGambar}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition duration-200">Lihat Penuh</a>
                        `;
                        containerBerkas.appendChild(div);
                    }
                });
            } else {
                containerBerkas.innerHTML = `<p class="col-span-2 text-xs italic text-gray-400 bg-gray-50 p-3 rounded-lg border border-dashed border-gray-200 w-full text-center">Tidak ada lampiran.</p>`;
            }

            const imgTtd = document.getElementById('modal_ttd');
            const ttdKosong = document.getElementById('modal_ttd_kosong');

            if (data.tanda_tangan && data.tanda_tangan.trim() !== "") {
                imgTtd.src = data.tanda_tangan;
                imgTtd.classList.remove('hidden');
                ttdKosong.classList.add('hidden');
            } else {
                imgTtd.src = "";
                imgTtd.classList.add('hidden');
                ttdKosong.classList.remove('hidden');
            }
        }

        function tutupModalDetail() {
            document.getElementById('modalDetail').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') { tutupModalDetail(); tutupTTD(); }
        });
    </script>
</body>
</html>