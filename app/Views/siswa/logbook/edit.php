<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<div class="info-card mb-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <div class="fw-semibold small"><?= tanggal_indo($logbook['tanggal_kegiatan'], true) ?></div>
        <span class="badge <?= logbook_status_badge($logbook['status']) ?>"><?= logbook_status_label($logbook['status']) ?></span>
    </div>
    <div class="text-muted small">Tanggal kegiatan tidak dapat diubah.</div>
</div>

<?php if (! empty($logbook['catatan_guru'])): ?>
    <div class="alert alert-warning small">
        <div class="fw-semibold mb-1">Catatan Guru Pembimbing</div>
        <?= nl2br(esc($logbook['catatan_guru'])) ?>
    </div>
<?php endif; ?>

<form action="<?= site_url('siswa/logbook/' . $logbook['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php $statusLama = old('status_kehadiran', $logbook['status_kehadiran'] ?? 'masuk'); ?>

    <div class="mb-3">
        <label class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
        <select name="status_kehadiran" id="statusKehadiran" class="form-select" required>
            <option value="masuk" <?= $statusLama === 'masuk' ? 'selected' : '' ?>>Masuk</option>
            <option value="sakit" <?= $statusLama === 'sakit' ? 'selected' : '' ?>>Sakit</option>
            <option value="izin" <?= $statusLama === 'izin' ? 'selected' : '' ?>>Izin</option>
        </select>
    </div>

    <div id="blokJamKegiatan" class="row g-2 mb-3">
        <div class="col-6">
            <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
            <input type="time" name="jam_mulai" id="inputJamMulai" class="form-control" value="<?= esc(old('jam_mulai', substr($logbook['jam_mulai'] ?? '08:00', 0, 5))) ?>" required>
        </div>
        <div class="col-6">
            <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
            <input type="time" name="jam_selesai" id="inputJamSelesai" class="form-control" value="<?= esc(old('jam_selesai', substr($logbook['jam_selesai'] ?? '16:00', 0, 5))) ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" id="labelUraian">Uraian Kegiatan <span class="text-danger">*</span></label>
        <textarea name="uraian_kegiatan" id="inputUraian" class="form-control" rows="4" required><?= esc(old('uraian_kegiatan', $logbook['uraian_kegiatan'])) ?></textarea>
    </div>

    <div id="blokKendala" class="mb-3">
        <label class="form-label">Kendala (opsional)</label>
        <textarea name="kendala" class="form-control" rows="2"><?= esc(old('kendala', $logbook['kendala'])) ?></textarea>
    </div>

    <?php if (! empty($dokumentasi)): ?>
        <div class="mb-3">
            <label class="form-label" id="labelDokumentasiTersimpan">Dokumentasi Tersimpan</label>
            <div class="row g-2">
                <?php foreach ($dokumentasi as $d): ?>
                    <div class="col-4">
                        <div class="position-relative">
                            <a href="<?= site_url('siswa/logbook/dokumentasi/' . $d['id']) ?>" target="_blank">
                                <?php if (str_starts_with($d['file_type'], 'image/')): ?>
                                    <img src="<?= site_url('siswa/logbook/dokumentasi/' . $d['id']) ?>" class="img-fluid rounded border" style="aspect-ratio: 1; object-fit: cover;" alt="<?= esc($d['file_name']) ?>">
                                <?php else: ?>
                                    <div class="border rounded d-flex align-items-center justify-content-center text-muted" style="aspect-ratio: 1; background: #f4f6f7;">PDF</div>
                                <?php endif; ?>
                            </a>
                            <form action="<?= site_url('siswa/logbook/' . $logbook['id'] . '/dokumentasi/' . $d['id'] . '/hapus') ?>" method="post" data-confirm="Hapus file ini?" class="position-absolute top-0 end-0 m-1">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger py-0 px-1" style="font-size: 0.7rem;">&times;</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label" id="labelDokumentasi">Tambah Dokumentasi (opsional)</label>
        <input type="file" name="dokumentasi[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
        <div class="form-text" id="helpDokumentasi">Format: JPG, PNG, atau PDF. Maksimal 2 MB per file.</div>
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="submit" name="aksi" value="kirim" class="btn btn-primary" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
            Kirim untuk Validasi
        </button>
        <button type="submit" name="aksi" value="draft" class="btn btn-outline-secondary">
            Simpan sebagai Draft
        </button>
        <a href="<?= site_url('siswa/logbook/' . $logbook['id']) ?>" class="btn btn-link text-muted">Batal</a>
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
                inputUraian.placeholder = '';
                labelDokumentasi.textContent = 'Tambah Dokumentasi (opsional)';
                helpDokumentasi.textContent = 'Format: JPG, PNG, atau PDF. Maksimal 2 MB per file.';
            } else {
                labelUraian.innerHTML = 'Alasan Ketidakhadiran <span class="text-danger">*</span>';
                inputUraian.placeholder = 'Jelaskan alasan tidak masuk PKL hari ini (min. 10 karakter)';
                labelDokumentasi.textContent = 'Tambah Bukti Ketidakhadiran (opsional)';
                helpDokumentasi.textContent = 'Contoh: foto surat dokter atau surat izin. Format JPG, PNG, atau PDF, maksimal 2 MB.';
            }
        }

        select.addEventListener('change', terapkan);
        terapkan();
    })();
</script>

<?= $this->endSection() ?>
