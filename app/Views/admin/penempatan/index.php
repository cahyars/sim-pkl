<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $keyword,
    'searchPlaceholder' => 'Cari siswa/NIS/kelas/perusahaan/guru',
    'searchWidth'       => '290px',
    'filters'           => [
        [
            'name'    => 'status',
            'value'   => $status,
            'width'   => '160px',
            'label'   => 'Status penempatan',
            'options' => [
                'aktif'      => 'Aktif',
                'selesai'    => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
                'semua'      => 'Semua Status',
            ],
        ],
    ],
    'resetUrl'          => site_url('admin/penempatan'),
    'actions'           => '<a href="' . site_url('admin/laporan/plotting') . '" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Rekap Plotting (P2)</a> <a href="' . site_url('admin/penempatan/tambah') . '" class="btn btn-sm btn-primary">+ Assign Penempatan</a>',
]) ?>

<div class="info-card">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted small">
                    <th>Siswa</th>
                    <th>Tempat PKL</th>
                    <th>Guru / Pembimbing Lapangan</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($penempatan)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data penempatan.</td></tr>
                <?php endif; ?>
                <?php foreach ($penempatan as $p): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold small"><?= esc($p['nama_siswa']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($p['nis']) ?> &middot; <?= esc($p['kelas']) ?></div>
                        </td>
                        <td class="small"><?= esc($p['nama_perusahaan']) ?></td>
                        <td class="small">
                            <?= esc($p['nama_guru']) ?>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= esc($p['nama_pembimbing_lapangan'] ?? '-') ?></div>
                        </td>
                        <td class="small"><?= tanggal_indo($p['tanggal_mulai']) ?><br><?= tanggal_indo($p['tanggal_selesai']) ?></td>
                        <td>
                            <?php
                                $badge = ['aktif' => 'text-bg-success', 'selesai' => 'text-bg-secondary', 'dibatalkan' => 'text-bg-danger'][$p['status']] ?? 'text-bg-secondary';
                            ?>
                            <span class="badge <?= $badge ?>"><?= esc(penempatan_status_label($p['status'])) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('admin/penempatan/riwayat/' . $p['siswa_id']) ?>" class="btn btn-sm btn-outline-secondary">Riwayat</a>
                            <?php if ($p['status'] === 'aktif'): ?>
                                <form action="<?= site_url('admin/penempatan/' . $p['id'] . '/selesai') ?>" method="post" class="d-inline" data-confirm="Tandai penempatan ini selesai?">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Selesai</button>
                                </form>
                                <form action="<?= site_url('admin/penempatan/' . $p['id'] . '/batalkan') ?>" method="post" class="d-inline" data-confirm="Batalkan penempatan ini? Siswa akan kembali berstatus belum ditempatkan.">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Batalkan</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= view('partials/pagination', [
    'pager'      => $pager,
    'pagerGroup' => 'penempatan',
    'pagerKeep'  => ['q', 'status'],
    'pagerLabel' => 'penempatan',
]) ?>

<?= $this->endSection() ?>
