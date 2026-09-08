<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php $persen = $tempat['kuota'] > 0 ? (int) round(($tempat['terisi'] / $tempat['kuota']) * 100) : 0; ?>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="info-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-semibold" style="font-size: 1.05rem;"><?= esc($tempat['nama_perusahaan']) ?></div>
                <span class="badge <?= $tempat['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $tempat['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
            </div>
            <div class="text-muted small mb-3"><?= esc($tempat['alamat']) ?></div>
            <div class="row small">
                <div class="col-6 mb-2"><span class="text-muted">Bidang Usaha:</span> <?= esc($tempat['bidang_usaha']) ?: '-' ?></div>
                <div class="col-6 mb-2"><span class="text-muted">Penanggung Jawab:</span> <?= esc($tempat['penanggung_jawab']) ?: '-' ?></div>
                <div class="col-6 mb-2"><span class="text-muted">No. Telepon:</span> <?= esc($tempat['no_telp']) ?: '-' ?></div>
                <div class="col-6 mb-2"><span class="text-muted">Email:</span> <?= esc($tempat['email']) ?: '-' ?></div>
            </div>
            <a href="<?= site_url('admin/tempat-pkl/' . $tempat['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary mt-2">Edit Data</a>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="info-card h-100">
            <div class="fw-semibold mb-2">Kuota Siswa</div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="progress progress-thin flex-grow-1">
                    <div class="progress-bar <?= $persen >= 100 ? 'bg-danger' : 'bg-success' ?>" style="width: <?= min($persen, 100) ?>%"></div>
                </div>
                <span class="small text-muted"><?= $tempat['terisi'] ?>/<?= $tempat['kuota'] ?></span>
            </div>
            <div class="text-muted small">Siswa PKL aktif saat ini di tempat ini.</div>
        </div>
    </div>
</div>

<div class="info-card">
    <div class="fw-semibold mb-3">Pembimbing Lapangan</div>

    <?= view('partials/table_toolbar', [
        'searchName'        => 'q',
        'searchValue'       => $keyword,
        'searchPlaceholder' => 'Cari nama/jabatan/email',
        'searchWidth'       => '230px',
        'resetUrl'          => site_url('admin/tempat-pkl/' . $tempat['id']),
        'actions'           => '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPembimbing">+ Tambah Pembimbing</button>',
    ]) ?>

    <?php if (empty($pembimbing)): ?>
        <div class="text-muted small text-center py-4"><?= $keyword !== '' ? 'Tidak ada pembimbing lapangan yang cocok.' : 'Belum ada data pembimbing lapangan di tempat ini.' ?></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Kontak</th>
                        <th>Akun Login</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pembimbing as $p): ?>
                        <tr>
                            <td class="fw-semibold small"><?= esc($p['nama']) ?></td>
                            <td class="small"><?= esc($p['jabatan']) ?: '-' ?></td>
                            <td class="small"><?= esc($p['no_hp']) ?: '-' ?><?= $p['email'] ? '<br><span class="text-muted">' . esc($p['email']) . '</span>' : '' ?></td>
                            <td>
                                <?php if (! $p['user_id']): ?>
                                    <span class="badge text-bg-secondary">Tidak Ada</span>
                                <?php elseif ((int) $p['akun_aktif'] === 1): ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditPembimbing<?= $p['id'] ?>">Edit</button>
                                <?php if ($p['user_id']): ?>
                                    <form action="<?= site_url('admin/pembimbing-lapangan/' . $p['id'] . '/reset-password') ?>" method="post" class="d-inline" data-confirm="Reset password pembimbing lapangan ini ke default (sama dengan username)?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Reset Password</button>
                                    </form>
                                    <form action="<?= site_url('admin/pembimbing-lapangan/' . $p['id'] . '/toggle-aktif') ?>" method="post" class="d-inline" data-confirm="Ubah status aktif akun pembimbing lapangan ini?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"><?= (int) $p['akun_aktif'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                                    </form>
                                <?php endif; ?>
                                <form action="<?= site_url('admin/pembimbing-lapangan/' . $p['id'] . '/hapus') ?>" method="post" class="d-inline" data-confirm="Hapus pembimbing lapangan ini?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditPembimbing<?= $p['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= site_url('admin/pembimbing-lapangan/' . $p['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h6 class="modal-title">Edit Pembimbing Lapangan</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="nama" class="form-control" value="<?= esc($p['nama']) ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Jabatan</label>
                                                <input type="text" name="jabatan" class="form-control" value="<?= esc($p['jabatan']) ?>">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">No. HP</label>
                                                <input type="text" name="no_hp" class="form-control" value="<?= esc($p['no_hp']) ?>">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= esc($p['email']) ?>">
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

        <?= view('partials/pagination', [
            'pager'      => $pager,
            'pagerGroup' => 'pembimbing',
            'pagerKeep'  => ['q'],
            'pagerLabel' => 'pembimbing lapangan',
        ]) ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalTambahPembimbing" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/tempat-pkl/' . $tempat['id'] . '/pembimbing') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Pembimbing Lapangan</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="pembimbingEmail">
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="buat_akun" value="1" class="form-check-input" id="buatAkunCheck">
                        <label class="form-check-label" for="buatAkunCheck">Buatkan akun login untuk pembimbing ini</label>
                    </div>
                    <div id="akunFields" class="d-none">
                        <div class="mb-2">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control">
                            <div class="form-text">Password awal akan sama dengan username. Email wajib diisi jika membuat akun.</div>
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

<script>
    (function () {
        var checkbox = document.getElementById('buatAkunCheck');
        var fields   = document.getElementById('akunFields');
        if (checkbox && fields) {
            checkbox.addEventListener('change', function () {
                fields.classList.toggle('d-none', !checkbox.checked);
            });
        }
    })();
</script>

<?= $this->endSection() ?>
