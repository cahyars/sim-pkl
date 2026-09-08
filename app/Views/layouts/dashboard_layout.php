<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? esc($title) . ' - SIM-PKL' : 'SIM-PKL SMKN 1 Subang' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-sekolah.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="app-dashboard">

<?php
$role = session()->get('role');

$menuByRole = [
    'guru_pembimbing' => [
        ['label' => 'Dashboard',        'url' => 'guru/dashboard', 'icon' => 'dashboard'],
        ['header' => 'Bimbingan'],
        ['label' => 'Validasi Logbook', 'url' => 'guru/validasi', 'match' => 'guru/validasi', 'icon' => 'validasi'],
        ['label' => 'Siswa Bimbingan',  'url' => 'guru/siswa',    'match' => 'guru/siswa', 'icon' => 'siswa'],
        ['header' => 'Rekapitulasi'],
        ['label' => 'Laporan',          'url' => 'guru/laporan',  'match' => 'guru/laporan', 'icon' => 'laporan'],
    ],
    'pembimbing_lapangan' => [
        ['label' => 'Dashboard',       'url' => 'pembimbing-lapangan/dashboard', 'icon' => 'dashboard'],
        ['header' => 'Bimbingan Industri'],
        ['label' => 'Siswa Bimbingan', 'url' => 'pembimbing-lapangan/siswa', 'match' => 'pembimbing-lapangan/siswa', 'icon' => 'siswa'],
        ['label' => 'Penilaian PKL',   'url' => 'pembimbing-lapangan/penilaian', 'match' => 'pembimbing-lapangan/penilaian', 'icon' => 'nilai'],
    ],
    'admin' => [
        ['label' => 'Dashboard',        'url' => 'admin/dashboard', 'icon' => 'dashboard'],

        ['header' => 'Data Master'],
        ['label' => 'Kelola Jurusan',   'url' => 'admin/jurusan',         'match' => 'admin/jurusan',         'icon' => 'jurusan'],
        ['label' => 'Data Siswa',       'url' => 'admin/siswa',           'match' => 'admin/siswa',           'icon' => 'siswa'],
        ['label' => 'Guru Pembimbing',  'url' => 'admin/guru',            'match' => 'admin/guru',            'icon' => 'guru'],
        ['label' => 'Tempat PKL',       'url' => 'admin/tempat-pkl',      'match' => 'admin/tempat-pkl',      'icon' => 'tempat'],
        ['label' => 'Tahun Ajaran',     'url' => 'admin/tahun-ajaran',    'match' => 'admin/tahun-ajaran',    'icon' => 'kalender'],
        ['label' => 'Aspek Penilaian',  'url' => 'admin/aspek-penilaian', 'match' => 'admin/aspek-penilaian', 'icon' => 'nilai'],

        ['header' => 'Penempatan & Plotting'],
        ['label' => 'Penempatan PKL',   'url' => 'admin/penempatan',      'match' => 'admin/penempatan',      'icon' => 'penempatan'],
        ['label' => 'Rekap Plotting P2', 'url' => 'admin/laporan/plotting', 'match' => 'admin/laporan/plotting', 'icon' => 'rekap'],

        ['header' => 'Laporan & Sistem'],
        ['label' => 'Laporan PKL',      'url' => 'admin/laporan',         'match' => 'admin/laporan',         'icon' => 'laporan'],
        ['label' => 'Pengaturan',       'url' => 'admin/pengaturan',      'match' => 'admin/pengaturan',      'icon' => 'pengaturan'],
    ],
];

$menu       = $menuByRole[$role] ?? [];
$currentUri = trim((string) current_url(true)->getPath(), '/');

foreach ($menu as &$item) {
    if (isset($item['url'])) {
        $match = $item['match'] ?? $item['url'];
        $item['active'] = $currentUri === $item['url'] || str_starts_with($currentUri, $match . '/');
    }
}
unset($item);
?>

