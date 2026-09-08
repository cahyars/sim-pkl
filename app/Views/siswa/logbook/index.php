<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<?php if ($penempatan): ?>
    <a href="<?= site_url('siswa/logbook/tambah') ?>" class="btn btn-primary w-100 mb-3" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
        + Isi Logbook Hari Ini
    </a>
<?php else: ?>
    <div class="alert alert-warning">Kamu belum memiliki penempatan PKL aktif, logbook belum bisa diisi.</div>
<?php endif; ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari uraian kegiatan',
    'searchWidth'       => '100%',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'label'   => 'Status',
            'empty'   => 'Semua Status',
            'width'   => '155px',
            'options' => [
                'draft'             => 'Draft',
                'menunggu_validasi' => 'Menunggu Validasi',
                'disetujui'         => 'Disetujui',
                'revisi'            => 'Revisi',
                'ditolak'           => 'Ditolak',
            ],
        ],
        ['name' => 'dari', 'value' => $dari, 'type' => 'date', 'label' => 'Dari', 'width' => '145px'],
        ['name' => 'sampai', 'value' => $sampai, 'type' => 'date', 'label' => 'Sampai', 'width' => '145px'],
    ],
    'resetUrl'          => site_url('siswa/logbook'),
]) ?>

<?php if (empty($logbook)): ?>
    <div class="info-card text-center text-muted small py-4">Tidak ada logbook yang cocok dengan filter ini.</div>
<?php endif; ?>

<div class="d-flex flex-column gap-2">
    <?php foreach ($logbook as $lb): ?>
        <a href="<?= site_url('siswa/logbook/' . $lb['id']) ?>" class="info-card py-2 px-3 text-decoration-none text-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-semibold small"><?= tanggal_indo($lb['tanggal_kegiatan'], true) ?></div>
                <span class="badge <?= logbook_status_badge($lb['status']) ?>"><?= logbook_status_label($lb['status']) ?></span>
            </div>
            <div class="text-muted small text-truncate mt-1"><?= esc($lb['uraian_kegiatan']) ?></div>
            <?php if (($lb['status_kehadiran'] ?? 'masuk') === 'masuk'): ?>
                <div class="text-muted mt-1" style="font-size: 0.72rem;"><?= esc(substr($lb['jam_mulai'], 0, 5)) ?> - <?= esc(substr($lb['jam_selesai'], 0, 5)) ?></div>
            <?php else: ?>
                <span class="badge <?= $lb['status_kehadiran'] === 'sakit' ? 'text-bg-danger' : 'text-bg-warning' ?> mt-1"><?= ucfirst($lb['status_kehadiran']) ?></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>

<?= view('partials/pagination', [
    'pager'      => $pager,
    'pagerGroup' => 'logbook',
    'pagerKeep'  => ['q', 'status', 'dari', 'sampai'],
    'pagerLabel' => 'logbook',
]) ?>

<?= $this->endSection() ?>
