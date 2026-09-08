<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<div class="info-card mb-3">
    <div class="small text-muted">Tempat PKL: <strong><?= esc($penempatan['nama_perusahaan'] ?? '') ?></strong></div>
    <div class="small text-muted">Periode: <?= tanggal_indo($penempatan['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($penempatan['tanggal_selesai']) ?></div>
</div>

<form action="<?= site_url('siswa/logbook') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_kegiatan" class="form-control" value="<?= esc(old('tanggal_kegiatan', date('Y-m-d'))) ?>"
               min="<?= esc($penempatan['tanggal_mulai']) ?>" max="<?= esc(min(date('Y-m-d'), $penempatan['tanggal_selesai'])) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
        <select name="status_kehadiran" id="statusKehadiran" class="form-select" required>
            <option value="masuk" <?= old('status_kehadiran', 'masuk') === 'masuk' ? 'selected' : '' ?>>Masuk</option>
            <option value="sakit" <?= old('status_kehadiran') === 'sakit' ? 'selected' : '' ?>>Sakit</option>
            <option value="izin" <?= old('status_kehadiran') === 'izin' ? 'selected' : '' ?>>Izin</option>
        </select>
    </div>

    <div id="blokJamKegiatan" class="row g-2 mb-3">
        <div class="col-6">
            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
            <input type="time" name="jam_mulai" id="inputJamMulai" class="form-control" value="<?= esc(old('jam_mulai', '08:00')) ?>" required>
        </div>
        <div class="col-6">
            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
            <input type="time" name="jam_selesai" id="inputJamSelesai" class="form-control" value="<?= esc(old('jam_selesai', '16:00')) ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" id="labelUraian">Uraian Kegiatan <span class="text-danger">*</span></label>
        <textarea name="uraian_kegiatan" id="inputUraian" class="form-control" rows="4" placeholder="Ceritakan kegiatan yang kamu lakukan hari ini secara rinci (min. 10 karakter)" required><?= esc(old('uraian_kegiatan')) ?></textarea>
    </div>

    <div id="blokKendala" class="mb-3">
        <label class="form-label">Kendala (opsional)</label>
        <textarea name="kendala" class="form-control" rows="2" placeholder="Kendala yang dihadapi, jika ada"><?= esc(old('kendala')) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label" id="labelDokumentasi">Dokumentasi Kegiatan (opsional)</label>
        <input type="file" name="dokumentasi[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
        <div class="form-text" id="helpDokumentasi">Format: JPG, PNG, atau PDF. Maksimal 2 MB per file, bisa lebih dari satu file.</div>
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" name="aksi" value="kirim" class="btn btn-primary" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
            Kirim untuk Validasi
        </button>
        <button type="submit" name="aksi" value="draft" class="btn btn-outline-secondary">
            Simpan sebagai Draft
        </button>
        <a href="<?= site_url('siswa/logbook') ?>" class="btn btn-link text-muted">Batal</a>
    </div>
</form>

<script>
    (function () {
        var select   = document.getElementById('statusKehadiran');
        var blokJam  = document.getElementById('blokJamKegiatan');
        var jamMulai = document.getElementById('inputJamMulai');
        var jamSelesai = document.getElementById('inputJamSelesai');
        var labelUraian = document.getElementById('labelUraian');
        var inputUraian = document.getElementById('inputUraian');
        var blokKendala = document.getElementById('blokKendala');
        var labelDokumentasi = document.getElementById('labelDokumentasi');
        var helpDokumentasi  = document.getElementById('helpDokumentasi');

        function terapkan() {
            var hadir = select.value === 'masuk';

            blokJam.classList.toggle('d-none', !hadir);
            jamMulai.required = hadir;
            jamSelesai.required = hadir;

            blokKendala.classList.toggle('d-none', !hadir);

            if (hadir) {
                labelUraian.innerHTML = 'Uraian Kegiatan <span class="text-danger">*</span>';
                inputUraian.placeholder = 'Ceritakan kegiatan yang kamu lakukan hari ini secara rinci (min. 10 karakter)';
                labelDokumentasi.textContent = 'Dokumentasi Kegiatan (opsional)';
                helpDokumentasi.textContent = 'Format: JPG, PNG, atau PDF. Maksimal 2 MB per file, bisa lebih dari satu file.';
            } else {
                labelUraian.innerHTML = 'Alasan Ketidakhadiran <span class="text-danger">*</span>';
                inputUraian.placeholder = 'Jelaskan alasan tidak masuk PKL hari ini (min. 10 karakter)';
                labelDokumentasi.textContent = 'Bukti Ketidakhadiran (opsional)';
                helpDokumentasi.textContent = 'Contoh: foto surat dokter atau surat izin. Format JPG, PNG, atau PDF, maksimal 2 MB.';
            }
        }

        select.addEventListener('change', terapkan);
        terapkan();
    })();
</script>

<?= $this->endSection() ?>
