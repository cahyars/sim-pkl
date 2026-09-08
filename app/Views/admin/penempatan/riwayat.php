<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card mb-3">
    <div class="fw-semibold" style="font-size: 1.05rem;"><?= esc($siswa['nama']) ?></div>
    <div class="text-muted small">NIS <?= esc($siswa['nis']) ?> &middot; <?= esc($siswa['kelas']) ?> &middot; <?= esc($siswa['jurusan']) ?></div>
</div>

<div class="info-card">
    <div class="fw-semibold mb-3">Riwayat Penempatan PKL</div>

    <?= view('partials/table_toolbar', [
        'searchName' => '',
        'filters'    => [
            [
                'name'    => 'status',
                'value'   => $status,
                'width'   => '170px',
                'label'   => 'Status penempatan',
                'empty'   => 'Semua Status',
                'options' => [
                    'aktif'      => 'Aktif',
                    'selesai'    => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ],
            ],
        ],
        'resetUrl'   => site_url('admin/penempatan/riwayat/' . $siswa['id']),
    ]) ?>

    <?php if (empty($riwayat)): ?>
        <div class="text-muted small text-center py-4"><?= $status !== '' ? 'Tidak ada riwayat dengan status tersebut.' : 'Siswa ini belum pernah ditempatkan PKL.' ?></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Tempat PKL</th>
                        <th>Guru / Pembimbing Lapangan</th>
                        <th>Periode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat as $r): ?>
                        <tr>
                            <td class="small"><?= esc($r['nama_perusahaan']) ?></td>
                            <td class="small">
                                <?= esc($r['nama_guru']) ?>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= esc($r['nama_pembimbing_lapangan'] ?? '-') ?></div>
                            </td>
                            <td class="small"><?= tanggal_indo($r['tanggal_mulai']) ?> &ndash; <?= tanggal_indo($r['tanggal_selesai']) ?></td>
                            <td>
                                <?php
                                    $badge = ['aktif' => 'text-bg-success', 'selesai' => 'text-bg-secondary', 'dibatalkan' => 'text-bg-danger'][$r['status']] ?? 'text-bg-secondary';
                                ?>
                                <span class="badge <?= $badge ?>"><?= esc(penempatan_status_label($r['status'])) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= view('partials/pagination', [
            'pager'      => $pager,
            'pagerGroup' => 'riwayat',
            'pagerKeep'  => ['status'],
            'pagerLabel' => 'riwayat penempatan',
        ]) ?>
    <?php endif; ?>
</div>

<div class="mt-3">
    <a href="<?= site_url('admin/penempatan') ?>" class="btn btn-sm btn-outline-secondary">&larr; Kembali ke Daftar Penempatan</a>
</div>

<?= $this->endSection() ?>
