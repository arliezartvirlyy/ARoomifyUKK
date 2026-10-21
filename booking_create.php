<?php
require 'config/koneksi.php';
require 'includes/functions.php';
require_login();
if ($_SESSION['user']['role'] !== 'peminjam') { header('Location: bookings.php'); exit; }
$rooms=$pdo->query("SELECT id,name,location,capacity FROM rooms WHERE status='aktif' ORDER BY name")->fetchAll();
$selectedRoom=(int)($_GET['room_id']??0);
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $roomId=(int)($_POST['room_id']??0);
    $purpose=trim($_POST['purpose']??'');
    $date=$_POST['booking_date']??'';
    $start=$_POST['start_time']??'';
    $end=$_POST['end_time']??'';
    $participants=(int)($_POST['participants']??0);
    $roomStmt=$pdo->prepare("SELECT * FROM rooms WHERE id=? AND status='aktif'"); $roomStmt->execute([$roomId]); $room=$roomStmt->fetch();
    if (!$room || $purpose==='' || !$date || !$start || !$end || $participants<1) {
        $error='Lengkapi semua data dengan benar.';
    } elseif ($date < date('Y-m-d')) {
        $error='Tanggal peminjaman tidak boleh di masa lalu.';
    } elseif ($start >= $end) {
        $error='Jam selesai harus setelah jam mulai.';
    } elseif ($participants > (int)$room['capacity']) {
        $error='Jumlah peserta melebihi kapasitas ruangan.';
    } else {
        $check=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id=? AND booking_date=? AND status IN ('menunggu','disetujui') AND start_time < ? AND end_time > ?");
        $check->execute([$roomId,$date,$end,$start]);
        if ((int)$check->fetchColumn()>0) {
            $error='Jadwal bertabrakan dengan pengajuan lain. Pilih jam berbeda.';
        } else {
            $stmt=$pdo->prepare("INSERT INTO bookings (user_id,room_id,purpose,booking_date,start_time,end_time,participants) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$_SESSION['user']['id'],$roomId,$purpose,$date,$start,$end,$participants]);
            flash('Pengajuan berhasil dikirim dan menunggu persetujuan admin.');
            header('Location: bookings.php'); exit;
        }
    }
    $selectedRoom=$roomId;
}
$pageTitle='Ajukan Peminjaman';
require 'includes/header.php';
?>
<div class="mb-4"><a href="rooms.php" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Daftar ruangan</a><h1 class="h2 page-title mt-2">Ajukan Peminjaman</h1><p class="text-secondary mb-0">Pilih ruangan dan waktu penggunaan.</p></div>
<div class="card p-4"><div class="row justify-content-center"><div class="col-lg-8">
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (!$rooms): ?><div class="alert alert-warning">Belum ada ruangan aktif untuk dipinjam.</div><?php else: ?>
<form method="post">
<div class="mb-3"><label class="form-label">Ruangan *</label><select class="form-select" name="room_id" required><option value="">Pilih ruangan</option><?php foreach($rooms as $r): ?><option value="<?= (int)$r['id'] ?>" <?= $selectedRoom===(int)$r['id']?'selected':'' ?>><?= e($r['name']) ?> — <?= e($r['location']) ?> (kapasitas <?= (int)$r['capacity'] ?>)</option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label">Keperluan peminjaman *</label><input class="form-control" name="purpose" maxlength="200" required value="<?= e($_POST['purpose']??'') ?>" placeholder="Contoh: Rapat OSIS"></div>
<div class="row"><div class="col-md-6 mb-3"><label class="form-label">Tanggal *</label><input type="date" class="form-control" name="booking_date" min="<?= date('Y-m-d') ?>" required value="<?= e($_POST['booking_date']??'') ?>"></div><div class="col-md-3 mb-3"><label class="form-label">Mulai *</label><input type="time" class="form-control" name="start_time" required value="<?= e($_POST['start_time']??'') ?>"></div><div class="col-md-3 mb-3"><label class="form-label">Selesai *</label><input type="time" class="form-control" name="end_time" required value="<?= e($_POST['end_time']??'') ?>"></div></div>
<div class="mb-4"><label class="form-label">Jumlah peserta *</label><input type="number" class="form-control" name="participants" min="1" required value="<?= e($_POST['participants']??'1') ?>"></div>
<button class="btn btn-primary">Kirim Pengajuan</button><a href="bookings.php" class="btn btn-light ms-2">Batal</a>
</form><?php endif; ?>
</div></div></div>
<?php require 'includes/footer.php'; ?>
