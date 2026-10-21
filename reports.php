<?php
require 'config/koneksi.php';
require 'includes/functions.php';
require_admin();
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
$stmt=$pdo->prepare("SELECT b.*,r.name AS room_name,u.name AS user_name FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN users u ON u.id=b.user_id WHERE b.booking_date BETWEEN ? AND ? ORDER BY b.booking_date DESC,b.start_time");
$stmt->execute([$from,$to]); $rows=$stmt->fetchAll();
$pageTitle='Laporan Peminjaman';
require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 page-title mb-1">Laporan Peminjaman</h1><p class="text-secondary mb-0">Rekap peminjaman berdasarkan rentang tanggal.</p></div><button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer me-2"></i>Cetak Laporan</button></div>
<div class="card p-3 p-md-4">
<form method="get" class="row g-3 align-items-end mb-4"><div class="col-sm-4"><label class="form-label">Dari tanggal</label><input type="date" class="form-control" name="from" value="<?= e($from) ?>" required></div><div class="col-sm-4"><label class="form-label">Sampai tanggal</label><input type="date" class="form-control" name="to" value="<?= e($to) ?>" required></div><div class="col-sm-4"><button class="btn btn-primary">Tampilkan</button></div></form>
<p class="text-secondary small">Periode: <?= rupiah_date($from) ?> sampai <?= rupiah_date($to) ?> · Total data: <?= count($rows) ?></p>
<div class="table-responsive"><table class="table table-bordered align-middle"><thead><tr><th>No</th><th>Ruangan</th><th>Peminjam</th><th>Keperluan</th><th>Tanggal</th><th>Waktu</th><th>Peserta</th><th>Status</th></tr></thead><tbody>
<?php foreach($rows as $i=>$b): ?><tr><td><?= $i+1 ?></td><td><?= e($b['room_name']) ?></td><td><?= e($b['user_name']) ?></td><td><?= e($b['purpose']) ?></td><td><?= rupiah_date($b['booking_date']) ?></td><td><?= e(substr($b['start_time'],0,5)) ?>–<?= e(substr($b['end_time'],0,5)) ?></td><td><?= (int)$b['participants'] ?></td><td><?= e(ucfirst($b['status'])) ?></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="8" class="text-center text-secondary">Tidak ada data pada periode ini.</td></tr><?php endif; ?>
</tbody></table></div></div>
<style>@media print { nav, footer, form, button, .btn, .alert {display:none!important} body{background:#fff}.card{box-shadow:none!important} main.container{max-width:100%;padding:0!important} }</style>
<?php require 'includes/footer.php'; ?>
