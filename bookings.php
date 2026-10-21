<?php
require 'config/koneksi.php';
require 'includes/functions.php';
require_login();
$user=$_SESSION['user']; $isAdmin=$user['role']==='admin';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
    $id=(int)($_POST['booking_id']??0); $action=$_POST['action']; $note=trim($_POST['admin_note']??'');
    if ($isAdmin && in_array($action,['disetujui','ditolak','selesai'],true)) {
        $st=$pdo->prepare("SELECT * FROM bookings WHERE id=?"); $st->execute([$id]); $b=$st->fetch();
        if (!$b) flash('Data peminjaman tidak ditemukan.','danger');
        elseif ($action==='disetujui') {
            $check=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE id<>? AND room_id=? AND booking_date=? AND status='disetujui' AND start_time < ? AND end_time > ?");
            $check->execute([$id,$b['room_id'],$b['booking_date'],$b['end_time'],$b['start_time']]);
            if ((int)$check->fetchColumn()>0) flash('Tidak dapat menyetujui karena jadwal bertabrakan.','danger');
            else { $up=$pdo->prepare('UPDATE bookings SET status=?, admin_note=? WHERE id=?'); $up->execute([$action,$note,$id]); flash('Peminjaman berhasil disetujui.'); }
        } else {
            $up=$pdo->prepare('UPDATE bookings SET status=?, admin_note=? WHERE id=?'); $up->execute([$action,$note,$id]); flash('Status peminjaman diperbarui.');
        }
    } elseif (!$isAdmin && $action==='batalkan') {
        $up=$pdo->prepare("UPDATE bookings SET status='dibatalkan' WHERE id=? AND user_id=? AND status='menunggu'");
        $up->execute([$id,$user['id']]);
        flash($up->rowCount() ? 'Pengajuan berhasil dibatalkan.' : 'Pengajuan tidak dapat dibatalkan.','warning');
    }
    header('Location: bookings.php'); exit;
}
$status=$_GET['status']??'';
$sql="SELECT b.*, r.name AS room_name, r.location, u.name AS user_name FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN users u ON u.id=b.user_id";
$params=[];
$where=[];
if (!$isAdmin) { $where[]='b.user_id=?'; $params[]=$user['id']; }
if (in_array($status,['menunggu','disetujui','ditolak','selesai','dibatalkan'],true)) { $where[]='b.status=?'; $params[]=$status; }
if ($where) $sql.=' WHERE '.implode(' AND ',$where);
$sql.=' ORDER BY b.booking_date DESC, b.start_time DESC';
$stmt=$pdo->prepare($sql); $stmt->execute($params); $bookings=$stmt->fetchAll();
$pageTitle=$isAdmin?'Data Peminjaman':'Peminjaman Saya';
require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 page-title mb-1"><?= e($pageTitle) ?></h1><p class="text-secondary mb-0"><?= $isAdmin?'Kelola pengajuan dan status peminjaman ruangan.':'Pantau status pengajuan ruangan Anda.' ?></p></div><?php if (!$isAdmin): ?><a href="booking_create.php" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Ajukan Peminjaman</a><?php endif; ?></div>
<div class="card p-3 p-md-4">
<form method="get" class="row g-2 mb-3"><div class="col-sm-5 col-md-3"><select class="form-select" name="status"><option value="">Semua status</option><?php foreach(['menunggu','disetujui','ditolak','selesai','dibatalkan'] as $s): ?><option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select></div><div class="col-auto"><button class="btn btn-outline-primary">Filter</button><a class="btn btn-light" href="bookings.php">Reset</a></div></form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ruangan / Keperluan</th><?php if($isAdmin): ?><th>Peminjam</th><?php endif; ?><th>Tanggal</th><th>Waktu</th><th>Peserta</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php if (!$bookings): ?><tr><td colspan="<?= $isAdmin?7:6 ?>" class="text-center text-secondary py-4">Data peminjaman tidak ditemukan.</td></tr><?php endif; ?>
<?php foreach($bookings as $b): ?><tr><td class="fw-semibold"><?= e($b['room_name']) ?><div class="small text-secondary"><?= e($b['purpose']) ?></div><?php if($b['admin_note']): ?><div class="small text-secondary">Catatan: <?= e($b['admin_note']) ?></div><?php endif; ?></td><?php if($isAdmin): ?><td><?= e($b['user_name']) ?></td><?php endif; ?><td><?= rupiah_date($b['booking_date']) ?></td><td><?= e(substr($b['start_time'],0,5)) ?>–<?= e(substr($b['end_time'],0,5)) ?></td><td><?= (int)$b['participants'] ?></td><td><span class="badge text-bg-<?= ['menunggu'=>'warning','disetujui'=>'success','ditolak'=>'danger','selesai'=>'secondary','dibatalkan'=>'dark'][$b['status']] ?? 'secondary' ?>"><?= e(ucfirst($b['status'])) ?></span></td><td class="text-nowrap">
<?php if($isAdmin && $b['status']==='menunggu'): ?><form method="post" class="d-inline"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="action" value="disetujui"><button class="btn btn-success btn-sm" title="Setujui"><i class="bi bi-check-lg"></i></button></form> <form method="post" class="d-inline"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="action" value="ditolak"><button class="btn btn-outline-danger btn-sm" title="Tolak"><i class="bi bi-x-lg"></i></button></form>
<?php elseif($isAdmin && $b['status']==='disetujui'): ?><form method="post" class="d-inline"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="action" value="selesai"><button class="btn btn-outline-secondary btn-sm">Tandai selesai</button></form>
<?php elseif(!$isAdmin && $b['status']==='menunggu'): ?><form method="post" class="d-inline" onsubmit="return confirm('Batalkan pengajuan ini?')"><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="action" value="batalkan"><button class="btn btn-outline-danger btn-sm">Batalkan</button></form><?php else: ?><span class="text-secondary small">—</span><?php endif; ?>
</td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
