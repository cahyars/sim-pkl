<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php if ($tahunAjaranAktif): ?>
    <?php if (! empty($isArsip)): ?>
        <div class="alert alert-warning border border-warning-subtle small mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2 shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 1.2rem;">📂</span>
                <div>
                    <strong>Mode Arsip Tahun Ajaran: <?= esc($tahunAjaranAktif['nama_tahun_ajaran']) ?> (<?= esc(ucfirst($tahunAjaranAktif['semester'])) ?>)</strong>
                    <div class="text-dark-emphasis" style="font-size: 0.76rem;">
                        Data siswa dan statistik yang ditampilkan adalah rekaman arsip periode <?= tanggal_indo($tahunAjaranAktif['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($tahunAjaranAktif['tanggal_selesai']) ?>.
                    </div>
                </div>
            </div>
            <?php
                $taAktifSekolah = (new \App\Models\TahunAjaranModel())->getAktif();
            ?>
            <?php if ($taAktifSekolah): ?>
                <form action="<?= site_url('admin/switch-tahun-ajaran') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tahun_ajaran_id" value="<?= $taAktifSekolah['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-dark">
                        &larr; Kembali ke Periode Aktif (<?= esc($taAktifSekolah['nama_tahun_ajaran']) ?>)
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-light border small mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                Tahun Ajaran Aktif: <strong><?= esc($tahunAjaranAktif['nama_tahun_ajaran']) ?> (<?= esc(ucfirst($tahunAjaranAktif['semester'])) ?>)</strong>
                &middot; <?= tanggal_indo($tahunAjaranAktif['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($tahunAjaranAktif['tanggal_selesai']) ?>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle">Periode Berjalan</span>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value"><?= $totalSiswa ?></div>
            <div class="stat-card-label">Total Siswa PKL</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-success"><?= $siswaAktif ?></div>
            <div class="stat-card-label">Sedang Aktif PKL</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-warning"><?= $siswaBelumTempat ?></div>
            <div class="stat-card-label">Belum Ditempatkan</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-value text-secondary"><?= $siswaSelesai ?></div>
            <div class="stat-card-label">Selesai PKL</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value"><?= $totalTempatPkl ?></div>
            <div class="stat-card-label">Tempat PKL Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value"><?= $totalGuru ?></div>
            <div class="stat-card-label">Guru Pembimbing</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value"><?= $totalPembimbing ?></div>
            <div class="stat-card-label">Pembimbing Lapangan</div>
        </div>
    </div>
</div>

<div class="info-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-semibold">Sebaran Siswa per Tempat PKL</div>
        <div class="text-muted small">Total kuota terisi: <?= $totalTerisi ?>/<?= $totalKuota ?></div>
    </div>

    <?php if (empty($sebaranTempat)): ?>
        <div class="text-muted small text-center py-4">Belum ada data tempat PKL.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Tempat PKL</th>
                        <th>Bidang Usaha</th>
                        <th>Kuota Terisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sebaranTempat as $t): ?>
                        <?php $persen = $t['kuota'] > 0 ? (int) round(($t['terisi'] / $t['kuota']) * 100) : 0; ?>
                        <tr>
                            <td>
                                <div class="fw-semibold small"><?= esc($t['nama_perusahaan']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= esc($t['penanggung_jawab']) ?></div>
                            </td>
                            <td class="small"><?= esc($t['bidang_usaha']) ?></td>
                            <td style="min-width: 180px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress progress-thin flex-grow-1">
                                        <div class="progress-bar <?= $persen >= 100 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= min($persen, 100) ?>%"></div>
                                    </div>
                                    <span class="small text-muted"><?= $t['terisi'] ?>/<?= $t['kuota'] ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="alert alert-light border small text-muted mt-3 mb-0">
    Fitur kelola data siswa, tempat PKL, penempatan, dan laporan akan tersedia pada tahap pengembangan berikutnya.
</div>

<?= $this->endSection() ?>
