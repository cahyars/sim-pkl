<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<?php $hadir = ($logbook['status_kehadiran'] ?? 'masuk') === 'masuk'; ?>

<div class="info-card mb-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="fw-semibold"><?= tanggal_indo($logbook['tanggal_kegiatan'], true) ?></div>
        <span class="badge <?= logbook_status_badge($logbook['status']) ?>"><?= logbook_status_label($logbook['status']) ?></span>
    </div>

    <?php if ($hadir): ?>
        <div class="text-muted small mb-3"><?= esc(substr($logbook['jam_mulai'], 0, 5)) ?> - <?= esc(substr($logbook['jam_selesai'], 0, 5)) ?> WIB</div>
    <?php else: ?>
        <span class="badge <?= $logbook['status_kehadiran'] === 'sakit' ? 'text-bg-danger' : 'text-bg-warning' ?> mb-3"><?= ucfirst($logbook['status_kehadiran']) ?></span>
    <?php endif; ?>

    <div class="mb-3">
        <div class="text-muted small mb-1"><?= $hadir ? 'Uraian Kegiatan' : 'Alasan Ketidakhadiran' ?></div>
        <div class="small"><?= nl2br(esc($logbook['uraian_kegiatan'])) ?></div>
    </div>

    <?php if ($hadir && ! empty($logbook['kendala'])): ?>
        <div class="mb-3">
            <div class="text-muted small mb-1">Kendala</div>
            <div class="small"><?= nl2br(esc($logbook['kendala'])) ?></div>
        </div>
    <?php endif; ?>

    <?php if (! empty($logbook['catatan_guru'])): ?>
        <div class="alert <?= $logbook['status'] === 'ditolak' ? 'alert-danger' : 'alert-info' ?> small mb-0">
            <div class="fw-semibold mb-1">Catatan Guru Pembimbing</div>
            <?= nl2br(esc($logbook['catatan_guru'])) ?>
        </div>
    <?php endif; ?>
</div>

<?php if (! empty($dokumentasi)): ?>
    <div class="info-card mb-3">
        <div class="fw-semibold small mb-2"><?= $hadir ? 'Dokumentasi' : 'Bukti Ketidakhadiran' ?> (<?= count($dokumentasi) ?>)</div>
        <div class="row g-2">
            <?php foreach ($dokumentasi as $d): ?>
                <div class="col-4">
                    <a href="<?= site_url('siswa/logbook/dokumentasi/' . $d['id']) ?>" target="_blank" class="d-block text-decoration-none">
                        <?php if (str_starts_with($d['file_type'], 'image/')): ?>
                            <img src="<?= site_url('siswa/logbook/dokumentasi/' . $d['id']) ?>" class="img-fluid rounded border" style="aspect-ratio: 1; object-fit: cover;" alt="<?= esc($d['file_name']) ?>">
                        <?php else: ?>
                            <div class="border rounded d-flex align-items-center justify-content-center text-muted" style="aspect-ratio: 1; background: #f4f6f7;">PDF</div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (in_array($logbook['status'], ['draft', 'revisi'], true)): ?>
    <a href="<?= site_url('siswa/logbook/' . $logbook['id'] . '/edit') ?>" class="btn btn-primary w-100" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
        Edit Logbook
    </a>
<?php endif; ?>

<a href="<?= site_url('siswa/logbook') ?>" class="btn btn-link w-100 text-muted mt-2">&larr; Kembali ke Riwayat</a>

<?= $this->endSection() ?>
