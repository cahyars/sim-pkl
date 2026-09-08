<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php
$query      = http_build_query($filter);
$aksiExcel  = site_url('admin/laporan/kehadiran/excel') . '?' . $query;
$aksiPdf    = site_url('admin/laporan/kehadiran/pdf') . '?' . $query;
$resetUrl   = site_url('admin/laporan/kehadiran');
?>

<?= view('admin/laporan/_filter', compact('filter', 'daftarKelas', 'daftarTempat', 'aksiExcel', 'aksiPdf', 'resetUrl')) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Tempat PKL</th>
                    <th>Hari Kerja Berjalan</th>
                    <th>Hari Terisi</th>
                    <th>Keaktifan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($d['nama_siswa']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($d['nis']) ?> &middot; <?= esc($d['kelas']) ?></div>
                        </td>
                        <td class="small"><?= esc($d['nama_perusahaan']) ?></td>
                        <td class="small"><?= $d['hari_kerja'] ?> hari</td>
                        <td class="small"><?= $d['hari_terisi'] ?> hari</td>
                        <td style="min-width: 160px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress progress-thin flex-grow-1">
                                    <div class="progress-bar <?= $d['persen'] >= 80 ? 'bg-success' : ($d['persen'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" style="width: <?= min($d['persen'], 100) ?>%"></div>
                                </div>
                                <span class="small text-muted"><?= $d['persen'] ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= view('partials/pagination', [
        'pager'      => $pager,
        'pagerGroup' => 'kehadiran',
        'pagerKeep'  => ['q', 'tahun_ajaran_id', 'kelas', 'tempat_pkl_id', 'status'],
        'pagerLabel' => 'rekap kehadiran',
    ]) ?>
</div>

<?= $this->endSection() ?>
