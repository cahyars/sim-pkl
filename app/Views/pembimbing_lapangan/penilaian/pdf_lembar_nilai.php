<?php helper('simpkl'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai - <?= esc($siswa['nama_siswa']) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 18mm 18mm 18mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-bold { font-weight: bold; }
        .page-break { page-break-after: always; }

        /* Header Lembar Nilai */
        .header-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            line-height: 1.25;
        }

        /* Tabel Identitas */
        .identity-table {
            width: 100%;
            margin-bottom: 14px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }
        .identity-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        /* Tabel Penilaian */
        .table-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .table-nilai th, .table-nilai td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .table-nilai th {
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
        }

        /* Catatan & Kotak Kehadiran */
        .box-catatan {
            border: 1px solid #000;
            padding: 5px 8px;
            min-height: 38px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .box-kehadiran {
            border: 1px solid #000;
            padding: 5px 8px;
            width: 210px;
            font-size: 8.5pt;
        }

        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 15px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }
        .ttd-table td {
            vertical-align: top;
            text-align: center;
        }

        /* Halaman 2: Formulir Huruf Kapital */
        .halaman-2-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            letter-spacing: 0.5px;
            margin-top: 40px;
            margin-bottom: 45px;
        }
        .form-sertifikat-table {
            width: 100%;
            font-size: 10.5pt;
            border-collapse: collapse;
        }
        .form-sertifikat-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .titik-titik {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 320px;
        }
    </style>
