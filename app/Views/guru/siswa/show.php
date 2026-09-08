<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php $kembaliUrl = site_url('guru/siswa/' . $siswa['id']) . ($status ? '?status=' . urlencode($status) : ''); ?>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="info-card h-100">
            <div class="fw-semibold" style="font-size: 1.05rem;"><?= esc($siswa['nama'] ?? '') ?></div>
            <div class="text-muted small mb-2"><?= esc($siswa['nis'] ?? '') ?> &middot; <?= esc($siswa['kelas'] ?? '') ?> &middot; <?= esc($siswa['jurusan'] ?? '') ?></div>
            <?php if ($penempatan): ?>
                <hr class="my-2">
                <div class="small mb-1"><span class="text-muted">Tempat PKL:</span> <?= esc($penempatan['nama_perusahaan']) ?></div>
                <div class="small mb-1"><span class="text-muted">Pembimbing Lapangan:</span> <?= esc($penempatan['nama_pembimbing_lapangan'] ?? '-') ?></div>
                <div class="small mb-2"><span class="text-muted">Periode:</span> <?= tanggal_indo($penempatan['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($penempatan['tanggal_selesai']) ?></div>
                <a href="<?= site_url('guru/siswa/' . $siswa['id'] . '/kehadiran') ?>" class="btn btn-sm btn-outline-secondary">Lihat Kehadiran</a>
            <?php endif; ?>

            <?php if ($penilaian !== null && $penilaian['status'] === 'final'): ?>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Nilai Akhir PKL</div>
                        <div class="fw-semibold" style="font-size: 1.3rem; color: var(--simpkl-primary-dark);"><?= number_format((float) $penilaian['nilai_akhir'], 2) ?></div>
                    </div>
                    <span class="badge text-bg-success">Final</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row g-2 h-100">
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-card-value"><?= (int) $rekap['total'] ?></div>
                    <div class="stat-card-label">Total Logbook</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-card-value text-warning"><?= (int) $rekap['menunggu_validasi'] ?></div>
                    <div class="stat-card-label">Menunggu Validasi</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-card-value text-success"><?= (int) $rekap['disetujui'] ?></div>
                    <div class="stat-card-label">Disetujui</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-card-value text-info"><?= (int) $rekap['revisi'] ?></div>
                    <div class="stat-card-label">Revisi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari uraian kegiatan',
    'searchWidth'       => '260px',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'label'   => 'Status Logbook',
            'empty'   => 'Semua Status',
            'width'   => '180px',
            'options' => [
                'draft'             => 'Draft',
                'menunggu_validasi' => 'Menunggu Validasi',
                'disetujui'         => 'Disetujui',
                'revisi'            => 'Revisi',
                'ditolak'           => 'Ditolak',
            ],
        ],
    ],
    'resetUrl'          => site_url('guru/siswa/' . $siswa['id']),
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Tanggal</th>
                    <th>Uraian</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logbook)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada logbook.</td></tr>
                <?php endif; ?>
                <?php foreach ($logbook as $lb): ?>
                    <tr>
                        <td class="small"><?= tanggal_indo($lb['tanggal_kegiatan']) ?></td>
                        <td class="small text-truncate" style="max-width: 320px;"><?= esc($lb['uraian_kegiatan']) ?></td>
                        <td><span class="badge <?= logbook_status_badge($lb['status']) ?>"><?= logbook_status_label($lb['status']) ?></span></td>
                        <td class="text-end">
                            <?php if ($lb['status'] !== 'draft'): ?>
                                <a href="<?= site_url('guru/validasi/' . $lb['id']) ?>?kembali=<?= urlencode($kembaliUrl) ?>" class="btn btn-sm btn-outline-secondary">
                                    <?= $lb['status'] === 'menunggu_validasi' ? 'Validasi' : 'Lihat' ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= view('partials/pagination', [
        'pager'      => $pager,
        'pagerGroup' => 'logbook',
        'pagerKeep'  => ['q', 'status'],
        'pagerLabel' => 'logbook',
    ]) ?>
</div>

<div class="mt-3">
    <a href="<?= site_url('guru/siswa') ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Daftar Siswa</a>
</div>

<?= $this->endSection() ?>
