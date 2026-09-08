<?= $this->extend('layouts/mobile_layout') ?>

<?= $this->section('content') ?>

<?php if ($penempatan === null): ?>
    <div class="alert alert-warning">Kamu belum memiliki penempatan PKL aktif, presensi belum tersedia.</div>
<?php else: ?>

    <div class="info-card mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <a href="?bulan=<?= $bulanSebelumnya ?>"
               class="btn btn-sm btn-outline-secondary <?= $bulanSebelumnya < $batasBulanAwal ? 'disabled' : '' ?>">&larr;</a>
            <div class="fw-semibold"><?= esc($namaBulan) ?></div>
            <a href="?bulan=<?= $bulanBerikutnya ?>"
               class="btn btn-sm btn-outline-secondary <?= $bulanBerikutnya > $batasBulanAkhir ? 'disabled' : '' ?>">&rarr;</a>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="stat-card text-center py-2">
                <div class="stat-card-value text-success" style="font-size: 1.3rem;"><?= $rekap['masuk'] ?></div>
                <div class="stat-card-label" style="font-size: 0.68rem;">Masuk</div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card text-center py-2">
                <div class="stat-card-value text-warning" style="font-size: 1.3rem;"><?= $rekap['izin'] ?></div>
                <div class="stat-card-label" style="font-size: 0.68rem;">Izin</div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card text-center py-2">
                <div class="stat-card-value text-danger" style="font-size: 1.3rem;"><?= $rekap['sakit'] ?></div>
                <div class="stat-card-label" style="font-size: 0.68rem;">Sakit</div>
            </div>
        </div>
        <div class="col-3">
            <div class="stat-card text-center py-2">
                <div class="stat-card-value text-muted" style="font-size: 1.3rem;"><?= $rekap['belum_mengisi'] ?></div>
                <div class="stat-card-label" style="font-size: 0.68rem;">Belum Isi</div>
            </div>
        </div>
    </div>

    <div class="info-card p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr class="text-muted small">
                        <th>Tanggal</th>
                        <th>Datang</th>
                        <th>Pulang</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($hari)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data pada bulan ini.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($hari as $h): ?>
                        <?php
                            $entry = $h['entry'];
                            $rowClass = ($h['akhirPekan'] && $entry === null) ? 'text-muted' : '';
                        ?>
                        <tr class="<?= $rowClass ?> small">
                            <td><?= tanggal_indo($h['tanggal'], true) ?></td>
                            <?php if ($entry !== null && $entry['status_kehadiran'] === 'masuk'): ?>
                                <td><?= esc(substr($entry['jam_mulai'] ?? '-', 0, 5)) ?></td>
                                <td><?= esc(substr($entry['jam_selesai'] ?? '-', 0, 5)) ?></td>
                                <td><span class="badge <?= logbook_status_badge($entry['status']) ?>"><?= logbook_status_label($entry['status']) ?></span></td>
                            <?php elseif ($entry !== null): ?>
                                <td>-</td><td>-</td>
                                <td><span class="badge <?= $entry['status_kehadiran'] === 'sakit' ? 'text-bg-danger' : 'text-bg-warning' ?>"><?= ucfirst($entry['status_kehadiran']) ?></span></td>
                            <?php elseif ($h['akhirPekan']): ?>
                                <td>-</td><td>-</td><td>Libur</td>
                            <?php elseif ($h['belumTiba']): ?>
                                <td>-</td><td>-</td><td>-</td>
                            <?php else: ?>
                                <td>-</td><td>-</td>
                                <td><span class="badge text-bg-secondary">Belum Mengisi</span></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-light border small text-muted mt-3 mb-0">
        Jam Datang &amp; Pulang otomatis mengikuti Jam Mulai/Selesai pada logbook harian kamu.
        Kalau berstatus Izin/Sakit, kolom jam ditampilkan "-".
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
