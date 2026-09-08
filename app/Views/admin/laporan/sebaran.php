<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari tempat PKL / bidang usaha',
    'searchWidth'       => '260px',
    'resetUrl'          => site_url('admin/laporan/sebaran'),
    'actions'           => '<a href="' . site_url('admin/laporan/sebaran/excel') . '" class="btn btn-sm btn-outline-success">Export Excel</a>'
        . '<a href="' . site_url('admin/laporan/sebaran/pdf') . '" class="btn btn-sm btn-outline-danger" target="_blank">Export PDF</a>',
]) ?>

<div class="row g-3">
    <?php if (empty($data)): ?>
        <div class="col-12 text-center text-muted py-5">
            <?= $keyword !== '' ? 'Tidak ada tempat PKL yang cocok dengan pencarian.' : 'Belum ada data tempat PKL.' ?>
        </div>
    <?php endif; ?>

    <?php foreach ($data as $t): ?>
        <?php $persen = $t['kuota'] > 0 ? (int) round(($t['terisi'] / $t['kuota']) * 100) : 0; ?>
        <div class="col-md-6">
            <div class="info-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-semibold"><?= esc($t['nama_perusahaan']) ?></div>
                    <span class="text-muted small"><?= $t['terisi'] ?>/<?= $t['kuota'] ?></span>
                </div>
                <div class="text-muted small mb-2"><?= esc($t['bidang_usaha']) ?></div>
                <div class="progress progress-thin mb-3">
                    <div class="progress-bar <?= $persen >= 100 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= min($persen, 100) ?>%"></div>
                </div>

                <?php if (empty($t['siswa'])): ?>
                    <div class="text-muted small">Belum ada siswa ditempatkan.</div>
                <?php else: ?>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach ($t['siswa'] as $s): ?>
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span><?= esc($s['nama_siswa']) ?></span>
                                <span class="text-muted"><?= esc($s['nis']) ?> &middot; <?= esc($s['kelas']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= view('partials/pagination', [
    'pager'      => $pager,
    'pagerGroup' => 'sebaran',
    'pagerKeep'  => ['q'],
    'pagerLabel' => 'tempat PKL',
]) ?>

<?= $this->endSection() ?>
