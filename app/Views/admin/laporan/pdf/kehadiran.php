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

<?= view('admin/laporan/pdf/_kop', ['sekolah' => $sekolah, 'judulLaporan' => 'Rekap Kehadiran & Keaktifan Logbook', 'subjudul' => $subjudul]) ?>

<table class="data">
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Tempat PKL</th>
            <th>Hari Kerja Berjalan</th>
            <th>Hari Terisi</th>
            <th>Keaktifan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="8" class="text-center">Tidak ada data untuk filter yang dipilih.</td></tr>
        <?php endif; ?>
        <?php foreach ($data as $i => $d): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= esc($d['nis']) ?></td>
                <td><?= esc($d['nama_siswa']) ?></td>
                <td><?= esc($d['kelas']) ?></td>
                <td><?= esc($d['nama_perusahaan']) ?></td>
                <td class="text-center"><?= $d['hari_kerja'] ?></td>
                <td class="text-center"><?= $d['hari_terisi'] ?></td>
                <td class="text-center"><?= $d['persen'] ?>%</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= view('admin/laporan/pdf/_tanda_tangan', ['sekolah' => $sekolah]) ?>

</body>
</html>
