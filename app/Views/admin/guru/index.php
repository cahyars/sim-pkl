<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari nama/NIP/jurusan/email',
    'searchWidth'       => '250px',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'empty'   => 'Semua Akun',
            'width'   => '150px',
            'label'   => 'Status akun',
            'options' => ['aktif' => 'Akun Aktif', 'nonaktif' => 'Akun Nonaktif'],
        ],
    ],
    'resetUrl'          => site_url('admin/guru'),
    'actions'           => '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">+ Tambah Guru Pembimbing</button>',
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jurusan Diampu</th>
                    <th>Kontak</th>
                    <th>Akun</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($guru)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data guru pembimbing.</td></tr>
                <?php endif; ?>
                <?php foreach ($guru as $g): ?>
                    <tr>
                        <td class="fw-semibold small"><?= esc($g['nama']) ?></td>
                        <td class="small"><?= esc($g['nip']) ?: '-' ?></td>
                        <td class="small"><?= esc($g['jurusan_ampu']) ?: '-' ?></td>
                        <td class="small"><?= esc($g['no_hp']) ?: '-' ?><br><span class="text-muted"><?= esc($g['email']) ?></span></td>
                        <td>
                            <?php if ((int) $g['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditGuru<?= $g['id'] ?>">Edit</button>
                            <form action="<?= site_url('admin/guru/' . $g['id'] . '/reset-password') ?>" method="post" class="d-inline" data-confirm="Reset password guru ini ke default (sama dengan username)?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Reset Password</button>
                            </form>
                            <form action="<?= site_url('admin/guru/' . $g['id'] . '/toggle-aktif') ?>" method="post" class="d-inline" data-confirm="Ubah status aktif akun guru ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-secondary"><?= (int) $g['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                            </form>
                            <form action="<?= site_url('admin/guru/' . $g['id'] . '/hapus') ?>" method="post" class="d-inline" data-confirm="Hapus data guru pembimbing ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditGuru<?= $g['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= site_url('admin/guru/' . $g['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h6 class="modal-title">Edit Guru Pembimbing</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label class="form-label">Nama</label>
                                            <input type="text" name="nama" class="form-control" value="<?= esc($g['nama']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= esc($g['email']) ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">NIP</label>
                                            <input type="text" name="nip" class="form-control" value="<?= esc($g['nip']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Jurusan Diampu</label>
                                            <input type="text" name="jurusan_ampu" class="form-control" value="<?= esc($g['jurusan_ampu']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">No. HP</label>
                                            <input type="text" name="no_hp" class="form-control" value="<?= esc($g['no_hp']) ?>">
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
    'pagerGroup' => 'guru',
    'pagerKeep'  => ['q', 'status'],
    'pagerLabel' => 'guru pembimbing',
]) ?>

<div class="modal fade" id="modalTambahGuru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/guru') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Guru Pembimbing</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-text mb-2">Password awal akan sama dengan username.</div>
                    <div class="mb-2">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Jurusan Diampu</label>
                        <input type="text" name="jurusan_ampu" class="form-control" placeholder="Contoh: Rekayasa Perangkat Lunak">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control">
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
