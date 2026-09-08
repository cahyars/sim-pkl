<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<!-- Kartu Ringkasan Metrik Jurusan -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium">Total Jurusan</div>
                <div class="fs-4 fw-bold text-dark mt-1"><?= $totalJurusan ?></div>
                <div class="small text-muted mt-1">Konsentrasi keahlian</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium">Jurusan Aktif</div>
                <div class="fs-4 fw-bold text-success mt-1"><?= $totalAktif ?></div>
                <div class="small text-muted mt-1">Siap penempatan PKL</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium">Total Siswa Terdaftar</div>
                <div class="fs-4 fw-bold text-primary mt-1"><?= $totalSiswa ?></div>
                <div class="small text-muted mt-1">Di semua jurusan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium">TP Terkonfigurasi</div>
                <div class="fs-4 fw-bold text-info mt-1"><?= $jurusanSiapTp ?></div>
                <div class="small text-muted mt-1">Memiliki poin penilaian</div>
            </div>
        </div>
    </div>
</div>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $cari,
    'searchPlaceholder' => 'Cari kode, nama jurusan, atau kaprog...',
    'searchWidth'       => '280px',
    'resetUrl'          => site_url('admin/jurusan'),
    'actions'           => '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Jurusan</button>',
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr class="text-muted small">
                    <th style="width: 50px;">No</th>
                    <th>Kode & Jurusan</th>
                    <th>Program & Konsentrasi Keahlian</th>
                    <th>Kepala Program Keahlian</th>
                    <th class="text-center">Siswa</th>
                    <th class="text-center">Tujuan Pembelajaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 170px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jurusan)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data jurusan. Klik <strong>+ Tambah Jurusan</strong> untuk menambahkan.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($jurusan as $idx => $j): ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold" style="letter-spacing: 0.5px;">
                                    <?= esc($j['kode']) ?>
                                </span>
                                <div>
                                    <div class="fw-semibold small text-dark"><?= esc($j['nama']) ?></div>
                                    <?php if (! empty($j['bidang_keahlian'])): ?>
                                        <div class="text-muted" style="font-size: 0.72rem;"><?= esc($j['bidang_keahlian']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="small">
                            <div class="fw-medium text-dark"><?= esc($j['program_keahlian'] ?: '-') ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;">Konsentrasi: <?= esc($j['konsentrasi_keahlian'] ?: '-') ?></div>
                        </td>
                        <td class="small">
                            <div class="fw-medium text-dark"><?= esc($j['kepala_program'] ?: '-') ?></div>
                            <?php if (! empty($j['nip_kepala_program'])): ?>
                                <div class="text-muted" style="font-size: 0.75rem;">NIP. <?= esc($j['nip_kepala_program']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= site_url('admin/siswa?jurusan=' . urlencode($j['nama'])) ?>" class="badge rounded-pill text-bg-light border text-decoration-none" title="Lihat daftar siswa">
                                <?= $j['total_siswa'] ?> Siswa
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="<?= site_url('admin/aspek-penilaian?jurusan=' . urlencode($j['nama'])) ?>" class="badge rounded-pill <?= $j['total_aspek'] > 0 ? 'bg-info-subtle text-info border border-info-subtle' : 'text-bg-warning' ?> text-decoration-none" title="Kelola Tujuan Pembelajaran">
                                <?= $j['total_aspek'] ?> TP
                            </a>
                        </td>
                        <td class="text-center">
                            <?php if ((int) $j['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $j['id'] ?>" title="Edit">
                                    Edit
                                </button>
                                
                                <form action="<?= site_url('admin/jurusan/' . $j['id'] . '/toggle') ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn <?= $j['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $j['is_active'] ? 'Nonaktifkan Jurusan' : 'Aktifkan Jurusan' ?>">
                                        <?= $j['is_active'] ? 'Nonaktif' : 'Aktif' ?>
                                    </button>
                                </form>

                                <form action="<?= site_url('admin/jurusan/' . $j['id'] . '/hapus') ?>" method="post" class="d-inline" data-confirm="Yakin ingin menghapus jurusan '<?= esc($j['nama']) ?>'?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit Jurusan -->
                    <div class="modal fade" id="modalEdit<?= $j['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="<?= site_url('admin/jurusan/' . $j['id'] . '/update') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">Edit Jurusan: <?= esc($j['nama']) ?></h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-semibold">Kode <span class="text-danger">*</span></label>
                                                <input type="text" name="kode" class="form-control form-control-sm text-uppercase" value="<?= esc($j['kode']) ?>" required maxlength="20">
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label small fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control form-control-sm" value="<?= esc($j['nama']) ?>" required maxlength="100">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Bidang Keahlian</label>
                                                <input type="text" name="bidang_keahlian" class="form-control form-control-sm" value="<?= esc($j['bidang_keahlian']) ?>" placeholder="Contoh: Teknologi Informasi dan Komunikasi">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Program Keahlian</label>
                                                <input type="text" name="program_keahlian" class="form-control form-control-sm" value="<?= esc($j['program_keahlian']) ?>" placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Konsentrasi Keahlian</label>
                                                <input type="text" name="konsentrasi_keahlian" class="form-control form-control-sm" value="<?= esc($j['konsentrasi_keahlian']) ?>" placeholder="Contoh: Rekayasa Perangkat Lunak">
                                            </div>

                                            <div class="col-md-7">
                                                <label class="form-label small fw-semibold">Kepala Program Keahlian</label>
                                                <input type="text" name="kepala_program" class="form-control form-control-sm" value="<?= esc($j['kepala_program']) ?>" placeholder="Nama lengkap & gelar">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-semibold">NIP Kepala Program</label>
                                                <input type="text" name="nip_kepala_program" class="form-control form-control-sm" value="<?= esc($j['nip_kepala_program']) ?>" placeholder="Nomor Induk Pegawai">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Deskripsi / Profil Lulusan</label>
                                                <textarea name="deskripsi" class="form-control form-control-sm" rows="3"><?= esc($j['deskripsi']) ?></textarea>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="editActive<?= $j['id'] ?>" value="1" <?= $j['is_active'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label small" for="editActive<?= $j['id'] ?>">Jurusan aktif untuk penempatan PKL</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
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

<!-- Modal Tambah Jurusan Baru -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/jurusan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">+ Tambah Jurusan Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control form-control-sm text-uppercase" placeholder="Contoh: RPL" required maxlength="20" autofocus>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Rekayasa Perangkat Lunak" required maxlength="100">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Bidang Keahlian</label>
                            <input type="text" name="bidang_keahlian" class="form-control form-control-sm" placeholder="Contoh: Teknologi Informasi dan Komunikasi">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Program Keahlian</label>
                            <input type="text" name="program_keahlian" class="form-control form-control-sm" placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Konsentrasi Keahlian</label>
                            <input type="text" name="konsentrasi_keahlian" class="form-control form-control-sm" placeholder="Contoh: Rekayasa Perangkat Lunak">
                        </div>

                        <div class="col-md-7">
                            <label class="form-label small fw-semibold">Kepala Program Keahlian</label>
                            <input type="text" name="kepala_program" class="form-control form-control-sm" placeholder="Nama lengkap & gelar">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">NIP Kepala Program</label>
                            <input type="text" name="nip_kepala_program" class="form-control form-control-sm" placeholder="Nomor Induk Pegawai">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Deskripsi / Profil Singkat</label>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="3" placeholder="Keterangan singkat kompetensi lulusan jurusan..."></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="tambahActive" value="1" checked>
                                <label class="form-check-label small" for="tambahActive">Aktifkan jurusan ini</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Jurusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
