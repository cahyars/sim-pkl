<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="info-card h-100">
            <div class="fw-semibold" style="font-size: 1.05rem;"><?= esc($siswa['nama'] ?? '') ?></div>
            <div class="text-muted small mb-2"><?= esc($siswa['nis'] ?? '') ?> &middot; <?= esc($siswa['kelas'] ?? '') ?> &middot; <?= esc($siswa['jurusan'] ?? '') ?></div>
            <?php if ($penempatan): ?>
                <hr class="my-2">
                <div class="small mb-1"><span class="text-muted">Guru Pembimbing:</span> <?= esc($penempatan['nama_guru']) ?></div>
                <div class="small"><span class="text-muted">Periode PKL:</span> <?= tanggal_indo($penempatan['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($penempatan['tanggal_selesai']) ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="info-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-semibold">Penilaian Akhir</div>
                <?php if ($penilaian === null): ?>
                    <span class="badge text-bg-secondary">Belum Dinilai</span>
                <?php elseif ($penilaian['status'] === 'final'): ?>
                    <span class="badge text-bg-success">Final</span>
                <?php else: ?>
                    <span class="badge text-bg-warning">Draft</span>
                <?php endif; ?>
            </div>

            <?php if ($penilaian !== null): ?>
                <div class="stat-card-value mb-1"><?= number_format((float) $penilaian['nilai_akhir'], 2) ?></div>
                <div class="text-muted small mb-3"><?= esc(\App\Models\PenilaianModel::predikat((float) $penilaian['nilai_akhir'])) ?></div>
            <?php else: ?>
                <div class="text-muted small mb-3">Belum ada nilai yang diinput.</div>
            <?php endif; ?>

            <a href="<?= site_url('pembimbing-lapangan/penilaian/' . $siswa['id']) ?>" class="btn btn-sm btn-primary" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
                <?= $penilaian === null ? 'Isi Penilaian' : ($penilaian['status'] === 'final' ? 'Lihat Penilaian' : 'Lanjutkan Penilaian') ?>
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value"><?= (int) $rekap['total'] ?></div>
            <div class="stat-card-label">Total Logbook</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-success"><?= (int) $rekap['disetujui'] ?></div>
            <div class="stat-card-label">Disetujui</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-warning"><?= (int) $rekap['menunggu_validasi'] ?></div>
            <div class="stat-card-label">Menunggu Validasi</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-info"><?= (int) $rekap['revisi'] ?></div>
            <div class="stat-card-label">Revisi</div>
        </div>
    </div>
</div>

<div class="info-card">
    <div class="fw-semibold mb-3">Logbook Terbaru <span class="text-muted small">(read-only)</span></div>

    <?php if (empty($logbookTerbaru)): ?>
        <div class="text-muted small text-center py-4">Belum ada logbook yang diajukan.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Tanggal</th>
                        <th>Uraian</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logbookTerbaru as $lb): ?>
                        <tr>
                            <td class="small"><?= tanggal_indo($lb['tanggal_kegiatan']) ?></td>
                            <td class="small text-truncate" style="max-width: 360px;"><?= esc($lb['uraian_kegiatan']) ?></td>
                            <td><span class="badge <?= logbook_status_badge($lb['status']) ?>"><?= logbook_status_label($lb['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="mt-3">
    <a href="<?= site_url('pembimbing-lapangan/siswa') ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Daftar Siswa</a>
</div>

<?= $this->endSection() ?>
