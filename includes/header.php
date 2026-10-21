<?php
$pageTitle = $pageTitle ?? 'Dashboard';
$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Roomify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background:#f4f6fb; }
        .navbar-brand { font-weight:800; letter-spacing:.2px; }
        .brand-mark { display:inline-flex; width:34px; height:34px; align-items:center; justify-content:center; border-radius:10px; background:#e8efff; color:#2458c6; margin-right:8px; }
        .card { border:0; box-shadow:0 4px 18px rgba(20,35,70,.05); }
        .page-title { font-weight:750; color:#18243c; }
        .btn-primary { background:#2458c6; border-color:#2458c6; }
        .btn-primary:hover { background:#1b449b; border-color:#1b449b; }
        .table thead th { color:#667085; font-size:.82rem; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
        .nav-link.active { font-weight:700; }
        .muted-small { font-size:.88rem; color:#697586; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand text-dark" href="<?= $user ? 'dashboard.php' : 'login.php' ?>">
            <span class="brand-mark"><i class="bi bi-calendar2-check"></i></span>Roomify
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <?php if ($user): ?>
            <ul class="navbar-nav me-auto ms-lg-3">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Daftar Ruangan</a></li>
                <li class="nav-item"><a class="nav-link" href="bookings.php"><?= $user['role'] === 'admin' ? 'Data Peminjaman' : 'Peminjaman Saya' ?></a></li>
                <?php if ($user['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="reports.php">Laporan</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-secondary d-none d-md-inline"><i class="bi bi-person-circle me-1"></i><?= e($user['name']) ?> · <?= e(ucfirst($user['role'])) ?></span>
                <a class="btn btn-outline-secondary btn-sm" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Keluar</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php show_flash(); ?>
