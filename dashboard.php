<?php
require 'config/koneksi.php';
require 'includes/functions.php';
require_login();
$user = $_SESSION['user'];
if ($user['role'] === 'admin') {
    $roomCount = (int)$pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
    $pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='menunggu'")->fetchColumn();
    $approvedCount = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='disetujui'")->fetchColumn();
    $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='peminjam'")->fetchColumn();
    $stmt = $pdo->query("SELECT b.*, r.name AS room_name, u.name AS user_name FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN users u ON u.id=b.user_id ORDER BY b.created_at DESC LIMIT 8");
} else {
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id=?"); $stmtCount->execute([$user['id']]); $myCount=(int)$stmtCount->fetchColumn();
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id=? AND status='menunggu'"); $stmtCount->execute([$user['id']]); $myPending=(int)$stmtCount->fetchColumn();
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id=? AND status='disetujui'"); $stmtCount->execute([$user['id']]); $myApproved=(int)$stmtCount->fetchColumn();
    $stmt = $pdo->prepare("SELECT b.*, r.name AS room_name FROM bookings b JOIN rooms r ON r.id=b.room_id WHERE b.user_id=? ORDER BY b.created_at DESC LIMIT 8");
    $stmt->execute([$user['id']]);
}
$recent = $stmt->fetchAll();
$pageTitle = 'Dashboard';
require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
<div><p class="text-primary fw-semibold mb-1">ROOMIFY / DASHBOARD</p><h1 class="h2 page-title mb-1">Halo, <?= e($user['name']) ?>!</h1><p class="text-secondary mb-0">Ringkasan aktivitas peminjaman ruangan.</p></div>
<?php if ($user['role'] === 'peminjam'): ?><a class="btn btn-primary" href="booking_create.php"><i class="bi bi-plus-lg me-2"></i>Ajukan Peminjaman</a><?php else: ?><a class="btn btn-primary" href="room_form.php"><i class="bi bi-plus-lg me-2"></i>Tambah Ruangan</a><?php endif; ?>
</div>
<div class="row g-3 mb-4">
<?php if ($user['role'] === 'admin'): ?>
<div class="col-6 col-xl-3"><div class="card p-3 p-lg-4"><div class="text-secondary small">Total Ruangan</div><div class="display-6 fw-bold"><?= $roomCount ?></div><i class="bi bi-door-open text-primary fs-4"></i></div></div>
<div class="col-6 col-xl-3"><div class="card p-3 p-lg-4"><div class="text-secondary small">Menunggu Persetujuan</div><div class="display-6 fw-bold"><?= $pendingCount ?></div><i class="bi bi-hourglass-split text-warning fs-4"></i></div></div>
<div class="col-6 col-xl-3"><div class="card p-3 p-lg-4"><div class="text-secondary small">Booking Disetujui</div><div class="display-6 fw-bold"><?= $approvedCount ?></div><i class="bi bi-check-circle text-success fs-4"></i></div></div>
<div class="col-6 col-xl-3"><div class="card p-3 p-lg-4"><div class="text-secondary small">Peminjam Terdaftar</div><div class="display-6 fw-bold"><?= $userCount ?></div><i class="bi bi-people text-info fs-4"></i></div></div>
<?php else: ?>
<div class="col-6 col-xl-4"><div class="card p-3 p-lg-4"><div class="text-secondary small">Total Pengajuan</div><div class="display-6 fw-bold"><?= $myCount ?></div><i class="bi bi-calendar2-week text-primary fs-4"></i></div></div>
<div class="col-6 col-xl-4"><div class="card p-3 p-lg-4"><div class="text-secondary small">Menunggu</div><div class="display-6 fw-bold"><?= $myPending ?></div><i class="bi bi-hourglass-split text-warning fs-4"></i></div></div>
<div class="col-6 col-xl-4"><div class="card p-3 p-lg-4"><div class="text-secondary small">Disetujui</div><div class="display-6 fw-bold"><?= $myApproved ?></div><i class="bi bi-check-circle text-success fs-4"></i></div></div>
<?php endif; ?>
</div>
<div class="card p-3 p-md-4">
<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 fw-bold mb-0"><?= $user['role'] === 'admin' ? 'Peminjaman Terbaru' : 'Riwayat Peminjaman Saya' ?></h2><a href="bookings.php" class="small text-decoration-none">Lihat semua <i class="bi bi-arrow-right"></i></a></div>
<div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Ruangan</th><?php if ($user['role']==='admin'): ?><th>Peminjam</th><?php endif; ?><th>Tanggal</th><th>Waktu</th><th>Status</th></tr></thead><tbody>
<?php if (!$recent): ?><tr><td colspan="<?= $user['role']==='admin' ? 5 : 4 ?>" class="text-center text-secondary py-4">Belum ada data peminjaman.</td></tr><?php endif; ?>
<?php foreach ($recent as $b): ?><tr><td class="fw-semibold"><?= e($b['room_name']) ?><div class="small text-secondary"><?= e($b['purpose']) ?></div></td><?php if ($user['role']==='admin'): ?><td><?= e($b['user_name']) ?></td><?php endif; ?><td><?= rupiah_date($b['booking_date']) ?></td><td><?= e(substr($b['start_time'],0,5)) ?>–<?= e(substr($b['end_time'],0,5)) ?></td><td><span class="badge text-bg-<?= ['menunggu'=>'warning','disetujui'=>'success','ditolak'=>'danger','selesai'=>'secondary','dibatalkan'=>'dark'][$b['status']] ?? 'secondary' ?>"><?= e(ucfirst($b['status'])) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
