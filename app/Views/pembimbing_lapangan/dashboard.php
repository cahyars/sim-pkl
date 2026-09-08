<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value"><?= $totalBimbingan ?></div>
            <div class="stat-card-label">Siswa Bimbingan</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value text-success"><?= $totalSudahDinilai ?></div>
            <div class="stat-card-label">Sudah Dinilai</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value text-warning"><?= $totalBimbingan - $totalSudahDinilai ?></div>
            <div class="stat-card-label">Belum Dinilai</div>
        </div>
    </div>
</div>

<div class="info-card">
    <div class="fw-semibold mb-3">Siswa PKL Bimbingan Anda</div>

    <?php if (empty($siswaBimbingan)): ?>
        <div class="text-muted small text-center py-4">Belum ada siswa yang ditempatkan di bawah bimbingan Anda.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Siswa</th>
                        <th>Periode PKL</th>
                        <th>Status Penilaian</th>
                        <th>Nilai Akhir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswaBimbingan as $s): ?>
                        <?php $p = $s['penempatan']; $nilai = $s['penilaian']; ?>
                        <tr>
                            <td>
                                <div class="fw-semibold small"><?= esc($p['nama_siswa']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= esc($p['nis']) ?> &middot; <?= esc($p['kelas']) ?></div>
                            </td>
                            <td class="small"><?= tanggal_indo($p['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($p['tanggal_selesai']) ?></td>
                            <td>
                                <?php if ($nilai === null): ?>
                                    <span class="badge text-bg-secondary">Belum Dinilai</span>
                                <?php elseif ($nilai['status'] === 'final'): ?>
                                    <span class="badge text-bg-success">Final</span>
                                <?php else: ?>
                                    <span class="badge text-bg-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="small">
                                <?php if ($nilai && $nilai['status'] === 'final'): ?>
                                    <span class="fw-semibold"><?= number_format((float) $nilai['nilai_akhir'], 2) ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= site_url('pembimbing-lapangan/siswa/' . $p['siswa_id']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
