<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card" style="max-width: 720px;">
    <form action="<?= site_url('admin/tempat-pkl/' . $tempat['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Nama Perusahaan / Instansi <span class="text-danger">*</span></label>
                <input type="text" name="nama_perusahaan" class="form-control" value="<?= esc(old('nama_perusahaan', $tempat['nama_perusahaan'])) ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="2" required><?= esc(old('alamat', $tempat['alamat'])) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="no_telp" class="form-control" value="<?= esc(old('no_telp', $tempat['no_telp'])) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc(old('email', $tempat['email'])) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Bidang Usaha</label>
                <input type="text" name="bidang_usaha" class="form-control" value="<?= esc(old('bidang_usaha', $tempat['bidang_usaha'])) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Penanggung Jawab</label>
                <input type="text" name="penanggung_jawab" class="form-control" value="<?= esc(old('penanggung_jawab', $tempat['penanggung_jawab'])) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kuota Siswa</label>
                <input type="number" name="kuota" class="form-control" min="0" value="<?= esc(old('kuota', $tempat['kuota'])) ?>">
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="<?= site_url('admin/tempat-pkl') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