<div class="dashboard-shell">
    <aside class="dashboard-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('assets/img/logo-sekolah.png') ?>" alt="Logo SMKN 1 Subang" class="brand-logo">
            <div>
                <div class="sidebar-brand-title">SIM-PKL</div>
                <div class="sidebar-brand-subtitle">SMKN 1 Subang</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <?php foreach ($menu as $item): ?>
                <?php if (isset($item['header'])): ?>
                    <div class="sidebar-heading"><?= esc($item['header']) ?></div>
                <?php elseif (isset($item['divider'])): ?>
                    <hr class="sidebar-divider">
                <?php elseif (! empty($item['soon'])): ?>
                    <span class="sidebar-link disabled" title="Segera hadir pada tahap pengembangan berikutnya">
                        <?= nav_icon($item['icon'] ?? 'dashboard') ?>
                        <span class="sidebar-link-label"><?= esc($item['label']) ?></span>
                        <span class="badge text-bg-secondary ms-auto">Segera</span>
                    </span>
                <?php else: ?>
                    <a href="<?= site_url($item['url']) ?>" class="sidebar-link <?= ! empty($item['active']) ? 'active' : '' ?>" <?= ! empty($item['active']) ? 'aria-current="page"' : '' ?>>
                        <?= nav_icon($item['icon'] ?? 'dashboard') ?>
                        <span class="sidebar-link-label"><?= esc($item['label']) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="dashboard-main">
        <header class="dashboard-topbar">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="sidebarToggle">&#9776;</button>
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <h1 class="dashboard-title mb-0"><?= isset($title) ? esc($title) : 'Dashboard' ?></h1>
                <?php if ($role === 'admin' && session()->get('is_arsip')): ?>
                    <span class="badge bg-warning text-dark border border-warning-subtle d-none d-md-inline-block" style="font-size: 0.72rem;">
                        Mode Arsip
                    </span>
                <?php endif; ?>
            </div>

            <div class="dashboard-user ms-auto d-flex align-items-center gap-2">
                <?php if ($role === 'admin'): ?>
                    <?php
                        $tahunModel    = new \App\Models\TahunAjaranModel();
                        $daftarSemuaTA = $tahunModel->orderBy('is_active', 'DESC')->orderBy('nama_tahun_ajaran', 'DESC')->findAll();
                        $sesiTAId      = (int) (session()->get('tahun_ajaran_id') ?: 0);
                        $taTerpilih    = $sesiTAId > 0 ? $tahunModel->find($sesiTAId) : $tahunModel->getAktif();
                        $isArsipSesi   = $taTerpilih && (int) $taTerpilih['is_active'] !== 1;
                    ?>
                    <div class="dropdown d-none d-sm-block">
                        <button class="btn btn-sm <?= $isArsipSesi ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Ganti Periode / Lihat Arsip Tahun Ajaran">
                            <span style="font-size: 0.75rem;"><?= $isArsipSesi ? '📂 Arsip:' : '📅 Periode:' ?></span>
                            <span class="fw-semibold small"><?= esc($taTerpilih['nama_tahun_ajaran'] ?? '-') ?> (<?= esc(ucfirst($taTerpilih['semester'] ?? '-')) ?>)</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 260px; font-size: 0.85rem;">
                            <li class="dropdown-header small text-uppercase text-muted fw-bold">Pilih Periode / Arsip</li>
                            <?php foreach ($daftarSemuaTA as $st): ?>
                                <li>
                                    <form action="<?= site_url('admin/switch-tahun-ajaran') ?>" method="post" class="m-0">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="tahun_ajaran_id" value="<?= $st['id'] ?>">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center py-2 <?= ($st['id'] == ($taTerpilih['id'] ?? null)) ? 'active fw-bold' : '' ?>">
                                            <div>
                                                <div><?= esc($st['nama_tahun_ajaran']) ?> &ndash; <?= esc(ucfirst($st['semester'])) ?></div>
                                                <div class="text-muted" style="font-size: 0.68rem;"><?= tanggal_indo($st['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($st['tanggal_selesai']) ?></div>
                                            </div>
                                            <?php if ((int) $st['is_active'] === 1): ?>
                                                <span class="badge bg-success ms-2">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary ms-2">Arsip</span>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="text-end d-none d-sm-block">
                    <div class="dashboard-user-name"><?= esc(session()->get('nama')) ?></div>
                    <div class="dashboard-user-role"><?= esc(role_label($role)) ?></div>
                </div>
                <?= view('partials/notifikasi_bell', ['variant' => 'dark']) ?>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-danger" data-confirm="Yakin ingin logout?">
                    Keluar
                </a>
            </div>
        </header>

        <main class="dashboard-content">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<?= view('partials/confirm_modal') ?>

<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