</head>
<body>

    <!-- =================================================================== -->
    <!-- HALAMAN 1: DAFTAR NILAI PESERTA DIDIK                               -->
    <!-- =================================================================== -->
    <table class="header-table">
        <tr>
            <td width="70" style="vertical-align: middle; text-align: center;">
                <?php if (! empty($sekolah['logo']) && is_file($sekolah['logo'])): ?>
                    <img src="<?= $sekolah['logo'] ?>" style="height: 58px;">
                <?php endif; ?>
            </td>
            <td class="header-title">
                DAFTAR NILAI PESERTA DIDIK<br>
                MATA PELAJARAN PKL<br>
                <?= strtoupper(esc($sekolah['nama'] ?? 'SMK NEGERI 1 SUBANG')) ?><br>
                TAHUN AJARAN <?= strtoupper(esc($tahunAjaran ?? '2025/2026')) ?>
            </td>
            <td width="70">&nbsp;</td>
        </tr>
    </table>

    <!-- Identitas Peserta Didik -->
    <table class="identity-table">
        <tr>
            <td width="160">Nama Peserta Didik</td>
            <td width="10">:</td>
            <td width="260" class="text-bold"><?= esc($siswa['nama_siswa']) ?></td>
            <td width="90">Tempat PKL</td>
            <td width="10">:</td>
            <td><?= esc($siswa['nama_perusahaan']) ?></td>
        </tr>
        <tr>
            <td>NISN</td>
            <td>:</td>
            <td><?= esc($siswa['nisn'] ?: $siswa['nis']) ?></td>
            <td>Tanggal PKL</td>
            <td>:</td>
            <td>Mulai : <?= tanggal_indo($siswa['tanggal_mulai']) ?><br>Selesai : <?= tanggal_indo($siswa['tanggal_selesai']) ?></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td><?= esc($siswa['kelas']) ?></td>
            <td>Nama Instruktur</td>
            <td>:</td>
            <td><?= esc($penilaian['instruktur_nama'] ?? $siswa['nama_pembimbing_lapangan'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>Program Keahlian</td>
            <td>:</td>
            <td><?= esc($programKeahlian) ?></td>
            <td>Nama Pembimbing</td>
            <td>:</td>
            <td><?= esc($siswa['nama_guru']) ?></td>
        </tr>
        <tr>
            <td>Konsentrasi Keahlian</td>
            <td>:</td>
            <td><?= esc($konsentrasiKeahlian) ?></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- Tabel Capaian Kompetensi / Tujuan Pembelajaran -->
    <table class="table-nilai">
        <thead>
            <tr>
                <th width="32">No</th>
                <th>Tujuan Pembelajaran</th>
                <th width="55">Skor</th>
                <th width="200">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($aspek as $i => $a): ?>
                <?php
                    $skor = $nilaiByAspek[$a['id']] ?? 0;
                    $desk = $deskripsiByAspek[$a['id']] ?? '';
                    if ($desk === '' && $skor > 0) {
                        $desk = $skor >= 86 ? 'Sangat menguasai kompetensi ini dengan baik.' : ($skor >= 70 ? 'Mampu menerapkan kompetensi ini secara optimal.' : 'Cukup menguasai, masih perlu penguatan.');
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td><?= esc($a['nama_aspek']) ?></td>
                    <td class="text-center text-bold"><?= number_format((float) $skor, 0) ?></td>
                    <td style="font-size: 8pt;"><?= esc($desk) ?: '-' ?></td>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: #f8f9fa;">
                <td colspan="2" class="text-center text-bold">RATA-RATA NILAI</td>
                <td class="text-center text-bold"><?= number_format((float) ($penilaian['nilai_akhir'] ?? 0), 2) ?></td>
                <td class="text-center text-bold" style="font-size: 8.5pt;">
                    <?= esc(\App\Models\PenilaianModel::predikat((float) ($penilaian['nilai_akhir'] ?? 0))) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Catatan -->
    <div style="font-size: 9pt; font-weight: bold; margin-bottom: 2px;">Catatan :</div>
    <div class="box-catatan">
        <?= nl2br(esc($penilaian['feedback'] ?? 'Ananda telah menyelesaikan seluruh rangkaian Praktik Kerja Lapangan dengan baik dan berdedikasi.')) ?>
    </div>

    <!-- Kehadiran & Keterangan & Tanda Tangan -->
    <table width="100%" style="border-collapse: collapse;">
        <tr>
            <td width="230" style="vertical-align: top;">
                <table class="box-kehadiran" cellpadding="2" cellspacing="0">
                    <tr><td colspan="3" class="text-bold" style="border-bottom: 1px solid #000; padding-bottom: 3px;">Kehadiran</td></tr>
                    <tr>
                        <td width="110">Sakit</td>
                        <td width="10">:</td>
                        <td><?= (int) ($penilaian['sakit'] ?? 0) ?> hari</td>
                    </tr>
                    <tr>
                        <td>Ijin</td>
                        <td>:</td>
                        <td><?= (int) ($penilaian['izin'] ?? 0) ?> hari</td>
                    </tr>
                    <tr>
                        <td>Tanpa Keterangan</td>
                        <td>:</td>
                        <td><?= (int) ($penilaian['tanpa_keterangan'] ?? 0) ?> hari</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border-top: 1px solid #000; padding-top: 4px; font-size: 8pt;">
                            <strong>KETERANGAN :</strong><br>
                            86 – 100 : A (Amat Baik)<br>
                            70 – 85 &nbsp;: B (Baik)<br>
                            &lt; 70 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: C (Kurang)
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; padding-left: 20px;">
                <div class="text-end" style="font-size: 9pt; margin-bottom: 20px;">
                    Subang, <?= tanggal_indo($penilaian['tanggal_penilaian'] ?? date('Y-m-d')) ?>
                </div>
                <table class="ttd-table">
                    <tr>
                        <td width="50%">
                            Guru Mata Pelajaran PKL<br><br><br><br><br>
                            <strong>( <?= esc($siswa['nama_guru']) ?> )</strong><br>
                            NIP. <?= esc($siswa['nip_guru'] ?? '..................................') ?>
                        </td>
                        <td width="50%">
                            Instruktur Dunia kerja<br><br><br><br><br>
                            <strong>( <?= esc($penilaian['instruktur_nama'] ?? $siswa['nama_pembimbing_lapangan'] ?? '..................................') ?> )</strong><br>
                            NIP/NIK/ID. <?= esc($penilaian['instruktur_nip'] ?? '..................................') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- =================================================================== -->
    <!-- HALAMAN 2: FORMULIR PENANDA TANGAN SERTIFIKAT                       -->
    <!-- =================================================================== -->
    <div class="halaman-2-title">
        MOHON DIISI DENGAN HURUF KAPITAL
    </div>

    <table class="form-sertifikat-table">
        <tr>
            <td width="220" class="text-bold">1. Nama Instansi/Perusahaan</td>
            <td width="15">:</td>
            <td class="text-bold"><?= strtoupper(esc($siswa['nama_perusahaan'])) ?></td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 18px;" class="text-bold">2. Penanda tangan Sertifikat</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Pimpinan/Kepala/Direktur</td>
            <td>:</td>
            <td><?= esc($penilaian['pimpinan_nama'] ?? '........................................................................................') ?></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">NIP/NIK/ID</td>
            <td>:</td>
            <td><?= esc($penilaian['pimpinan_nip'] ?? '........................................................................................') ?></td>
        </tr>
        <tr>
            <td style="padding-left: 20px; padding-top: 14px;">Pembimbing Siswa</td>
            <td style="padding-top: 14px;">:</td>
            <td style="padding-top: 14px;"><?= esc($penilaian['instruktur_nama'] ?? $siswa['nama_pembimbing_lapangan'] ?? '........................................................................................') ?></td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">NIP/NIK/ID</td>
            <td>:</td>
            <td><?= esc($penilaian['instruktur_nip'] ?? '........................................................................................') ?></td>
        </tr>
    </table>

    <div style="margin-top: 70px; float: right; width: 280px; text-align: center; font-size: 10pt;">
        <div>...................., ...........................</div>
        <div style="margin-top: 5px;">Pimpinan, Ketua/ Direktur</div>
        <div style="margin-top: 75px;">
            <strong>( <?= esc($penilaian['pimpinan_nama'] ?? '..........................................') ?> )</strong>
        </div>
        <div style="margin-top: 4px;">
            NIP/K/ID. <?= esc($penilaian['pimpinan_nip'] ?? '...........................') ?>
        </div>
    </div>

</body>
</html>
