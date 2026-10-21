<?php
require 'config/koneksi.php';
require 'includes/functions.php';
require_login();
$isAdmin = $_SESSION['user']['role'] === 'admin';
if (isset($_GET['delete']) && $isAdmin) {
    $id = (int)$_GET['delete'];
    $check = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE room_id=?'); $check->execute([$id]);
    if ((int)$check->fetchColumn() > 0) {
        flash('Ruangan tidak dapat dihapus karena sudah memiliki riwayat peminjaman.', 'warning');
    } else {
        $del = $pdo->prepare('DELETE FROM rooms WHERE id=?'); $del->execute([$id]);
        flash('Ruangan berhasil dihapus.');
    }
    header('Location: rooms.php'); exit;
}
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY status='aktif' DESC, name ASC")->fetchAll();
$pageTitle = 'Daftar Ruangan';
require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 page-title mb-1">Daftar Ruangan</h1><p class="text-secondary mb-0">Lihat informasi dan kapasitas ruangan yang tersedia.</p></div><?php if ($isAdmin): ?><a href="room_form.php" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah Ruangan</a><?php endif; ?></div>
<div class="row g-3">
<?php foreach ($rooms as $r): ?>
<div class="col-md-6 col-xl-4"><div class="card h-100 p-4">
<div class="d-flex justify-content-between align-items-start mb-3"><span class="rounded-3 bg-primary-subtle text-primary p-3"><i class="bi bi-door-closed fs-3"></i></span><span class="badge text-bg-<?= $r['status']==='aktif'?'success':'secondary' ?>"><?= e(ucfirst($r['status'])) ?></span></div>
<h2 class="h5 fw-bold"><?= e($r['name']) ?></h2><p class="text-secondary small mb-2"><i class="bi bi-geo-alt me-1"></i><?= e($r['location']) ?> · <i class="bi bi-people ms-1 me-1"></i><?= (int)$r['capacity'] ?> orang</p>
<p class="small"><?= e($r['description'] ?: 'Belum ada deskripsi.') ?></p>
<div class="mt-auto"><div class="small text-secondary mb-3"><strong>Fasilitas:</strong> <?= e($r['facilities'] ?: 'Belum dicantumkan') ?></div>
<?php if ($isAdmin): ?><div class="d-flex gap-2"><a href="room_form.php?id=<?= (int)$r['id'] ?>" class="btn btn-outline-primary btn-sm flex-fill">Edit</a><a href="rooms.php?delete=<?= (int)$r['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus ruangan ini?')">Hapus</a></div>
<?php elseif ($r['status']==='aktif'): ?><a class="btn btn-primary w-100" href="booking_create.php?room_id=<?= (int)$r['id'] ?>">Ajukan Peminjaman</a><?php else: ?><button class="btn btn-secondary w-100" disabled>Ruangan nonaktif</button><?php endif; ?>
</div></div></div>
<?php endforeach; ?>
<?php if (!$rooms): ?><div class="col-12"><div class="alert alert-info">Belum ada data ruangan.</div></div><?php endif; ?>
</div>
<?php require 'includes/footer.php'; ?>
