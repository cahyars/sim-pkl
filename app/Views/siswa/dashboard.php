<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<div class="mb-3">
    <div class="fw-semibold" style="font-size: 1.05rem;">Halo, <?= esc(explode(' ', session()->get('nama'))[0]) ?> 👋</div>
    <div class="text-muted small">Selamat datang di dashboard PKL kamu.</div>
</div>

<?php if ($penempatan): ?>
    <div class="info-card mb-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="fw-semibold">Tempat PKL Kamu</div>
            <span class="badge text-bg-success"><?= esc(penempatan_status_label($penempatan['status'])) ?></span>
        </div>
        <div class="fw-semibold" style="font-size: 1.05rem;"><?= esc($penempatan['nama_perusahaan']) ?></div>
        <div class="text-muted small mb-2"><?= esc($penempatan['alamat_perusahaan']) ?></div>
        <hr class="my-2">
        <div class="small mb-1"><span class="text-muted">Guru Pembimbing:</span> <?= esc($penempatan['nama_guru']) ?></div>
        <div class="small mb-1"><span class="text-muted">Pembimbing Lapangan:</span> <?= esc($penempatan['nama_pembimbing_lapangan'] ?? '-') ?></div>
        <div class="small"><span class="text-muted">Periode:</span> <?= tanggal_indo($penempatan['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($penempatan['tanggal_selesai']) ?></div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <strong>Kamu belum ditempatkan.</strong> Hubungi Admin/Tata Usaha PKL untuk informasi penempatan tempat PKL.
    </div>
<?php endif; ?>

<?php if ($penempatan): ?>
    <a href="<?= site_url('siswa/logbook/tambah') ?>" class="btn btn-primary w-100 mb-3" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
        + Isi Logbook Hari Ini
    </a>
<?php endif; ?>

<div class="fw-semibold mb-2">Rekap Logbook</div>
<div class="row g-2 mb-3">
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
            <div class="stat-card-label">Perlu Revisi</div>
        </div>
    </div>
</div>

<div class="fw-semibold mb-2">Logbook Terbaru</div>
<?php if (empty($logbookTerbaru)): ?>
    <div class="info-card text-center text-muted small py-4">
        Belum ada logbook yang diisi.
    </div>
<?php else: ?>
    <div class="d-flex flex-column gap-2 mb-3">
        <?php foreach ($logbookTerbaru as $lb): ?>
            <a href="<?= site_url('siswa/logbook/' . $lb['id']) ?>" class="info-card py-2 px-3 text-decoration-none text-body d-block">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small fw-semibold"><?= tanggal_indo($lb['tanggal_kegiatan']) ?></div>
                    <span class="badge <?= logbook_status_badge($lb['status']) ?>"><?= logbook_status_label($lb['status']) ?></span>
                </div>
                <div class="small text-muted text-truncate mt-1"><?= esc($lb['uraian_kegiatan']) ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <a href="<?= site_url('siswa/logbook') ?>" class="btn btn-outline-secondary w-100 mb-3">Lihat Semua Logbook</a>
<?php endif; ?>

<?= $this->endSection() ?>
