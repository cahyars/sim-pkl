<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2a2c; }
    table.data { width: 100%; border-collapse: collapse; margin-top: 4px; margin-bottom: 16px; }
    table.data th, table.data td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
    table.data th { background: #0d5c63; color: #fff; font-size: 9px; text-transform: uppercase; }
    table.data td { font-size: 9px; }
    .judul-tempat { font-weight: bold; font-size: 11px; margin-top: 14px; margin-bottom: 2px; color: #093f44; }
    .info-tempat { font-size: 9px; color: #667479; margin-bottom: 4px; }
    .text-center { text-align: center; }
</style>
</head>
<body>

<?= view('admin/laporan/pdf/_kop', ['sekolah' => $sekolah, 'judulLaporan' => 'Laporan Sebaran Siswa PKL', 'subjudul' => ['Dicetak: ' . tanggal_indo(date('Y-m-d'), true)]]) ?>

<?php if (empty($data)): ?>
    <p class="text-center">Belum ada data tempat PKL.</p>
<?php endif; ?>

<?php foreach ($data as $t): ?>
    <div class="judul-tempat"><?= esc($t['nama_perusahaan']) ?> (<?= esc($t['bidang_usaha']) ?>)</div>
    <div class="info-tempat">Kuota: <?= $t['kuota'] ?> &middot; Terisi: <?= $t['terisi'] ?></div>

    <?php if (empty($t['siswa'])): ?>
        <p style="font-size: 9px; color: #667479;">Belum ada siswa ditempatkan.</p>
    <?php else: ?>
        <table class="data">
            <thead>
                <tr><th style="width: 30px;">No</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th></tr>
            </thead>
            <tbody>
                <?php foreach ($t['siswa'] as $i => $s): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= esc($s['nis']) ?></td>
                        <td><?= esc($s['nama_siswa']) ?></td>
                        <td><?= esc($s['kelas']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endforeach; ?>

<?= view('admin/laporan/pdf/_tanda_tangan', ['sekolah' => $sekolah]) ?>

</body>
</html>
