<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<form action="<?= site_url('admin/pengaturan') ?>" method="post" style="max-width: 640px;">
    <?= csrf_field() ?>

    <div class="info-card mb-3">
        <div class="fw-semibold mb-3">Reminder Notifikasi Logbook</div>

        <div class="form-check form-switch mb-3">
            <input type="checkbox" class="form-check-input" role="switch" id="reminder_aktif" name="reminder_aktif" value="1"
                <?= ($byKunci['reminder_aktif']['nilai'] ?? '1') === '1' ? 'checked' : '' ?>>
            <label class="form-check-label" for="reminder_aktif">Aktifkan reminder otomatis</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Kirim reminder jika siswa belum isi logbook selama</label>
            <div class="input-group" style="max-width: 220px;">
                <input type="number" name="reminder_hari" class="form-control" min="1"
                    value="<?= esc(old('reminder_hari', $byKunci['reminder_hari']['nilai'] ?? '3')) ?>" required>
                <span class="input-group-text">hari</span>
            </div>
        </div>

        <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input" role="switch" id="reminder_tembusan_guru" name="reminder_tembusan_guru" value="1"
                <?= ($byKunci['reminder_tembusan_guru']['nilai'] ?? '1') === '1' ? 'checked' : '' ?>>
            <label class="form-check-label" for="reminder_tembusan_guru">Tembusan email ke guru pembimbing</label>
        </div>
    </div>

    <div class="info-card mb-3">
        <div class="fw-semibold mb-3">Upload Dokumentasi Logbook</div>

        <div class="mb-3">
            <label class="form-label">Ukuran maksimal per file</label>
            <div class="input-group" style="max-width: 220px;">
                <input type="number" name="upload_max_size" class="form-control" min="1"
                    value="<?= esc(old('upload_max_size', $byKunci['upload_max_size']['nilai'] ?? '2048')) ?>" required>
                <span class="input-group-text">KB</span>
            </div>
        </div>

        <div class="mb-0">
            <label class="form-label">Ekstensi file yang diizinkan</label>
            <input type="text" name="upload_allowed_ext" class="form-control"
                value="<?= esc(old('upload_allowed_ext', $byKunci['upload_allowed_ext']['nilai'] ?? 'jpg,jpeg,png,pdf')) ?>">
            <div class="form-text">Pisahkan dengan koma, tanpa titik. Contoh: jpg,jpeg,png,pdf</div>
        </div>
    </div>

    <div class="info-card mb-3">
        <div class="fw-semibold mb-3">Identitas Sekolah <span class="text-muted small">(dipakai di kop laporan)</span></div>

        <div class="mb-3">
            <label class="form-label">Nama Sekolah</label>
            <input type="text" name="nama_sekolah" class="form-control"
                value="<?= esc(old('nama_sekolah', $byKunci['nama_sekolah']['nilai'] ?? '')) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat Sekolah</label>
            <input type="text" name="alamat_sekolah" class="form-control"
                value="<?= esc(old('alamat_sekolah', $byKunci['alamat_sekolah']['nilai'] ?? '')) ?>">
        </div>
        <div class="mb-0">
            <label class="form-label">Nama Kepala Sekolah</label>
            <input type="text" name="kepala_sekolah" class="form-control"
                value="<?= esc(old('kepala_sekolah', $byKunci['kepala_sekolah']['nilai'] ?? '')) ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
        Simpan Pengaturan
    </button>
</form>

<?= $this->endSection() ?>
