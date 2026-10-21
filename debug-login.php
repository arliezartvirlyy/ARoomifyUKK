<?php
require 'config/koneksi.php';
require 'includes/functions.php';

echo "<h2>DEBUG ROOMIFY LOGIN</h2>";
echo "1. Cek PDO ada? " . (isset($pdo) ? "ADA ✅" : "GAK ADA ❌ - koneksi.php salah") . "<br>";

if(!isset($pdo)){ die("Fix dulu config/koneksi.php pakai PDO!"); }

// Cek tabel users
try{
  $users = $pdo->query("SELECT id, name, username, password, role, LENGTH(password) as len FROM users")->fetchAll();
  echo "2. Jumlah user di DB: " . count($users) . "<br>";
  foreach($users as $u){
    echo "- {$u['username']} / {$u['name']} / role:{$u['role']} / panjang hash:{$u['len']} (harus 60) / hash: ".substr($u['password'],0,20)."..<br>";
  }
} catch(Exception $e){
  die("Tabel users error: ".$e->getMessage());
}

echo "<hr><h3>Test Login Manual</h3>";
echo '<form method="post"><input name="username" placeholder="username" value="admin"><input name="password" placeholder="password" value="password"><button>Test</button></form>';

if($_POST){
  $u = trim($_POST['username']);
  $p = $_POST['password'];
  echo "<br>Input: username=<b>$u</b> password=<b>$p</b><br>";
  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$u]);
  $acc = $stmt->fetch();
  if(!$acc){
    echo "❌ USER TIDAK DITEMUKAN di DB! Cek username kamu salah tulis.";
  } else {
    echo "✅ User ditemukan: {$acc['name']}<br>";
    echo "Hash di DB: {$acc['password']}<br>";
    $cek = password_verify($p, $acc['password']);
    echo "password_verify('$p', hash) = " . ($cek ? "TRUE ✅ COCOK" : "FALSE ❌ GAK COCOK") . "<br>";
    if(!$cek){
      echo "<br><b>SOLUSI:</b> Password di DB bukan hash dari '$p'. Jalankan SQL ini di phpMyAdmin:<br>";
      echo "<code>UPDATE users SET password = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = '$u';</code><br>";
      echo "Abis itu login pakai password: <b>password</b>";
    } else {
      echo "<br>✅ Seharusnya bisa login! Kalau masih gak bisa, cek session_start() double atau redirect error di login.php";
    }
  }
}
?>