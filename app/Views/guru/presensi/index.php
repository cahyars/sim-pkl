<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card mb-3">
    <div class="fw-semibold"><?= esc($penempatan['nama_siswa']) ?></div>
    <div class="text-muted small"><?= esc($penempatan['nis']) ?> &middot; <?= esc($penempatan['kelas']) ?> &middot; <?= esc($penempatan['nama_perusahaan']) ?></div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="?bulan=<?= $bulanSebelumnya ?>" class="btn btn-sm btn-outline-secondary <?= $bulanSebelumnya < $batasBulanAwal ? 'disabled' : '' ?>">&larr;</a>
        <div class="fw-semibold">Bulan <?= esc($namaBulan) ?></div>
        <a href="?bulan=<?= $bulanBerikutnya ?>" class="btn btn-sm btn-outline-secondary <?= $bulanBerikutnya > $batasBulanAkhir ? 'disabled' : '' ?>">&rarr;</a>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('guru/siswa/' . $penempatan['siswa_id'] . '/kehadiran/excel') ?>?bulan=<?= $bulan ?>" class="btn btn-sm btn-outline-success">Export Excel</a>
        <a href="<?= site_url('guru/siswa/' . $penempatan['siswa_id'] . '/kehadiran/pdf') ?>?bulan=<?= $bulan ?>" class="btn btn-sm btn-outline-danger" target="_blank">Export PDF</a>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-success"><?= $rekap['masuk'] ?></div>
            <div class="stat-card-label">Masuk</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-warning"><?= $rekap['izin'] ?></div>
            <div class="stat-card-label">Izin</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-danger"><?= $rekap['sakit'] ?></div>
            <div class="stat-card-label">Sakit</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-muted"><?= $rekap['belum_mengisi'] ?></div>
            <div class="stat-card-label">Belum Mengisi</div>
        </div>
    </div>
</div>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Tanggal</th>
                    <th>Datang</th>
                    <th>Pulang</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($hari)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data pada bulan ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($hari as $h): ?>
                    <?php $entry = $h['entry']; ?>
                    <tr class="small <?= ($h['akhirPekan'] && $entry === null) ? 'text-muted' : '' ?>">
                        <td><?= tanggal_indo($h['tanggal'], true) ?></td>
                        <?php if ($entry !== null && $entry['status_kehadiran'] === 'masuk'): ?>
                            <td><?= esc(substr($entry['jam_mulai'] ?? '-', 0, 5)) ?></td>
                            <td><?= esc(substr($entry['jam_selesai'] ?? '-', 0, 5)) ?></td>
                            <td><span class="badge <?= logbook_status_badge($entry['status']) ?>"><?= logbook_status_label($entry['status']) ?></span></td>
                        <?php elseif ($entry !== null): ?>
                            <td>-</td><td>-</td>
                            <td><span class="badge <?= $entry['status_kehadiran'] === 'sakit' ? 'text-bg-danger' : 'text-bg-warning' ?>"><?= ucfirst($entry['status_kehadiran']) ?></span></td>
                        <?php elseif ($h['akhirPekan']): ?>
                            <td>-</td><td>-</td><td>Libur</td>
                        <?php elseif ($h['belumTiba']): ?>
                            <td>-</td><td>-</td><td>-</td>
                        <?php else: ?>
                            <td>-</td><td>-</td>
                            <td><span class="badge text-bg-secondary">Belum Mengisi</span></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="<?= site_url('guru/siswa/' . $penempatan['siswa_id']) ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Detail Siswa</a>
</div>

<?= $this->endSection() ?>
