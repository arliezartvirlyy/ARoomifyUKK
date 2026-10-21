<?php
require 'config/koneksi.php';
require 'includes/functions.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || $username === '' || strlen($password) < 6) {
        $error = 'Lengkapi semua data. Password minimal 6 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, 'peminjam')");
            $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Pendaftaran berhasil. Silakan login.');
            header('Location: login.php'); exit;
        } catch (PDOException $e) {
            $error = 'Username sudah digunakan. Silakan pilih username lain.';
        }
    }
}
$pageTitle = 'Daftar Peminjam';
require 'includes/header.php';
?>
<div class="row justify-content-center py-4"><div class="col-md-7 col-lg-5">
<div class="card p-4 p-md-5">
<h1 class="h3 page-title">Buat akun Roomify</h1><p class="text-secondary">Daftar untuk mengajukan peminjaman ruangan.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<div class="mb-3"><label class="form-label">Nama lengkap</label><input class="form-control" name="name" required maxlength="100"></div>
<div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required maxlength="50"></div>
<div class="mb-4"><label class="form-label">Password (minimal 6 karakter)</label><input type="password" class="form-control" name="password" required minlength="6"></div>
<button class="btn btn-primary w-100">Daftar</button>
</form><p class="text-center mt-3 mb-0 small"><a href="login.php">Kembali ke login</a></p>
</div></div></div>
<?php require 'includes/footer.php'; ?>
