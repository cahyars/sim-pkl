<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari nama/NIS/NISN',
    'searchWidth'       => '230px',
    'filters'           => [
        [
            'name'    => 'kelas',
            'value'   => $kelas,
            'empty'   => 'Semua Kelas',
            'width'   => '150px',
            'label'   => 'Kelas',
            'options' => array_combine($daftarKelas, $daftarKelas) ?: [],
        ],
        [
            'name'    => 'status',
            'value'   => $status,
            'empty'   => 'Semua Status PKL',
            'width'   => '175px',
            'label'   => 'Status PKL',
            'options' => [
                'belum_ditempatkan' => 'Belum Ditempatkan',
                'aktif'             => 'Aktif',
                'selesai'           => 'Selesai',
            ],
        ],
    ],
    'resetUrl'          => site_url('admin/siswa'),
    'actions'           => '<a href="' . site_url('admin/siswa/import') . '" class="btn btn-sm btn-outline-primary">Import Excel</a>'
        . '<a href="' . site_url('admin/siswa/tambah') . '" class="btn btn-sm btn-primary">+ Tambah Siswa</a>',
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Kelas / Jurusan</th>
                    <th>Status PKL</th>
                    <th>Akun</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($siswa)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data siswa.</td></tr>
                <?php endif; ?>
                <?php foreach ($siswa as $s): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($s['nama']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;">NIS <?= esc($s['nis']) ?><?= $s['nisn'] ? ' &middot; NISN ' . esc($s['nisn']) : '' ?></div>
                        </td>
                        <td class="small"><?= esc($s['kelas']) ?><div class="text-muted" style="font-size: 0.75rem;"><?= esc($s['jurusan']) ?></div></td>
                        <td>
                            <?php
                                $badge = ['belum_ditempatkan' => 'text-bg-warning', 'aktif' => 'text-bg-success', 'selesai' => 'text-bg-secondary'][$s['status_pkl']] ?? 'text-bg-secondary';
                                $label = ['belum_ditempatkan' => 'Belum Ditempatkan', 'aktif' => 'Aktif', 'selesai' => 'Selesai'][$s['status_pkl']] ?? $s['status_pkl'];
                            ?>
                            <span class="badge <?= $badge ?>"><?= $label ?></span>
                        </td>
                        <td>
                            <?php if ((int) $s['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Aksi</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= site_url('admin/siswa/' . $s['id'] . '/edit') ?>">Edit</a></li>
                                    <li><a class="dropdown-item" href="<?= site_url('admin/penempatan/riwayat/' . $s['id']) ?>">Riwayat Penempatan</a></li>
                                    <li>
                                        <form action="<?= site_url('admin/siswa/' . $s['id'] . '/reset-password') ?>" method="post" data-confirm="Reset password siswa ini ke default (NIS)?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dropdown-item">Reset Password</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="<?= site_url('admin/siswa/' . $s['id'] . '/toggle-aktif') ?>" method="post" data-confirm="Ubah status aktif akun siswa ini?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dropdown-item"><?= (int) $s['is_active'] === 1 ? 'Nonaktifkan Akun' : 'Aktifkan Akun' ?></button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="<?= site_url('admin/siswa/' . $s['id'] . '/hapus') ?>" method="post" data-confirm="Hapus data siswa ini? Tindakan tidak dapat dibatalkan.">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('partials/pagination', [
    'pager'      => $pager,
    'pagerGroup' => 'siswa',
    'pagerKeep'  => ['q', 'kelas', 'status'],
    'pagerLabel' => 'siswa',
]) ?>

<?= $this->endSection() ?>
