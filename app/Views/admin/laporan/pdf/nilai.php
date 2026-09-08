<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2a2c; }
    table.data { width: 100%; border-collapse: collapse; margin-top: 4px; }
    table.data th, table.data td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
    table.data th { background: #0d5c63; color: #fff; font-size: 9px; text-transform: uppercase; }
    table.data td { font-size: 9px; }
    .text-center { text-align: center; }
</style>
</head>
<body>

<?= view('admin/laporan/pdf/_kop', ['sekolah' => $sekolah, 'judulLaporan' => 'Rekap Nilai Akhir PKL', 'subjudul' => $subjudul]) ?>

<table class="data">
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tempat PKL</th>
            <?php foreach ($aspek as $a): ?>
                <th><?= esc($a['nama_aspek']) ?></th>
            <?php endforeach; ?>
            <th>Nilai Akhir</th>
            <th>Predikat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="<?= 8 + count($aspek) ?>" class="text-center">Tidak ada data untuk filter yang dipilih.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $d): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= esc($d['nis']) ?></td>
                <td><?= esc($d['nama_siswa']) ?></td>
                <td><?= esc($d['kelas']) ?></td>
                <td><?= esc($d['nama_perusahaan']) ?></td>
                <?php foreach ($aspek as $a): ?>
                    <td class="text-center"><?= $d['nilai_per_aspek'][$a['id']] ?? '-' ?></td>
                <?php endforeach; ?>
                <td class="text-center"><?= $d['penilaian'] ? number_format((float) $d['penilaian']['nilai_akhir'], 2) : '-' ?></td>
                <td><?= $d['penilaian'] ? esc(\App\Models\PenilaianModel::predikat((float) $d['penilaian']['nilai_akhir'])) : '-' ?></td>
                <td><?= $d['penilaian'] === null ? 'Belum Dinilai' : ($d['penilaian']['status'] === 'final' ? 'Final' : 'Draft') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= view('admin/laporan/pdf/_tanda_tangan', ['sekolah' => $sekolah]) ?>

</body>
</html>
