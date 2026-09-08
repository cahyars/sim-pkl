<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari tahun ajaran',
    'searchWidth'       => '210px',
    'filters'           => [
        [
            'name'    => 'semester',
            'value'   => $semester,
            'empty'   => 'Semua Semester',
            'width'   => '165px',
            'label'   => 'Semester',
            'options' => ['ganjil' => 'Ganjil', 'genap' => 'Genap'],
        ],
    ],
    'resetUrl'          => site_url('admin/tahun-ajaran'),
    'actions'           => '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Tahun Ajaran</button>',
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Tahun Ajaran</th>
                    <th>Semester</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftar)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data tahun ajaran.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $t): ?>
                    <tr>
                        <td class="fw-semibold small"><?= esc($t['nama_tahun_ajaran']) ?></td>
                        <td class="small"><?= esc(ucfirst($t['semester'])) ?></td>
                        <td class="small"><?= tanggal_indo($t['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($t['tanggal_selesai']) ?></td>
                        <td>
                            <?php if ((int) $t['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $t['id'] ?>">Edit</button>
                            <?php if ((int) $t['is_active'] !== 1): ?>
                                <form action="<?= site_url('admin/tahun-ajaran/' . $t['id'] . '/aktifkan') ?>" method="post" class="d-inline" data-confirm="Jadikan tahun ajaran ini sebagai periode aktif? Siswa baru akan mengikuti periode ini secara default.">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success">Jadikan Aktif</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit<?= $t['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= site_url('admin/tahun-ajaran/' . $t['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h6 class="modal-title">Edit Tahun Ajaran</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label">Tahun Ajaran</label>
                                                <input type="text" name="nama_tahun_ajaran" class="form-control" value="<?= esc($t['nama_tahun_ajaran']) ?>" placeholder="2026/2027" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Semester</label>
                                                <select name="semester" class="form-select" required>
                                                    <option value="ganjil" <?= $t['semester'] === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                                    <option value="genap" <?= $t['semester'] === 'genap' ? 'selected' : '' ?>>Genap</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label">Tanggal Mulai</label>
                                                <input type="date" name="tanggal_mulai" class="form-control" value="<?= esc($t['tanggal_mulai']) ?>">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Tanggal Selesai</label>
                                                <input type="date" name="tanggal_selesai" class="form-control" value="<?= esc($t['tanggal_selesai']) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('partials/pagination', [
    'pager'      => $pager,
    'pagerGroup' => 'tahun',
    'pagerKeep'  => ['q', 'semester'],
    'pagerLabel' => 'tahun ajaran',
]) ?>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/tahun-ajaran') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Tahun Ajaran</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama_tahun_ajaran" class="form-control" placeholder="2026/2027" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select" required>
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="jadikan_aktif" value="1" class="form-check-input" id="jadikanAktif">
                        <label class="form-check-label" for="jadikanAktif">Jadikan periode aktif sekarang</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
