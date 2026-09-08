<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php
$kembaliQuery = array_filter([
    'status' => $status,
    'q'      => $keyword,
], static fn ($v) => $v !== '' && $v !== null);
$kembaliUrl   = site_url('guru/validasi') . ($kembaliQuery === [] ? '' : '?' . http_build_query($kembaliQuery));
?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari siswa/NIS/kelas/uraian',
    'searchWidth'       => '260px',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'label'   => 'Status Logbook',
            'width'   => '180px',
            'options' => [
                'menunggu_validasi' => 'Menunggu Validasi',
                'disetujui'         => 'Disetujui',
                'revisi'            => 'Revisi',
                'ditolak'           => 'Ditolak',
                'semua'             => 'Semua',
            ],
        ],
    ],
    'resetUrl'          => site_url('guru/validasi'),
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Tanggal Kegiatan</th>
                    <th>Uraian</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logbook)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">
                        <?= $keyword !== '' ? 'Tidak ada logbook yang cocok dengan pencarian.' : 'Tidak ada logbook dengan status ini.' ?>
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($logbook as $lb): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($lb['nama_siswa']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($lb['nis']) ?> &middot; <?= esc($lb['kelas']) ?></div>
                        </td>
                        <td class="small"><?= tanggal_indo($lb['tanggal_kegiatan']) ?></td>
                        <td class="small text-truncate" style="max-width: 280px;"><?= esc($lb['uraian_kegiatan']) ?></td>
                        <td><span class="badge <?= logbook_status_badge($lb['status']) ?>"><?= logbook_status_label($lb['status']) ?></span></td>
                        <td class="text-end">
                            <a href="<?= site_url('guru/validasi/' . $lb['id']) ?>?kembali=<?= urlencode($kembaliUrl) ?>" class="btn btn-sm btn-outline-secondary">
                                <?= $lb['status'] === 'menunggu_validasi' ? 'Validasi' : 'Lihat' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= view('partials/pagination', [
        'pager'      => $pager,
        'pagerGroup' => 'validasi',
        'pagerKeep'  => ['q', 'status'],
        'pagerLabel' => 'logbook',
    ]) ?>
</div>

<?= $this->endSection() ?>
