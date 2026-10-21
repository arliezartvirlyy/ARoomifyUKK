<?php
require 'config/koneksi.php';
require 'includes/functions.php';
require_admin();
$id = (int)($_GET['id'] ?? 0);
$room = ['name'=>'','location'=>'','capacity'=>1,'facilities'=>'','description'=>'','status'=>'aktif'];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id=?'); $stmt->execute([$id]); $found=$stmt->fetch();
    if (!$found) { flash('Ruangan tidak ditemukan.', 'danger'); header('Location: rooms.php'); exit; }
    $room=$found;
}
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $room['name']=trim($_POST['name']??'');
    $room['location']=trim($_POST['location']??'');
    $room['capacity']=(int)($_POST['capacity']??0);
    $room['facilities']=trim($_POST['facilities']??'');
    $room['description']=trim($_POST['description']??'');
    $room['status']=$_POST['status']??'aktif';
    if ($room['name']==='' || $room['location']==='' || $room['capacity']<1) $error='Nama, lokasi, dan kapasitas wajib diisi dengan benar.';
    elseif (!in_array($room['status'],['aktif','nonaktif'],true)) $error='Status tidak valid.';
    else {
        if ($id) {
            $stmt=$pdo->prepare('UPDATE rooms SET name=?, location=?, capacity=?, facilities=?, description=?, status=? WHERE id=?');
            $stmt->execute([$room['name'],$room['location'],$room['capacity'],$room['facilities'],$room['description'],$room['status'],$id]);
            flash('Data ruangan berhasil diperbarui.');
        } else {
            $stmt=$pdo->prepare('INSERT INTO rooms (name,location,capacity,facilities,description,status) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$room['name'],$room['location'],$room['capacity'],$room['facilities'],$room['description'],$room['status']]);
            flash('Ruangan baru berhasil ditambahkan.');
        }
        header('Location: rooms.php'); exit;
    }
}
$pageTitle=$id?'Edit Ruangan':'Tambah Ruangan';
require 'includes/header.php';
?>
<div class="mb-4"><a href="rooms.php" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali</a><h1 class="h2 page-title mt-2"><?= e($pageTitle) ?></h1></div>
<div class="card p-4"><div class="row justify-content-center"><div class="col-lg-8">
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<div class="mb-3"><label class="form-label">Nama ruangan *</label><input class="form-control" name="name" value="<?= e($room['name']) ?>" required maxlength="100"></div>
<div class="row"><div class="col-md-8 mb-3"><label class="form-label">Lokasi *</label><input class="form-control" name="location" value="<?= e($room['location']) ?>" required maxlength="150" placeholder="Contoh: Lantai 2"></div><div class="col-md-4 mb-3"><label class="form-label">Kapasitas orang *</label><input type="number" class="form-control" name="capacity" min="1" value="<?= (int)$room['capacity'] ?>" required></div></div>
<div class="mb-3"><label class="form-label">Fasilitas</label><input class="form-control" name="facilities" value="<?= e($room['facilities']) ?>" placeholder="Proyektor, Wi-Fi, AC"></div>
<div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"><?= e($room['description']) ?></textarea></div>
<div class="mb-4"><label class="form-label">Status</label><select class="form-select" name="status"><option value="aktif" <?= $room['status']==='aktif'?'selected':'' ?>>Aktif</option><option value="nonaktif" <?= $room['status']==='nonaktif'?'selected':'' ?>>Nonaktif</option></select></div>
<button class="btn btn-primary">Simpan</button><a href="rooms.php" class="btn btn-light ms-2">Batal</a>
</form></div></div></div>
<?php require 'includes/footer.php'; ?>
