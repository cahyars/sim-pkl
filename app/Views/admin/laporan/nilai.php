<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?php
$query      = http_build_query($filter);
$aksiExcel  = site_url('admin/laporan/nilai/excel') . '?' . $query;
$aksiPdf    = site_url('admin/laporan/nilai/pdf') . '?' . $query;
$resetUrl   = site_url('admin/laporan/nilai');
?>

<?= view('admin/laporan/_filter', compact('filter', 'daftarKelas', 'daftarTempat', 'aksiExcel', 'aksiPdf', 'resetUrl')) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Tempat PKL</th>
                    <?php foreach ($aspek as $a): ?>
                        <th><?= esc($a['nama_aspek']) ?></th>
                    <?php endforeach; ?>
                    <th>Nilai Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="<?= 4 + count($aspek) ?>" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($data as $d): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($d['nama_siswa']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($d['nis']) ?> &middot; <?= esc($d['kelas']) ?></div>
                        </td>
                        <td class="small"><?= esc($d['nama_perusahaan']) ?></td>
                        <?php foreach ($aspek as $a): ?>
                            <td class="small"><?= $d['nilai_per_aspek'][$a['id']] ?? '-' ?></td>
                        <?php endforeach; ?>
                        <td class="small fw-semibold"><?= $d['penilaian'] ? number_format((float) $d['penilaian']['nilai_akhir'], 2) : '-' ?></td>
                        <td>
                            <?php if ($d['penilaian'] === null): ?>
                                <span class="badge text-bg-secondary">Belum Dinilai</span>
                            <?php elseif ($d['penilaian']['status'] === 'final'): ?>
                                <span class="badge text-bg-success">Final</span>
                            <?php else: ?>
                                <span class="badge text-bg-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?= view('partials/pagination', [
        'pager'      => $pager,
        'pagerGroup' => 'nilai',
        'pagerKeep'  => ['q', 'tahun_ajaran_id', 'kelas', 'tempat_pkl_id', 'status'],
        'pagerLabel' => 'rekap nilai',
    ]) ?>
</div>

<?= $this->endSection() ?>
