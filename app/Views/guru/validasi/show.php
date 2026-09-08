<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php $hadir = ($logbook['status_kehadiran'] ?? 'masuk') === 'masuk'; ?>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="info-card mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-semibold"><?= esc($logbook['nama_siswa']) ?></div>
                    <div class="text-muted small"><?= esc($logbook['nis']) ?> &middot; <?= esc($logbook['kelas']) ?> &middot; <?= esc($logbook['jurusan']) ?></div>
                </div>
                <span class="badge <?= logbook_status_badge($logbook['status']) ?>"><?= logbook_status_label($logbook['status']) ?></span>
            </div>
            <hr>

            <?php if ($hadir): ?>
                <div class="small text-muted mb-1">Tanggal &amp; Jam Kegiatan</div>
                <div class="mb-3"><?= tanggal_indo($logbook['tanggal_kegiatan'], true) ?>, <?= esc(substr($logbook['jam_mulai'], 0, 5)) ?> - <?= esc(substr($logbook['jam_selesai'], 0, 5)) ?> WIB</div>
            <?php else: ?>
                <div class="small text-muted mb-1">Tanggal</div>
                <div class="mb-3">
                    <?= tanggal_indo($logbook['tanggal_kegiatan'], true) ?>
                    <span class="badge <?= $logbook['status_kehadiran'] === 'sakit' ? 'text-bg-danger' : 'text-bg-warning' ?> ms-1"><?= ucfirst($logbook['status_kehadiran']) ?></span>
                </div>
            <?php endif; ?>

            <div class="small text-muted mb-1"><?= $hadir ? 'Uraian Kegiatan' : 'Alasan Ketidakhadiran' ?></div>
            <div class="mb-3"><?= nl2br(esc($logbook['uraian_kegiatan'])) ?></div>

            <?php if ($hadir && ! empty($logbook['kendala'])): ?>
                <div class="small text-muted mb-1">Kendala</div>
                <div class="mb-3"><?= nl2br(esc($logbook['kendala'])) ?></div>
            <?php endif; ?>

            <?php if (! empty($dokumentasi)): ?>
                <div class="small text-muted mb-2"><?= $hadir ? 'Dokumentasi' : 'Bukti Ketidakhadiran' ?> (<?= count($dokumentasi) ?>)</div>
                <div class="row g-2">
                    <?php foreach ($dokumentasi as $d): ?>
                        <div class="col-4 col-md-3">
                            <a href="<?= site_url('guru/validasi/dokumentasi/' . $d['id']) ?>" target="_blank" class="d-block text-decoration-none">
                                <?php if (str_starts_with($d['file_type'], 'image/')): ?>
                                    <img src="<?= site_url('guru/validasi/dokumentasi/' . $d['id']) ?>" class="img-fluid rounded border" style="aspect-ratio: 1; object-fit: cover;" alt="<?= esc($d['file_name']) ?>">
                                <?php else: ?>
                                    <div class="border rounded d-flex align-items-center justify-content-center text-muted" style="aspect-ratio: 1; background: #f4f6f7;">PDF</div>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <?php if ($logbook['status'] === 'menunggu_validasi'): ?>
            <div class="info-card">
                <div class="fw-semibold mb-2">Validasi Logbook</div>
                <form id="formValidasi" method="post" action="<?= site_url('guru/validasi/' . $logbook['id'] . '/setujui') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kembali" value="<?= esc($kembali) ?>">
                    <div class="mb-3">
                        <label class="form-label">Catatan / Feedback</label>
                        <textarea name="catatan_guru" class="form-control" rows="4" placeholder="Opsional untuk persetujuan, wajib untuk revisi/tolak"><?= esc(old('catatan_guru')) ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" formaction="<?= site_url('guru/validasi/' . $logbook['id'] . '/setujui') ?>" class="btn btn-success">
                            Setujui
                        </button>
                        <button type="submit" formaction="<?= site_url('guru/validasi/' . $logbook['id'] . '/revisi') ?>" class="btn btn-outline-warning">
                            Minta Revisi
                        </button>
                        <button type="submit" formaction="<?= site_url('guru/validasi/' . $logbook['id'] . '/tolak') ?>" class="btn btn-outline-danger" data-confirm="Yakin ingin menolak logbook ini?">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="info-card">
                <div class="fw-semibold mb-2">Status Validasi</div>
                <span class="badge <?= logbook_status_badge($logbook['status']) ?> mb-3"><?= logbook_status_label($logbook['status']) ?></span>

                <?php if (! empty($logbook['catatan_guru'])): ?>
                    <div class="small text-muted mb-1">Catatan Anda</div>
                    <div class="mb-3"><?= nl2br(esc($logbook['catatan_guru'])) ?></div>
                <?php endif; ?>

                <?php if (! empty($logbook['validated_at'])): ?>
                    <div class="text-muted small">Divalidasi pada <?= tanggal_indo(substr($logbook['validated_at'], 0, 10), true) ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <a href="<?= esc($kembali) ?>" class="btn btn-link text-muted mt-2">&larr; Kembali</a>
    </div>
</div>

<?= $this->endSection() ?>
