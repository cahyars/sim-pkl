<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<!-- Filter Jurusan Tabs / Buttons -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body p-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex flex-wrap align-items-center gap-1">
                <span class="small text-muted me-1 fw-semibold"><i class="bi bi-filter"></i> Jurusan:</span>
                <a href="<?= site_url('admin/aspek-penilaian?jurusan=semua') ?>"
                   class="btn btn-sm <?= $jurusanFilter === 'semua' ? 'btn-dark' : 'btn-outline-secondary' ?>">
                    Semua (<?= count($daftar) ?>)
                </a>
                <?php foreach ($daftarJurusan as $j): ?>
                    <a href="<?= site_url('admin/aspek-penilaian?jurusan=' . urlencode($j)) ?>"
                       class="btn btn-sm <?= $jurusanFilter === $j ? 'btn-primary' : 'btn-outline-secondary' ?>">
                        <?= esc($j) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahAspek">
                + Tambah Aspek / Tujuan Pembelajaran
            </button>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="small text-muted">
        <?php if ($jurusanFilter !== 'semua'): ?>
            Jurusan aktif: <strong><?= esc($jurusanFilter) ?></strong> &bull; Total bobot:
        <?php else: ?>
            Total bobot semua aspek aktif:
        <?php endif; ?>
        <span class="badge <?= $totalBobot === 100 ? 'text-bg-success' : 'text-bg-warning' ?>"><?= $totalBobot ?>%</span>
        <?php if ($totalBobot !== 100): ?>
            <span class="text-warning small">— idealnya per jurusan berjumlah 100%.</span>
        <?php endif; ?>
    </div>
</div>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr class="text-muted small">
                    <th style="width: 45px;" class="text-center">No</th>
                    <th style="width: 140px;">Jurusan</th>
                    <th>Tujuan Pembelajaran / Aspek Penilaian</th>
                    <th>Deskripsi Capaian</th>
                    <th style="width: 80px;" class="text-center">Bobot</th>
                    <th style="width: 90px;" class="text-center">Status</th>
                    <th class="text-end" style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftar)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada aspek penilaian untuk filter <strong><?= esc($jurusanFilter) ?></strong>.
                            <br><small>Silakan klik <strong>+ Tambah Aspek</strong> untuk membuat indikator penilaian.</small>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($daftar as $i => $a): ?>
                    <tr>
                        <td class="small text-center fw-semibold text-muted">
                            <?= esc($a['urutan'] ?? ($i + 1)) ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-wrap text-start" style="font-size: 0.75rem;">
                                <?= esc($a['jurusan'] ?? 'Umum') ?>
                            </span>
                        </td>
                        <td class="fw-semibold small" style="max-width: 320px;">
                            <?= esc($a['nama_aspek']) ?>
                        </td>
                        <td class="text-muted" style="font-size: 0.78rem; max-width: 250px;">
                            <?= esc($a['deskripsi']) ?: '-' ?>
                        </td>
                        <td class="small text-center fw-semibold"><?= (int) $a['bobot'] ?>%</td>
                        <td class="text-center">
                            <?php if ((int) $a['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditAspek<?= $a['id'] ?>">Edit</button>
                            <form action="<?= site_url('admin/aspek-penilaian/' . $a['id'] . '/toggle-aktif') ?>" method="post" class="d-inline" data-confirm="Ubah status aktif aspek penilaian ini?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-secondary"><?= (int) $a['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                            </form>
                            <form action="<?= site_url('admin/aspek-penilaian/' . $a['id'] . '/hapus') ?>" method="post" class="d-inline" data-confirm="Hapus aspek penilaian ini? Tindakan tidak dapat dibatalkan.">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Aspek -->
                    <div class="modal fade" id="modalEditAspek<?= $a['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="<?= site_url('admin/aspek-penilaian/' . $a['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h6 class="modal-title">Edit Aspek / Tujuan Pembelajaran</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-8 mb-2">
                                                <label class="form-label small fw-semibold">Jurusan <span class="text-danger">*</span></label>
                                                <input type="text" name="jurusan" class="form-control form-control-sm" list="listJurusan" value="<?= esc($a['jurusan'] ?? 'Umum') ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label small fw-semibold">No Urut</label>
                                                <input type="number" name="urutan" class="form-control form-control-sm" value="<?= esc($a['urutan'] ?? 1) ?>" min="1">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Tujuan Pembelajaran / Nama Aspek <span class="text-danger">*</span></label>
                                            <textarea name="nama_aspek" class="form-control form-control-sm" rows="2" required><?= esc($a['nama_aspek']) ?></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Deskripsi / Ruang Lingkup Materi</label>
                                            <textarea name="deskripsi" class="form-control form-control-sm" rows="2"><?= esc($a['deskripsi']) ?></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Bobot Penilaian (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="bobot" class="form-control form-control-sm" min="1" max="100" value="<?= (int) $a['bobot'] ?>" required>
                                            <div class="form-text small">Total bobot seluruh aspek pada satu jurusan idealnya berjumlah 100%.</div>
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

<div class="alert alert-light border small text-muted mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    <strong>Panduan Format Nilai Dinamis:</strong> Aspek penilaian diatur per jurusan. Saat pembimbing lapangan membuka form penilaian untuk seorang siswa, sistem akan otomatis memuat aspek yang sesuai dengan jurusan siswa tersebut (misal Akuntansi memuat 6 aspek, TKJ memuat 8 aspek). Aspek bertanda <em>Umum</em> dipakai sebagai fallback jika suatu jurusan belum dikonfigurasi.
</div>

<!-- Modal Tambah Aspek -->
<div class="modal fade" id="modalTambahAspek" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/aspek-penilaian') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Aspek / Tujuan Pembelajaran</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <label class="form-label small fw-semibold">Jurusan <span class="text-danger">*</span></label>
                            <input type="text" name="jurusan" class="form-control form-control-sm" list="listJurusan" value="<?= $jurusanFilter !== 'semua' ? esc($jurusanFilter) : 'Umum' ?>" placeholder="Pilih atau ketik nama jurusan" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small fw-semibold">No Urut</label>
                            <input type="number" name="urutan" class="form-control form-control-sm" value="1" min="1">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Tujuan Pembelajaran / Nama Aspek <span class="text-danger">*</span></label>
                        <textarea name="nama_aspek" class="form-control form-control-sm" rows="2" placeholder="Contoh: Peserta didik mampu memahami etika profesi di bidang akuntansi" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Deskripsi / Ruang Lingkup Materi</label>
                        <textarea name="deskripsi" class="form-control form-control-sm" rows="2" placeholder="Penjelasan atau indikator penilaian yang membantu pembimbing lapangan"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Bobot Penilaian (%) <span class="text-danger">*</span></label>
                        <input type="number" name="bobot" class="form-control form-control-sm" min="1" max="100" value="15" required>
                        <div class="form-text small">Total bobot seluruh aspek aktif per jurusan idealnya berjumlah 100%.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success">Simpan Aspek</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Datalist Saran Jurusan -->
<datalist id="listJurusan">
    <?php foreach ($daftarJurusan as $j): ?>
        <option value="<?= esc($j) ?>">
    <?php endforeach; ?>
</datalist>

<?= $this->endSection() ?>
