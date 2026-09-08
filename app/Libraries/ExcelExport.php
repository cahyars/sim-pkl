<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Wrapper tipis di atas PhpSpreadsheet untuk membuat laporan tabular
 * (judul + info filter + header kolom + data) dan langsung mengunduhkannya
 * sebagai .xlsx. Dipakai oleh semua controller laporan (Fitur §4.6).
 */
class ExcelExport
{
    /**
     * @param string        $judul      Judul laporan, tampil di baris paling atas.
     * @param array<string> $subjudul   Baris info tambahan (mis. filter yang dipakai, tanggal cetak).
     * @param array<string> $headers    Nama kolom.
     * @param array<int, array<int, string|int|float|null>> $rows Data baris, urutan harus sama dengan $headers.
     */
    public function unduh(string $judul, array $subjudul, array $headers, array $rows, string $namaFile)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $jumlahKolom = count($headers);
        $kolomTerakhir = $this->kolomKe($jumlahKolom);

        $baris = 1;

        $sheet->setCellValue("A{$baris}", $judul);
        $sheet->mergeCells("A{$baris}:{$kolomTerakhir}{$baris}");
        $sheet->getStyle("A{$baris}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$baris}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $baris += 2;

        foreach ($subjudul as $baris_info) {
            $sheet->setCellValue("A{$baris}", $baris_info);
            $sheet->mergeCells("A{$baris}:{$kolomTerakhir}{$baris}");
            $sheet->getStyle("A{$baris}")->getFont()->setItalic(true)->setSize(9);
            $baris++;
        }
        $baris++;

        $barisHeader = $baris;
        foreach ($headers as $i => $h) {
            $kolom = $this->kolomKe($i + 1);
            $sheet->setCellValue("{$kolom}{$barisHeader}", $h);
        }
        $sheet->getStyle("A{$barisHeader}:{$kolomTerakhir}{$barisHeader}")
            ->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$barisHeader}:{$kolomTerakhir}{$barisHeader}")
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D5C63');
        $baris++;

        if ($rows === []) {
            $sheet->setCellValue("A{$baris}", 'Tidak ada data untuk filter yang dipilih.');
            $sheet->mergeCells("A{$baris}:{$kolomTerakhir}{$baris}");
            $sheet->getStyle("A{$baris}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$baris}")->getFont()->setItalic(true);
            $baris++;
        } else {
            foreach ($rows as $row) {
                foreach ($row as $i => $nilai) {
                    $kolom = $this->kolomKe($i + 1);

                    // Angka desimal yang sudah diformat (mis. "89.10" dari number_format) ditulis
                    // sebagai teks apa adanya — kalau dibiarkan PhpSpreadsheet mendeteksinya
                    // sebagai numerik, representasi float biner bisa memunculkan noise seperti
                    // 89.099999999999994 saat file dibuka di Excel.
                    if (is_string($nilai) && preg_match('/^-?\d+\.\d{1,}$/', $nilai)) {
                        $sheet->setCellValueExplicit("{$kolom}{$baris}", $nilai, DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValue("{$kolom}{$baris}", $nilai);
                    }
                }
                $baris++;
            }
        }

        $sheet->getStyle("A{$barisHeader}:{$kolomTerakhir}" . ($baris - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range(1, $jumlahKolom) as $i) {
            $sheet->getColumnDimension($this->kolomKe($i))->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        ob_start();
        $writer->save('php://output');
        $konten = ob_get_clean();

        return service('response')
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $namaFile . '"')
            ->setBody($konten);
    }

    private function kolomKe(int $index): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }
}
