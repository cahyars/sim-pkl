<?php helper('simpkl'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa, Pembimbing dan Tempat PKL</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.2;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-bold { font-weight: bold; }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #C6E0B4; /* Hijau pastel khas dokumen Data PKL P2 */
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
        }

        .baris-total {
            background-color: #FFFF00; /* Kuning khas penutup dokumen Data PKL P2 */
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>

    <div class="title">
        DAFTAR SISWA, PEMBIMBING DAN TEMPAT PKL <?= strtoupper(esc($periodeJudul)) ?>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">NO</th>
                <th style="width: 160px;">TEMPAT PKL</th>
                <th style="width: 135px;">NAMA PEMBIMBING</th>
                <th>NAMA SISWA</th>
                <th style="width: 65px;">KELAS</th>
                <th style="width: 45px;">Jumlah<br>Siswa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Belum ada data penempatan siswa pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; ?>
                <?php foreach ($items as $item): ?>
                    <?php
                        $jumlahSiswa = count($item['siswa']);
                        $first = true;
                    ?>
                    <?php foreach ($item['siswa'] as $s): ?>
                        <tr>
                            <?php if ($first): ?>
                                <td class="text-center" rowspan="<?= $jumlahSiswa ?>"><?= $no++ ?></td>
                                <td rowspan="<?= $jumlahSiswa ?>" style="font-size: 7.5pt;"><?= esc($item['nama_perusahaan']) ?></td>
                                <td class="text-center" rowspan="<?= $jumlahSiswa ?>" style="font-size: 7.5pt;"><?= esc($item['nama_pembimbing'] ?? $item['nama_guru'] ?? '-') ?></td>
                            <?php endif; ?>
                            <td style="font-size: 7.5pt;"><?= esc($s['nama_siswa']) ?></td>
                            <td class="text-center" style="font-size: 7.5pt;"><?= esc($s['kelas']) ?></td>
                            <?php if ($first): ?>
                                <td class="text-center text-bold" rowspan="<?= $jumlahSiswa ?>"><?= $jumlahSiswa ?></td>
                                <?php $first = false; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="baris-total">
                <td colspan="5">
                    JUMLAH SISWA PKL <?= strtoupper(esc($periodeJudul)) ?>
                </td>
                <td><?= $totalSiswa ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
