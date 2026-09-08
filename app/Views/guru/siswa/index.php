<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari siswa/NIS/kelas/perusahaan',
    'searchWidth'       => '260px',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'label'   => 'Status Penempatan',
            'width'   => '150px',
            'options' => [
                'aktif'   => 'Aktif',
                'selesai' => 'Selesai',
                'semua'   => 'Semua',
            ],
        ],
    ],
    'resetUrl'          => site_url('guru/siswa'),
]) ?>

<div class="info-card">
    <?php if (empty($siswaBimbingan)): ?>
        <div class="text-muted small text-center py-4">
            <?= $keyword !== '' ? 'Tidak ada siswa bimbingan yang cocok dengan pencarian.' : 'Tidak ada siswa bimbingan dengan status ini.' ?>
        </div>
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
                                <?php if ($p['status'] !== 'aktif'): ?>
                                    <span class="badge text-bg-secondary"><?= esc(penempatan_status_label($p['status'])) ?></span>
                                <?php elseif ($s['belum_aktif']): ?>
                                    <span class="badge text-bg-danger">Belum Aktif</span>
                                <?php else: ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= site_url('guru/siswa/' . $p['siswa_id']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="<?= site_url('guru/siswa/' . $p['siswa_id'] . '/kehadiran') ?>" class="btn btn-sm btn-outline-secondary">Kehadiran</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= view('partials/pagination', [
            'pager'      => $pager,
            'pagerGroup' => 'binaan',
            'pagerKeep'  => ['q', 'status'],
            'pagerLabel' => 'siswa bimbingan',
        ]) ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
