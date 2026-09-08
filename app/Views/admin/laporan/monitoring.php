<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php
$query      = http_build_query($filter);
$aksiExcel  = site_url('admin/laporan/monitoring/excel') . '?' . $query;
$aksiPdf    = site_url('admin/laporan/monitoring/pdf') . '?' . $query;
$resetUrl   = site_url('admin/laporan/monitoring');
?>

<?= view('admin/laporan/_filter', compact('filter', 'daftarKelas', 'daftarTempat', 'aksiExcel', 'aksiPdf', 'resetUrl')) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Tempat PKL</th>
                    <th>Guru / Pembimbing Lapangan</th>
                    <th>Total</th>
                    <th>Disetujui</th>
                    <th>Menunggu</th>
                    <th>Revisi</th>
                    <th>Ditolak</th>
                    <th>Progres</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($d['nama_siswa']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($d['nis']) ?> &middot; <?= esc($d['kelas']) ?></div>
                        </td>
                        <td class="small"><?= esc($d['nama_perusahaan']) ?></td>
                        <td class="small">
                            <?= esc($d['nama_guru']) ?>
                            <div class="text-muted" style="font-size: 0.72rem;"><?= esc($d['nama_pembimbing_lapangan'] ?? '-') ?></div>
                        </td>
                        <td class="small"><?= $d['rekap']['total'] ?></td>
                        <td class="small text-success"><?= $d['rekap']['disetujui'] ?></td>
                        <td class="small text-warning"><?= $d['rekap']['menunggu_validasi'] ?></td>
                        <td class="small text-info"><?= $d['rekap']['revisi'] ?></td>
                        <td class="small text-danger"><?= $d['rekap']['ditolak'] ?></td>
                        <td class="small"><?= $d['persen'] ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= view('partials/pagination', [
        'pager'      => $pager,
        'pagerGroup' => 'monitoring',
        'pagerKeep'  => ['q', 'tahun_ajaran_id', 'kelas', 'tempat_pkl_id', 'status'],
        'pagerLabel' => 'data monitoring',
    ]) ?>
</div>

<?= $this->endSection() ?>
