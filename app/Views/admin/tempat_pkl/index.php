<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari perusahaan/bidang/alamat',
    'searchWidth'       => '265px',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'empty'   => 'Semua Status',
            'width'   => '150px',
            'label'   => 'Status tempat',
            'options' => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'],
        ],
    ],
    'resetUrl'          => site_url('admin/tempat-pkl'),
    'actions'           => '<a href="' . site_url('admin/tempat-pkl/tambah') . '" class="btn btn-sm btn-primary">+ Tambah Tempat PKL</a>',
]) ?>

<div class="row g-3">
    <?php if (empty($tempat)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada data tempat PKL.</div>
    <?php endif; ?>
    <?php foreach ($tempat as $t): ?>
        <?php $persen = $t['kuota'] > 0 ? (int) round(($t['terisi'] / $t['kuota']) * 100) : 0; ?>
        <div class="col-md-6 col-xl-4">
            <div class="info-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-semibold"><?= esc($t['nama_perusahaan']) ?></div>
                    <span class="badge <?= $t['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $t['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                </div>
                <div class="text-muted small mb-2"><?= esc($t['bidang_usaha']) ?></div>
                <div class="text-muted small mb-3 text-truncate"><?= esc($t['alamat']) ?></div>

                <div class="mt-auto">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="progress progress-thin flex-grow-1">
                            <div class="progress-bar <?= $persen >= 100 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= min($persen, 100) ?>%"></div>
                        </div>
                        <span class="small text-muted"><?= $t['terisi'] ?>/<?= $t['kuota'] ?></span>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <a href="<?= site_url('admin/tempat-pkl/' . $t['id']) ?>" class="btn btn-sm btn-outline-secondary flex-fill">Detail</a>
                        <a href="<?= site_url('admin/tempat-pkl/' . $t['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="<?= site_url('admin/tempat-pkl/' . $t['id'] . '/hapus') ?>" method="post" data-confirm="Hapus tempat PKL ini?">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?= view('partials/pagination', [
    'pager'      => $pager,
    'pagerGroup' => 'tempat',
    'pagerKeep'  => ['q', 'status'],
    'pagerLabel' => 'tempat PKL',
]) ?>

<?= $this->endSection() ?>
