<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php $exportQuery = http_build_query(['status' => $status, 'q' => $keyword]); ?>

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
    'resetUrl'          => site_url('guru/laporan'),
    'actions'           => '<a href="' . site_url('guru/laporan/excel') . '?' . $exportQuery . '" class="btn btn-sm btn-outline-success">Export Excel</a>'
        . '<a href="' . site_url('guru/laporan/pdf') . '?' . $exportQuery . '" class="btn btn-sm btn-outline-danger" target="_blank">Export PDF</a>',
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Tempat PKL</th>
                    <th>Total</th>
                    <th>Disetujui</th>
                    <th>Menunggu</th>
                    <th>Revisi</th>
                    <th>Ditolak</th>
                    <th>Progres</th>
                    <th>Keaktifan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">
                        <?= $keyword !== '' ? 'Tidak ada siswa bimbingan yang cocok dengan pencarian.' : 'Tidak ada siswa bimbingan dengan status ini.' ?>
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($d['nama_siswa']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($d['nis']) ?> &middot; <?= esc($d['kelas']) ?></div>
                        </td>
                        <td class="small"><?= esc($d['nama_perusahaan']) ?></td>
                        <td class="small"><?= $d['rekap']['total'] ?></td>
                        <td class="small text-success"><?= $d['rekap']['disetujui'] ?></td>
                        <td class="small text-warning"><?= $d['rekap']['menunggu_validasi'] ?></td>
                        <td class="small text-info"><?= $d['rekap']['revisi'] ?></td>
                        <td class="small text-danger"><?= $d['rekap']['ditolak'] ?></td>
                        <td class="small"><?= $d['persen'] ?>%</td>
                        <td class="small"><?= $d['keaktifan'] ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= view('partials/pagination', [
        'pager'      => $pager,
        'pagerGroup' => 'laporan',
        'pagerKeep'  => ['q', 'status'],
        'pagerLabel' => 'siswa bimbingan',
    ]) ?>
</div>

<?= $this->endSection() ?>
