<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card" style="max-width: 720px;">
    <form action="<?= site_url('admin/siswa/' . $siswa['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?= esc(old('nama', $siswa['nama'])) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">NIS</label>
                <input type="text" class="form-control" value="<?= esc($siswa['nis']) ?>" disabled>
                <div class="form-text">NIS tidak dapat diubah (dipakai sebagai username login).</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">NISN</label>
                <input type="text" name="nisn" class="form-control" value="<?= esc(old('nisn', $siswa['nisn'])) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                <input type="text" name="kelas" class="form-control" value="<?= esc(old('kelas', $siswa['kelas'])) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                <input type="text" name="jurusan" class="form-control" list="daftarJurusanList" value="<?= esc(old('jurusan', $siswa['jurusan'])) ?>" required>
                <datalist id="daftarJurusanList">
                    <?php foreach ($daftarJurusan as $j): ?>
                        <option value="<?= esc($j) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc(old('email', $siswa['email'])) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?= esc(old('no_hp', $siswa['no_hp'])) ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="form-select">
                    <option value="">-</option>
                    <?php foreach ($tahunAjaran as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= (old('tahun_ajaran_id') ?: $siswa['tahun_ajaran_id']) == $ta['id'] ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?> (<?= esc(ucfirst($ta['semester'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status PKL</label>
                <div>
                    <?php
                        $badge = ['belum_ditempatkan' => 'text-bg-warning', 'aktif' => 'text-bg-success', 'selesai' => 'text-bg-secondary'][$siswa['status_pkl']] ?? 'text-bg-secondary';
                        $label = ['belum_ditempatkan' => 'Belum Ditempatkan', 'aktif' => 'Aktif', 'selesai' => 'Selesai'][$siswa['status_pkl']] ?? $siswa['status_pkl'];
                    ?>
                    <span class="badge <?= $badge ?>"><?= $label ?></span>
                    <div class="form-text mb-0">Status ini mengikuti data penempatan PKL, tidak diedit manual.</div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"><?= esc(old('alamat', $siswa['alamat'])) ?></textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="<?= site_url('admin/siswa') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
