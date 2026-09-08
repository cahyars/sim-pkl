<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="fw-bold" style="font-size: 1.15rem;"><?= esc($siswa['nama_siswa']) ?></div>
            <div class="text-muted small">
                NIS: <?= esc($siswa['nis']) ?> &middot; Kelas: <?= esc($siswa['kelas']) ?> &middot;
                <span class="badge bg-primary text-white"><?= esc($siswa['jurusan']) ?></span>
            </div>
            <div class="text-muted small mt-1">
                Tempat PKL: <strong><?= esc($siswa['nama_perusahaan']) ?></strong>
            </div>
        </div>
        <?php if ($terkunci): ?>
            <a href="<?= site_url('pembimbing-lapangan/penilaian/' . $siswa['siswa_id'] . '/pdf') ?>" class="btn btn-sm btn-outline-danger" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Unduh Lembar Nilai Resmi (PDF)
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($terkunci): ?>
    <div class="alert alert-success d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            Penilaian sudah <strong>FINAL</strong> dan terkunci. Nilai telah tampil ke siswa dan guru pembimbing.
            Anda dapat mencetak atau mengunduh <strong>Lembar Nilai Resmi (PDF 2 Halaman)</strong> melalui tombol di atas.
        </div>
    </div>

    <div class="info-card mb-3">
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <div class="stat-card-value text-primary" style="font-size: 2.8rem; line-height: 1;">
                    <?= number_format((float) $penilaian['nilai_akhir'], 2) ?>
                </div>
            </div>
            <div class="col">
                <div class="fw-bold fs-5 mb-0"><?= esc(\App\Models\PenilaianModel::predikat((float) $penilaian['nilai_akhir'])) ?></div>
                <div class="text-muted small">Rata-rata Nilai Capaian Kompetensi PKL</div>
            </div>
        </div>

        <h6 class="fw-bold mb-2">Capaian Tujuan Pembelajaran (Jurusan: <?= esc($siswa['jurusan']) ?>)</h6>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr class="text-muted small text-center">
                        <th style="width: 40px;">No</th>
                        <th>Tujuan Pembelajaran</th>
                        <th style="width: 80px;">Skor</th>
                        <th>Deskripsi Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspek as $i => $a): ?>
                        <tr>
                            <td class="small text-center"><?= $i + 1 ?></td>
                            <td class="small fw-semibold"><?= esc($a['nama_aspek']) ?></td>
                            <td class="small text-center fw-bold fs-6"><?= number_format((float) ($nilaiByAspek[$a['id']] ?? 0), 0) ?></td>
                            <td class="small text-muted"><?= esc($deskripsiByAspek[$a['id']] ?? '-') ?: '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-7">
                <div class="card h-100 border">
                    <div class="card-header bg-light py-1 fw-semibold small">Catatan / Evaluasi Pembimbing</div>
                    <div class="card-body py-2 small">
                        <?= nl2br(esc($penilaian['feedback'] ?? 'Tidak ada catatan khusus.')) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card h-100 border">
                    <div class="card-header bg-light py-1 fw-semibold small">Rekap Kehadiran Selama PKL</div>
                    <div class="card-body py-2 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Sakit:</span>
                            <strong><?= (int) ($penilaian['sakit'] ?? 0) ?> hari</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Izin:</span>
                            <strong><?= (int) ($penilaian['izin'] ?? 0) ?> hari</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tanpa Keterangan:</span>
                            <strong><?= (int) ($penilaian['tanpa_keterangan'] ?? 0) ?> hari</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border mb-2">
            <div class="card-header bg-light py-1 fw-semibold small">Data Penanda Tangan Sertifikat (Halaman 2)</div>
            <div class="card-body py-2 small">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="text-muted">Pimpinan / Direktur Perusahaan:</div>
                        <strong><?= esc($penilaian['pimpinan_nama'] ?: '-') ?></strong>
                        <div class="text-muted small">NIP/NIK/ID: <?= esc($penilaian['pimpinan_nip'] ?: '-') ?></div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="text-muted">Pembimbing Siswa / Instruktur:</div>
                        <strong><?= esc($penilaian['instruktur_nama'] ?: '-') ?></strong>
                        <div class="text-muted small">NIP/NIK/ID: <?= esc($penilaian['instruktur_nip'] ?: '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-muted small mt-2">
            Difinalisasi pada <?= tanggal_indo(substr($penilaian['finalized_at'], 0, 10), true) ?>
        </div>
    </div>
<?php else: ?>
    <div class="info-card">
        <div class="alert alert-info border-0 mb-3 small">
            <i class="bi bi-info-circle me-1"></i>
            Aspek penilaian di bawah ini dimuat <strong>secara dinamis sesuai jurusan siswa (<?= esc($siswa['jurusan']) ?>)</strong>.
            Silakan masukkan skor angka (0–100) dan deskripsi capaian (opsional).
        </div>

        <form action="<?= site_url('pembimbing-lapangan/penilaian/' . $siswa['siswa_id']) ?>" method="post">
            <?= csrf_field() ?>

            <h6 class="fw-bold mb-2">1. Capaian Tujuan Pembelajaran</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-muted small text-center">
                            <th style="width: 40px;">No</th>
                            <th>Tujuan Pembelajaran</th>
                            <th style="width: 120px;">Skor (0-100) <span class="text-danger">*</span></th>
                            <th style="width: 350px;">Deskripsi Capaian Kompetensi (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aspek as $i => $a): ?>
                            <tr>
                                <td class="small text-center fw-semibold"><?= $i + 1 ?></td>
                                <td class="small">
                                    <div class="fw-semibold"><?= esc($a['nama_aspek']) ?></div>
                                    <?php if (! empty($a['deskripsi'])): ?>
                                        <div class="text-muted" style="font-size: 0.72rem;"><?= esc($a['deskripsi']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="nilai_<?= $a['id'] ?>"
                                        class="form-control form-control-sm text-center fw-bold"
                                        min="0" max="100" step="1"
                                        value="<?= esc(old('nilai_' . $a['id'], $nilaiByAspek[$a['id']] ?? '')) ?>"
                                        required
                                        placeholder="0-100"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="deskripsi_<?= $a['id'] ?>"
                                        class="form-control form-control-sm"
                                        value="<?= esc(old('deskripsi_' . $a['id'], $deskripsiByAspek[$a['id']] ?? '')) ?>"
                                        placeholder="Misal: Sangat menguasai kompetensi ini..."
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-7">
                    <h6 class="fw-bold mb-2">2. Catatan Kualitatif / Evaluasi Pembimbing</h6>
                    <textarea name="feedback" class="form-control" rows="5" placeholder="Catatan kualitatif tentang keaktifan, sikap kerja, dan kinerja siswa selama PKL di perusahaan..."><?= esc(old('feedback', $penilaian['feedback'] ?? '')) ?></textarea>
                </div>
                <div class="col-md-5">
                    <h6 class="fw-bold mb-2">3. Rekap Kehadiran (Hari)</h6>
                    <div class="card p-3 border">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Sakit</label>
                            <input type="number" name="sakit" class="form-control form-control-sm" min="0" value="<?= esc(old('sakit', $kehadiran['sakit'])) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Izin</label>
                            <input type="number" name="izin" class="form-control form-control-sm" min="0" value="<?= esc(old('izin', $kehadiran['izin'])) ?>">
                        </div>
                        <div>
                            <label class="form-label small fw-semibold">Tanpa Keterangan</label>
                            <input type="number" name="tanpa_keterangan" class="form-control form-control-sm" min="0" value="<?= esc(old('tanpa_keterangan', $kehadiran['tanpa_keterangan'])) ?>">
                        </div>
                        <div class="form-text small mt-1">Data otomatis diringkas dari logbook & hari kerja berjalan.</div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-2">4. Data Penanda Tangan Sertifikat (Halaman 2 Lembar Nilai)</h6>
            <div class="card p-3 border mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Pimpinan / Kepala / Direktur Perusahaan</label>
                        <input type="text" name="pimpinan_nama" class="form-control form-control-sm" value="<?= esc(old('pimpinan_nama', $penilaian['pimpinan_nama'] ?? '')) ?>" placeholder="Nama Direktur / Kepala Cabang">
                        <div class="mt-2">
                            <label class="form-label small fw-semibold">NIP / NIK / ID Pimpinan</label>
                            <input type="text" name="pimpinan_nip" class="form-control form-control-sm" value="<?= esc(old('pimpinan_nip', $penilaian['pimpinan_nip'] ?? '')) ?>" placeholder="NIP / NIK (opsional)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Pembimbing Siswa / Instruktur</label>
                        <input type="text" name="instruktur_nama" class="form-control form-control-sm" value="<?= esc(old('instruktur_nama', $penilaian['instruktur_nama'] ?? $siswa['nama_pembimbing_lapangan'] ?? '')) ?>" placeholder="Nama Instruktur Lapangan">
                        <div class="mt-2">
                            <label class="form-label small fw-semibold">NIP / NIK / ID Instruktur</label>
                            <input type="text" name="instruktur_nip" class="form-control form-control-sm" value="<?= esc(old('instruktur_nip', $penilaian['instruktur_nip'] ?? '')) ?>" placeholder="NIP / NIK / ID Karyawan">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" name="aksi" value="final" class="btn btn-success" data-confirm="Nilai yang sudah difinalisasi TIDAK BISA diubah lagi. Yakin ingin memfinalisasi penilaian ini?">
                    <i class="bi bi-check-all me-1"></i> Finalisasi Penilaian
                </button>
                <button type="submit" name="aksi" value="draft" class="btn btn-outline-secondary">
                    <i class="bi bi-save me-1"></i> Simpan Draft
                </button>
                <a href="<?= site_url('pembimbing-lapangan/siswa/' . $siswa['siswa_id']) ?>" class="btn btn-link text-muted">Batal</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="mt-3">
    <a href="<?= site_url('pembimbing-lapangan/siswa/' . $siswa['siswa_id']) ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Detail Siswa</a>
</div>

<?= $this->endSection() ?>
