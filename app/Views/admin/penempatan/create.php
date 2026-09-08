<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card" style="max-width: 720px;">
    <?php if (empty($siswa)): ?>
        <div class="alert alert-warning mb-0">
            Semua siswa sudah memiliki penempatan PKL aktif. Tidak ada siswa yang bisa ditempatkan saat ini.
        </div>
    <?php else: ?>
        <form action="<?= site_url('admin/penempatan') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Siswa <span class="text-danger">*</span></label>
                    <select name="siswa_id" class="form-select" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php foreach ($siswa as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= old('siswa_id') == $s['id'] ? 'selected' : '' ?>>
                                <?= esc($s['nama']) ?> &mdash; <?= esc($s['nis']) ?> (<?= esc($s['kelas']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tempat PKL <span class="text-danger">*</span></label>
                    <select name="tempat_pkl_id" id="selectTempat" class="form-select" required>
                        <option value="">-- Pilih Tempat PKL --</option>
                        <?php foreach ($tempat as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= old('tempat_pkl_id') == $t['id'] ? 'selected' : '' ?>>
                                <?= esc($t['nama_perusahaan']) ?> (<?= $t['terisi'] ?>/<?= $t['kuota'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Guru Pembimbing <span class="text-danger">*</span></label>
                    <select name="guru_pembimbing_id" class="form-select" required>
                        <option value="">-- Pilih Guru Pembimbing --</option>
                        <?php foreach ($guru as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= old('guru_pembimbing_id') == $g['id'] ? 'selected' : '' ?>><?= esc($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Pembimbing Lapangan</label>
                    <select name="pembimbing_lapangan_id" id="selectPembimbing" class="form-select">
                        <option value="">-- Pilih setelah menentukan Tempat PKL --</option>
                        <?php foreach ($pembimbing as $p): ?>
                            <option value="<?= $p['id'] ?>" data-tempat="<?= $p['tempat_pkl_id'] ?>" <?= old('pembimbing_lapangan_id') == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['nama']) ?> (<?= esc($p['nama_perusahaan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Opsional, bisa dilengkapi belakangan.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= esc(old('tanggal_mulai', date('Y-m-d'))) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="<?= esc(old('tanggal_selesai')) ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"><?= esc(old('keterangan')) ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan Penempatan</button>
                <a href="<?= site_url('admin/penempatan') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    (function () {
        var selectTempat     = document.getElementById('selectTempat');
        var selectPembimbing = document.getElementById('selectPembimbing');
        if (!selectTempat || !selectPembimbing) return;

        var allOptions = Array.prototype.slice.call(selectPembimbing.options);

        function filterPembimbing() {
            var tempatId = selectTempat.value;
            selectPembimbing.innerHTML = '';
            selectPembimbing.appendChild(new Option('-- Pilih Pembimbing Lapangan --', ''));

            allOptions.forEach(function (opt) {
                if (opt.value === '' ) return;
                if (!tempatId || opt.dataset.tempat === tempatId) {
                    selectPembimbing.appendChild(opt.cloneNode(true));
                }
            });
        }

        selectTempat.addEventListener('change', filterPembimbing);
        filterPembimbing();
    })();
</script>

<?= $this->endSection() ?>
