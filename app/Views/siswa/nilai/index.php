<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<?php if ($penilaian === null): ?>
    <div class="info-card text-center py-5">
        <div class="mb-2" style="font-size: 2rem;">&#127942;</div>
        <div class="fw-semibold mb-1">Belum Ada Nilai</div>
        <div class="text-muted small">Nilai akhir kamu akan tampil di sini setelah pembimbing lapangan menyelesaikan dan memfinalisasi penilaian.</div>
    </div>
<?php else: ?>
    <div class="info-card mb-3 text-center py-4">
        <div class="text-muted small mb-1">Nilai Akhir PKL</div>
        <div style="font-size: 2.6rem; font-weight: 750; color: var(--simpkl-primary-dark);"><?= number_format((float) $penilaian['nilai_akhir'], 2) ?></div>
        <div class="fw-semibold fs-5"><?= esc(\App\Models\PenilaianModel::predikat((float) $penilaian['nilai_akhir'])) ?></div>
        <div class="mt-3">
            <a href="<?= site_url('siswa/nilai/pdf') ?>" class="btn btn-sm btn-outline-danger px-3" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Unduh Lembar Nilai Resmi (PDF)
            </a>
        </div>
    </div>

    <div class="fw-semibold mb-2">Rincian Tujuan Pembelajaran</div>
    <div class="info-card mb-3">
        <table class="table table-sm mb-0">
            <thead>
                <tr class="text-muted small">
                    <th>Tujuan Pembelajaran</th>
                    <th class="text-end" style="width: 70px;">Skor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail as $d): ?>
                    <tr>
                        <td class="small">
                            <div><?= esc($d['nama_aspek']) ?></div>
                            <?php if (! empty($d['deskripsi'])): ?>
                                <div class="text-muted" style="font-size: 0.72rem;"><?= esc($d['deskripsi']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="small text-end fw-semibold"><?= number_format((float) $d['nilai'], 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (! empty($penilaian['feedback'])): ?>
        <div class="fw-semibold mb-2">Feedback Pembimbing Lapangan</div>
        <div class="info-card mb-3">
            <div class="small"><?= nl2br(esc($penilaian['feedback'])) ?></div>
        </div>
    <?php endif; ?>

    <div class="info-card mb-3 py-2">
        <div class="text-muted small mb-1 fw-semibold">Rekap Kehadiran:</div>
        <div class="d-flex justify-content-around text-center small">
            <div>Sakit: <strong><?= (int) ($penilaian['sakit'] ?? 0) ?></strong> hari</div>
            <div>Izin: <strong><?= (int) ($penilaian['izin'] ?? 0) ?></strong> hari</div>
            <div>Tanpa Ket: <strong><?= (int) ($penilaian['tanpa_keterangan'] ?? 0) ?></strong> hari</div>
        </div>
    </div>

    <div class="text-muted small text-center mb-4">Difinalisasi pada <?= tanggal_indo(substr($penilaian['finalized_at'], 0, 10), true) ?></div>
<?php endif; ?>

<?= $this->endSection() ?>
