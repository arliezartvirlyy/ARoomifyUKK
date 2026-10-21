<?php
require 'config/koneksi.php';
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute(['admin']);
if ($stmt->fetch()) {
    exit('Akun admin sudah ada. Hapus file setup_admin.php dan login seperti biasa.');
}
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute(['Administrator', 'admin', $hash]);
echo 'Admin berhasil dibuat. Username: admin | Password: admin123. Segera hapus file setup_admin.php setelah ini.';
