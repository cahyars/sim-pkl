<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 12mm 12mm; }
    body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #000; }

    .judul { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; margin: 0 0 8pt; }

    .info-tabel { width: 100%; font-size: 10pt; margin-bottom: 8pt; }
    .info-tabel td { padding: 0.5pt 0; }
    .info-tabel td.label { width: 145px; }
    .info-tabel td.titik-dua { width: 12px; }

    table.data { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.data th, table.data td { border: 1px solid #000; padding: 1.5pt 3pt; text-align: center; line-height: 1.1; }
    table.data th { font-weight: bold; font-size: 10pt; }
    table.data td { font-size: 10pt; }
    table.data td.tanggal { text-align: left; white-space: nowrap; }
</style>
</head>
<body>

<div class="judul">Daftar Hadir Kegiatan Praktik Kerja Lapangan (PKL)</div>

<table class="info-tabel" cellpadding="0" cellspacing="0">
    <tr>
        <td class="label">Nama Peserta Didik</td><td class="titik-dua">:</td>
        <td><?= esc($penempatan['nama_siswa']) ?> (<?= esc($penempatan['nis']) ?> / <?= esc($penempatan['kelas']) ?>)</td>
    </tr>
    <tr>
        <td class="label">Nama Tempat PKL</td><td class="titik-dua">:</td>
        <td><?= esc($penempatan['nama_perusahaan']) ?></td>
    </tr>
    <tr>
        <td class="label">Nama Pembimbing PKL</td><td class="titik-dua">:</td>
        <td><?= esc($penempatan['nama_pembimbing_lapangan'] ?? '-') ?></td>
    </tr>
    <tr>
        <td class="label">Bulan</td><td class="titik-dua">:</td>
        <td><?= esc($namaBulan) ?></td>
    </tr>
</table>

<table class="data">
    <thead>
        <tr>
            <th rowspan="2" style="width: 4%;">No</th>
            <th rowspan="2" style="width: 17%;">Tanggal</th>
            <th colspan="2" style="width: 20%;">Jam</th>
            <th colspan="2" style="width: 26%;">Tanda Tangan</th>
            <th rowspan="2" style="width: 33%;">Keterangan</th>
        </tr>
        <tr>
            <th style="width: 10%;">Datang</th>
            <th style="width: 10%;">Pulang</th>
            <th style="width: 13%;">Siswa</th>
            <th style="width: 13%;">Pembimbing</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($baris)): ?>
            <tr><td colspan="7">Tidak ada data pada bulan ini.</td></tr>
        <?php endif; ?>
        <?php foreach ($baris as $b): ?>
            <tr>
                <td><?= $b['no'] ?></td>
                <td class="tanggal"><?= esc($b['tanggal']) ?></td>
                <td><?= esc($b['datang']) ?></td>
                <td><?= esc($b['pulang']) ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><?= esc($b['keterangan']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
