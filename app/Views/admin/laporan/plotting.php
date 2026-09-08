<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold mb-0">Rekap Plotting Siswa PKL (Format Data PKL P2)</h5>
        <div class="text-muted small"><?= esc($periodeJudul) ?> &bull; Total Siswa Terplotting: <strong><?= $totalSiswa ?></strong></div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/laporan/plotting/excel?' . http_build_query($filter)) ?>" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Download Excel (.xlsx)
        </a>
        <a href="<?= site_url('admin/laporan/plotting/pdf?' . http_build_query($filter)) ?>" class="btn btn-sm btn-danger" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body p-2">
        <form action="<?= site_url('admin/laporan/plotting') ?>" method="get" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="tahun_ajaran_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Periode / Tahun Ajaran</option>
                    <?php foreach ($daftarTahun as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($filter['tahun_ajaran_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= esc($t['nama_tahun_ajaran']) ?> - <?= ucfirst(esc($t['semester'])) ?> <?= (int) $t['is_active'] === 1 ? '(Aktif)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="aktif" <?= ($filter['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Status: Aktif Saja</option>
                    <option value="semua" <?= ($filter['status'] ?? '') === 'semua' ? 'selected' : '' ?>>Status: Semua Penempatan</option>
                    <option value="selesai" <?= ($filter['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Status: Selesai</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari perusahaan, siswa, atau pembimbing..." value="<?= esc($filter['q'] ?? '') ?>">
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-sm btn-primary">Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.85rem;">
            <thead>
                <tr class="text-center fw-bold" style="background-color: #C6E0B4; color: #111;">
                    <th style="width: 45px;">NO</th>
                    <th style="width: 250px;">TEMPAT PKL</th>
                    <th style="width: 200px;">NAMA PEMBIMBING</th>
                    <th>NAMA SISWA</th>
                    <th style="width: 110px;">KELAS</th>
                    <th style="width: 80px;">Jumlah<br>Siswa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            Tidak ada data siswa yang di-plotting pada filter yang dipilih.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                            $jumlahSiswa = count($item['siswa']);
                            $first = true;
                        ?>
                        <?php foreach ($item['siswa'] as $s): ?>
                            <tr>
                                <?php if ($first): ?>
                                    <td class="text-center fw-bold" rowspan="<?= $jumlahSiswa ?>"><?= $no++ ?></td>
                                    <td rowspan="<?= $jumlahSiswa ?>">
                                        <div class="fw-bold"><?= esc($item['nama_perusahaan']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Instruktur DU/DI: <?= esc($item['nama_instruktur']) ?></div>
                                    </td>
                                    <td class="text-center fw-semibold" rowspan="<?= $jumlahSiswa ?>"><?= esc($item['nama_pembimbing']) ?></td>
                                <?php endif; ?>
                                <td>
                                    <span class="fw-semibold"><?= esc($s['nama_siswa']) ?></span>
                                    <span class="text-muted ms-1" style="font-size: 0.75rem;">(<?= esc($s['nis']) ?>)</span>
                                </td>
                                <td class="text-center"><?= esc($s['kelas']) ?></td>
                                <?php if ($first): ?>
                                    <td class="text-center fw-bold fs-6" rowspan="<?= $jumlahSiswa ?>"><?= $jumlahSiswa ?></td>
                                    <?php $first = false; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold text-center" style="background-color: #FFFF00; color: #000; font-size: 0.9rem;">
                    <td colspan="5" class="py-2">
                        JUMLAH SISWA PKL <?= strtoupper(esc($periodeJudul)) ?>
                    </td>
                    <td class="py-2 fs-6"><?= $totalSiswa ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="<?= site_url('admin/penempatan') ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Kelola Penempatan</a>
</div>

<?= $this->endSection() ?>
