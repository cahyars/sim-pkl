<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card" style="max-width: 720px;">
    <form action="<?= site_url('admin/siswa') ?>" method="post">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?= esc(old('nama')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">NIS <span class="text-danger">*</span></label>
                <input type="text" name="nis" class="form-control" value="<?= esc(old('nis')) ?>" required>
                <div class="form-text">Dipakai sebagai username &amp; password awal login siswa.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">NISN</label>
                <input type="text" name="nisn" class="form-control" value="<?= esc(old('nisn')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                <input type="text" name="kelas" class="form-control" list="daftarKelasList" value="<?= esc(old('kelas')) ?>" placeholder="Contoh: XII RPL 1" required>
                <datalist id="daftarKelasList">
                    <?php foreach ($daftarKelas as $k): ?>
                        <option value="<?= esc($k) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="jurusan" class="form-control" list="daftarJurusanList" value="<?= esc(old('jurusan')) ?>" required>
                <datalist id="daftarJurusanList">
                    <?php foreach ($daftarJurusan as $j): ?>
                        <option value="<?= esc($j) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" placeholder="Kosongkan untuk otomatis dari NIS">
            </div>
            <div class="col-md-6">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?= esc(old('no_hp')) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="form-select">
                    <option value="">-</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= (old('tahun_ajaran_id') ?: '') == $ta['id'] || $ta['is_active'] ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?> (<?= esc(ucfirst($ta['semester'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"><?= esc(old('alamat')) ?></textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('admin/siswa') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
