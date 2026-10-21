<?php
require 'config/koneksi.php';
require 'includes/functions.php';

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, name, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $account = $stmt->fetch();
    if ($account && password_verify($password, $account['password'])) {
        session_regenerate_id(true);
        unset($account['password']);
        $_SESSION['user'] = $account;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Username atau password tidak sesuai.';
}
$pageTitle = 'Login';
require 'includes/header.php';
?>
<div class="row justify-content-center align-items-center" style="min-height:72vh">
    <div class="col-md-7 col-lg-5">
        <div class="card p-4 p-md-5">
            <div class="text-center mb-4">
                <span class="brand-mark mb-3" style="width:58px;height:58px;font-size:1.5rem"><i class="bi bi-calendar2-check"></i></span>
                <h1 class="h3 page-title">Selamat datang di Roomify</h1>
                <p class="text-secondary mb-0">Masuk untuk mengatur peminjaman ruangan.</p>
            </div>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-3"><label class="form-label">Username</label><input class="form-control form-control-lg" name="username" required autofocus autocomplete="username"></div>
                <div class="mb-4"><label class="form-label">Password</label><input class="form-control form-control-lg" type="password" name="password" required autocomplete="current-password"></div>
                <button class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</button>
            </form>
            <div class="text-center mt-4 text-secondary small">Belum punya akun? <a href="register.php">Daftar sebagai peminjam</a></div>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
