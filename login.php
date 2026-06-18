<?php
require_once 'koneksi.php';

// Jika user sudah login, langsung alihkan ke dashboard
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Query untuk mencari user berdasarkan username
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $row['password']) || md5($password) === $row['password'] || $password === $row['password']) {
                $_SESSION['login'] = true;
                $_SESSION['id_user'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                
                header("Location: index.php");
                exit;
            } else {
                $error = 'Password yang Anda masukkan salah.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
    } else {
        $error = 'Username dan Password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LabBorrow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-3xl shadow-2xl shadow-sky-100/70 border border-sky-100/80 max-w-md w-full p-8 md:p-10 transition">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center bg-sky-50 text-sky-600 w-16 h-16 rounded-full mb-4 shadow-sm border border-sky-100/50">
                <i class="fa-solid fa-flask-vial text-3xl"></i>
            </div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">LabBorrow</h1>
            <p class="text-xs text-sky-600 font-bold uppercase tracking-wider mt-1">Sistem Peminjaman Alat Laboratorium</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-semibold p-3.5 rounded-xl mb-6 flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            
            <div>
                <label for="username" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sky-500">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="username" id="username" required autocomplete="off"
                        class="w-full pl-11 pr-4 py-3 bg-sky-50/30 border border-sky-100/70 rounded-xl text-sm font-medium text-gray-700 placeholder-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sky-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-11 pr-4 py-3 bg-sky-50/30 border border-sky-100/70 rounded-xl text-sm font-medium text-gray-700 placeholder-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>
            </div>

            <button type="submit" name="login" 
                class="w-full bg-sky-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-sky-100 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition flex items-center justify-center space-x-2 mt-6">
                <span>Masuk</span>
                <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
            </button>

        </form>

        <div class="text-center mt-8 pt-4 border-t border-gray-100">
            <span class="text-[11px] text-gray-400 font-medium">© 2026 LabBorrow Informatics Engineering.</span>
        </div>

    </div>

</body>
</html>