<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<div class="info-card mb-3 text-center">
    <?php if (! empty($siswa['foto'])): ?>
        <img src="<?= site_url('siswa/profil/foto/' . $siswa['foto']) ?>" alt="Foto profil"
             class="rounded-circle mb-2" style="width: 88px; height: 88px; object-fit: cover;">
    <?php else: ?>
        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-2"
             style="width: 88px; height: 88px; font-size: 2rem; color: var(--simpkl-text-muted);">
            &#128100;
        </div>
    <?php endif; ?>
    <div class="fw-semibold" style="font-size: 1.05rem;"><?= esc($siswa['nama']) ?></div>
    <div class="text-muted small"><?= esc($siswa['nis']) ?> &middot; <?= esc($siswa['kelas']) ?></div>
</div>

<div class="fw-semibold mb-2">Data Diri</div>
<form action="<?= site_url('siswa/profil') ?>" method="post" enctype="multipart/form-data" class="mb-4">
    <?= csrf_field() ?>

    <div class="info-card">
        <div class="mb-3">
            <label class="form-label">Foto Profil</label>
            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
            <div class="form-text">JPG/PNG, maksimal 1 MB.</div>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <label class="form-label text-muted small mb-1">NIS</label>
                <input type="text" class="form-control" value="<?= esc($siswa['nis']) ?>" disabled>
            </div>
            <div class="col-6">
                <label class="form-label text-muted small mb-1">Kelas</label>
                <input type="text" class="form-control" value="<?= esc($siswa['kelas']) ?>" disabled>
            </div>
        </div>
        <div class="form-text mb-3">NIS, nama, kelas, dan jurusan hanya bisa diubah oleh Admin/Tata Usaha PKL.</div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= esc(old('email', $siswa['email'])) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" name="no_hp" class="form-control" value="<?= esc(old('no_hp', $siswa['no_hp'])) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= esc(old('alamat', $siswa['alamat'])) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
            Simpan Perubahan
        </button>
    </div>
</form>

<div class="fw-semibold mb-2">Ubah Password</div>
<form action="<?= site_url('siswa/profil/password') ?>" method="post">
    <?= csrf_field() ?>
    <div class="info-card">
        <div class="mb-3">
            <label class="form-label">Password Lama</label>
            <input type="password" name="password_lama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password_baru" class="form-control" minlength="6" required>
            <div class="form-text">Minimal 6 karakter.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_baru_konfirmasi" class="form-control" minlength="6" required>
        </div>
        <button type="submit" class="btn btn-outline-secondary w-100">
            Ganti Password
        </button>
    </div>
</form>

<?= $this->endSection() ?>
