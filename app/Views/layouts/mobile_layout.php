<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?= isset($title) ? esc($title) . ' - SIM-PKL' : 'SIM-PKL SMKN 1 Subang' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-sekolah.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="app-mobile">

<header class="mobile-topbar">
    <div class="d-flex align-items-center justify-content-between px-3 py-2">
        <div class="d-flex align-items-center gap-2">
            <img src="<?= base_url('assets/img/logo-sekolah.png') ?>" alt="Logo SMKN 1 Subang" class="brand-logo">
            <div>
                <div class="topbar-title"><?= isset($title) ? esc($title) : 'Dashboard' ?></div>
                <div class="topbar-subtitle">SMKN 1 Subang</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?= view('partials/notifikasi_bell', ['variant' => 'light']) ?>
            <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-light" data-confirm="Yakin ingin logout?">
                Keluar
            </a>
        </div>
    </div>
</header>

<main class="mobile-content">
    <div class="container-fluid px-3 py-3">
        <?= view('partials/flash') ?>
        <?= $this->renderSection('content') ?>
    </div>
</main>

<?php
$currentUri = trim((string) current_url(true)->getPath(), '/');

$bottomMenu = [
    ['label' => 'Beranda', 'url' => 'siswa/dashboard', 'icon' => 'beranda', 'exact' => true],
    ['label' => 'Logbook', 'url' => 'siswa/logbook',   'icon' => 'logbook'],
    ['label' => 'Presensi', 'url' => 'siswa/presensi', 'icon' => 'kalender'],
    ['label' => 'Nilai',   'url' => 'siswa/nilai',     'icon' => 'nilai'],
    ['label' => 'Profil',  'url' => 'siswa/profil',    'icon' => 'profil'],
];
?>
<nav class="mobile-bottomnav">
    <?php foreach ($bottomMenu as $item): ?>
        <?php
        $active = empty($item['exact'])
            ? ($currentUri === $item['url'] || str_starts_with($currentUri, $item['url'] . '/'))
            : ($currentUri === $item['url']);
        ?>
        <a href="<?= site_url($item['url']) ?>" class="bottomnav-item <?= $active ? 'active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?>>
            <span class="bottomnav-icon"><?= nav_icon($item['icon']) ?></span>
            <span><?= esc($item['label']) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<?= view('partials/confirm_modal') ?>

<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
