<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value"><?= $totalBimbingan ?></div>
            <div class="stat-card-label">Siswa Bimbingan Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <a href="<?= site_url('guru/validasi') ?>" class="text-decoration-none text-body">
            <div class="stat-card">
                <div class="stat-card-value text-warning"><?= $totalMenunggu ?></div>
                <div class="stat-card-label">Logbook Menunggu Validasi</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-value text-danger"><?= $totalBelumAktif ?></div>
            <div class="stat-card-label">Belum Aktif &gt; <?= $reminderHari ?> Hari</div>
        </div>
    </div>
</div>

<?php if ($grafik['adaData']): ?>
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="info-card h-100">
                <div class="fw-semibold mb-1">Grafik Progres Logbook per Siswa</div>
                <div class="text-muted small mb-3">Persentase logbook yang sudah disetujui dari total logbook yang diisi.</div>
                <div style="height: 260px;">
                    <canvas id="grafikProgres"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="info-card h-100">
                <div class="fw-semibold mb-1">Komposisi Status Logbook</div>
                <div class="text-muted small mb-3">Seluruh logbook siswa bimbingan Anda.</div>
                <div style="height: 260px;">
                    <canvas id="grafikKomposisi"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="info-card">
    <div class="fw-semibold mb-3">Progres Pengisian Logbook Siswa Bimbingan</div>

    <?php if (empty($siswaBimbingan)): ?>
        <div class="text-muted small text-center py-4">Belum ada siswa bimbingan yang ditempatkan.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Siswa</th>
                        <th>Tempat PKL</th>
                        <th>Progres Disetujui</th>
                        <th>Terakhir Isi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswaBimbingan as $s): ?>
                        <?php
                            $p = $s['penempatan'];
                            $r = $s['rekap'];
                            $persen = $r['total'] > 0 ? (int) round(($r['disetujui'] / $r['total']) * 100) : 0;
                        ?>
                        <tr>
                            <td>
                                <div class="fw-semibold small"><?= esc($p['nama_siswa']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= esc($p['nis']) ?> &middot; <?= esc($p['kelas']) ?></div>
                            </td>
                            <td class="small"><?= esc($p['nama_perusahaan']) ?></td>
                            <td style="min-width: 160px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress progress-thin flex-grow-1">
                                        <div class="progress-bar bg-success" style="width: <?= $persen ?>%"></div>
                                    </div>
                                    <span class="small text-muted"><?= $persen ?>%</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?= $r['disetujui'] ?>/<?= $r['total'] ?> logbook</div>
                            </td>
                            <td class="small"><?= $s['terakhir_isi'] ? tanggal_indo($s['terakhir_isi']) : '-' ?></td>
                            <td>
                                <?php if ($s['belum_aktif']): ?>
                                    <span class="badge text-bg-danger">Belum Aktif</span>
                                <?php else: ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= site_url('guru/siswa/' . $p['siswa_id']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php if ($grafik['adaData']): ?>
    <script src="<?= base_url('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
    <script>
        (function () {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.font.family = 'Inter, "Segoe UI", Roboto, sans-serif';
            Chart.defaults.font.size = 11;
            Chart.defaults.color = '#667479';

            var labels = <?= json_encode($grafik['label']) ?>;
            var persen = <?= json_encode($grafik['persen']) ?>;

            new Chart(document.getElementById('grafikProgres'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Logbook disetujui (%)',
                        data: persen,
                        backgroundColor: persen.map(function (v) {
                            if (v >= 80) return '#0d5c63';
                            if (v >= 50) return '#f2a541';
                            return '#dc3545';
                        }),
                        borderRadius: 6,
                        maxBarThickness: 46
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) { return ctx.parsed.y + '% disetujui'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (v) { return v + '%'; } },
                            grid: { color: '#e6e9eb' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            new Chart(document.getElementById('grafikKomposisi'), {
                type: 'doughnut',
                data: {
                    labels: ['Disetujui', 'Menunggu Validasi', 'Revisi', 'Ditolak', 'Draft'],
                    datasets: [{
                        data: <?= json_encode($grafik['komposisi']) ?>,
                        backgroundColor: ['#0d5c63', '#f2a541', '#0dcaf0', '#dc3545', '#adb5bd'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '58%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10, padding: 12, usePointStyle: true }
                        }
                    }
                }
            });
        })();
    </script>
<?php endif; ?>

<?= $this->endSection() ?>
