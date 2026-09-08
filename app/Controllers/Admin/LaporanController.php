<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ExcelExport;
use App\Libraries\PdfExport;
use App\Models\AspekPenilaianModel;
use App\Models\DetailPenilaianModel;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;
use App\Models\PenilaianModel;
use App\Models\SiswaModel;
use App\Models\TahunAjaranModel;
use App\Models\TempatPklModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends BaseController
{
    public function index()
    {
        return view('admin/laporan/index', [
            'title' => 'Laporan',
        ]);
    }

    // =====================================================================
    // 1. LAPORAN MONITORING PKL
    // =====================================================================

    public function monitoring()
    {
        $filter = $this->filterUmum();
        $model  = new PenempatanPklModel();
        $data   = $this->dataMonitoring($filter, 15, 'monitoring', $model);

        return view('admin/laporan/monitoring', array_merge($this->opsiFilter(), [
            'title'  => 'Laporan Monitoring PKL',
            'filter' => $filter,
            'data'   => $data,
            'pager'  => $model->pager,
        ]));
    }

    public function monitoringExcel()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataMonitoring($filter);

        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Jurusan', 'Tempat PKL', 'Guru Pembimbing', 'Pembimbing Lapangan', 'Periode PKL', 'Total Logbook', 'Disetujui', 'Menunggu', 'Revisi', 'Ditolak', 'Progres (%)'];

        $rows = [];
        foreach ($data as $i => $d) {
            $rows[] = [
                $i + 1, $d['nis'], $d['nama_siswa'], $d['kelas'], $d['jurusan'], $d['nama_perusahaan'],
                $d['nama_guru'], $d['nama_pembimbing_lapangan'] ?? '-',
                tanggal_indo($d['tanggal_mulai']) . ' - ' . tanggal_indo($d['tanggal_selesai']),
                $d['rekap']['total'], $d['rekap']['disetujui'], $d['rekap']['menunggu_validasi'],
                $d['rekap']['revisi'], $d['rekap']['ditolak'], $d['persen'],
            ];
        }

        return (new ExcelExport())->unduh(
            'Laporan Monitoring PKL',
            $this->subjudul($filter),
            $headers,
            $rows,
            'laporan-monitoring-pkl-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function monitoringPdf()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataMonitoring($filter);

        return (new PdfExport())->unduh('admin/laporan/pdf/monitoring', [
            'data'      => $data,
            'subjudul'  => $this->subjudul($filter),
            'sekolah'   => $this->identitasSekolah(),
        ], 'laporan-monitoring-pkl-' . date('Y-m-d') . '.pdf', 'landscape');
    }

    // =====================================================================
    // 2. LAPORAN SEBARAN SISWA PKL
    // =====================================================================

    public function sebaran()
    {
        $keyword     = trim((string) $this->request->getGet('q'));
        $tempatModel = new TempatPklModel();
        $query       = $tempatModel->orderBy('nama_perusahaan', 'ASC');

        if ($keyword !== '') {
            $query->groupStart()
                ->like('nama_perusahaan', $keyword)
                ->orLike('bidang_usaha', $keyword)
                ->groupEnd();
        }

        $daftarTempat = $query->paginate(8, 'sebaran');

        return view('admin/laporan/sebaran', [
            'title'   => 'Laporan Sebaran Siswa PKL',
            'data'    => $this->dataSebaran($daftarTempat),
            'pager'   => $tempatModel->pager,
            'keyword' => $keyword,
        ]);
    }

    public function sebaranExcel()
    {
        $data    = $this->dataSebaran();
        $headers = ['No', 'Tempat PKL', 'Bidang Usaha', 'Kuota', 'Terisi', 'Nama Siswa', 'NIS', 'Kelas'];

        $rows = [];
        $no   = 1;
        foreach ($data as $t) {
            if ($t['siswa'] === []) {
                $rows[] = [$no++, $t['nama_perusahaan'], $t['bidang_usaha'], $t['kuota'], $t['terisi'], '-', '-', '-'];

                continue;
            }
            foreach ($t['siswa'] as $s) {
                $rows[] = [$no++, $t['nama_perusahaan'], $t['bidang_usaha'], $t['kuota'], $t['terisi'], $s['nama_siswa'], $s['nis'], $s['kelas']];
            }
        }

        return (new ExcelExport())->unduh(
            'Laporan Sebaran Siswa PKL',
            ['Dicetak: ' . tanggal_indo(date('Y-m-d'), true)],
            $headers,
            $rows,
            'laporan-sebaran-siswa-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function sebaranPdf()
    {
        return (new PdfExport())->unduh('admin/laporan/pdf/sebaran', [
            'data'     => $this->dataSebaran(),
            'sekolah'  => $this->identitasSekolah(),
        ], 'laporan-sebaran-siswa-' . date('Y-m-d') . '.pdf', 'portrait');
    }

    // =====================================================================
    // 3. REKAP NILAI AKHIR
    // =====================================================================

    public function nilai()
    {
        $filter = $this->filterUmum();
        $aspek  = (new AspekPenilaianModel())->getAktif();
        $model  = new PenempatanPklModel();
        $data   = $this->dataNilai($filter, 15, 'nilai', $model);

        return view('admin/laporan/nilai', array_merge($this->opsiFilter(), [
            'title'  => 'Rekap Nilai Akhir',
            'filter' => $filter,
            'aspek'  => $aspek,
            'data'   => $data,
            'pager'  => $model->pager,
        ]));
    }

    public function nilaiExcel()
    {
        $filter = $this->filterUmum();
        $aspek  = (new AspekPenilaianModel())->getAktif();
        $data   = $this->dataNilai($filter);

        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Tempat PKL', 'Pembimbing Lapangan'];
        foreach ($aspek as $a) {
            $headers[] = $a['nama_aspek'];
        }
        $headers[] = 'Nilai Akhir';
        $headers[] = 'Predikat';
        $headers[] = 'Status';

        $rows = [];
        foreach ($data as $i => $d) {
            $row = [$i + 1, $d['nis'], $d['nama_siswa'], $d['kelas'], $d['nama_perusahaan'], $d['nama_pembimbing_lapangan'] ?? '-'];
            foreach ($aspek as $a) {
                $row[] = $d['nilai_per_aspek'][$a['id']] ?? '-';
            }
            $row[] = $d['penilaian'] ? number_format((float) $d['penilaian']['nilai_akhir'], 2) : '-';
            $row[] = $d['penilaian'] ? PenilaianModel::predikat((float) $d['penilaian']['nilai_akhir']) : '-';
            $row[] = $d['penilaian'] === null ? 'Belum Dinilai' : ($d['penilaian']['status'] === 'final' ? 'Final' : 'Draft');
            $rows[] = $row;
        }

        return (new ExcelExport())->unduh(
            'Rekap Nilai Akhir PKL',
            $this->subjudul($filter),
            $headers,
            $rows,
            'rekap-nilai-akhir-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function nilaiPdf()
    {
        $filter = $this->filterUmum();
        $aspek  = (new AspekPenilaianModel())->getAktif();
        $data   = $this->dataNilai($filter);

        return (new PdfExport())->unduh('admin/laporan/pdf/nilai', [
            'data'     => $data,
            'aspek'    => $aspek,
            'subjudul' => $this->subjudul($filter),
            'sekolah'  => $this->identitasSekolah(),
        ], 'rekap-nilai-akhir-' . date('Y-m-d') . '.pdf', 'landscape');
    }

    // =====================================================================
    // 4. REKAP KEHADIRAN / KEAKTIFAN
    // =====================================================================

    public function kehadiran()
    {
        $filter = $this->filterUmum();
        $model  = new PenempatanPklModel();
        $data   = $this->dataKehadiran($filter, 15, 'kehadiran', $model);

        return view('admin/laporan/kehadiran', array_merge($this->opsiFilter(), [
            'title'  => 'Rekap Kehadiran & Keaktifan',
            'filter' => $filter,
            'data'   => $data,
            'pager'  => $model->pager,
        ]));
    }

    public function kehadiranExcel()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataKehadiran($filter);

        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Tempat PKL', 'Hari Kerja Berjalan', 'Hari Terisi', 'Keaktifan (%)'];

        $rows = [];
        foreach ($data as $i => $d) {
            $rows[] = [$i + 1, $d['nis'], $d['nama_siswa'], $d['kelas'], $d['nama_perusahaan'], $d['hari_kerja'], $d['hari_terisi'], $d['persen']];
        }

        return (new ExcelExport())->unduh(
            'Rekap Kehadiran & Keaktifan Logbook',
            $this->subjudul($filter),
            $headers,
            $rows,
            'rekap-kehadiran-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function kehadiranPdf()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataKehadiran($filter);

        return (new PdfExport())->unduh('admin/laporan/pdf/kehadiran', [
            'data'     => $data,
            'subjudul' => $this->subjudul($filter),
            'sekolah'  => $this->identitasSekolah(),
        ], 'rekap-kehadiran-' . date('Y-m-d') . '.pdf', 'portrait');
    }

    // =====================================================================
    // Helper internal
    // =====================================================================

    private function filterUmum(): array
    {
        return [
            'kelas'           => $this->request->getGet('kelas') ?: '',
            'tempat_pkl_id'   => $this->request->getGet('tempat_pkl_id') ?: '',
            'tahun_ajaran_id' => $this->request->getGet('tahun_ajaran_id') ?: '',
            'status'          => $this->request->getGet('status') ?: 'aktif',
            'q'               => trim((string) $this->request->getGet('q')),
        ];
    }

    private function opsiFilter(): array
    {
        return [
            'daftarKelas'  => (new SiswaModel())->daftarKelas(),
            'daftarTempat' => (new TempatPklModel())->orderBy('nama_perusahaan', 'ASC')->findAll(),
            'daftarTahun'  => (new TahunAjaranModel())->orderBy('nama_tahun_ajaran', 'DESC')->orderBy('semester', 'DESC')->findAll(),
        ];
    }

    private function subjudul(array $filter): array
    {
        $bagian = [];

        if (! empty($filter['tahun_ajaran_id'])) {
            $tahun = (new TahunAjaranModel())->find($filter['tahun_ajaran_id']);
            $bagian[] = 'Periode: ' . ($tahun ? $tahun['nama_tahun_ajaran'] . ' ' . ucfirst($tahun['semester']) : '-');
        } else {
            $bagian[] = 'Periode: Semua';
        }

        $bagian[] = 'Kelas: ' . ($filter['kelas'] ?: 'Semua');
        if (! empty($filter['tempat_pkl_id'])) {
            $tempat = (new TempatPklModel())->find($filter['tempat_pkl_id']);
            $bagian[] = 'Tempat PKL: ' . ($tempat['nama_perusahaan'] ?? '-');
        }
        $bagian[] = 'Status Penempatan: ' . penempatan_status_label($filter['status'] ?: 'aktif');
        $bagian[] = 'Dicetak: ' . tanggal_indo(date('Y-m-d'), true);

        return $bagian;
    }

    private function identitasSekolah(): array
    {
        $pengaturan = new PengaturanModel();

        return [
            'nama'   => $pengaturan->ambil('nama_sekolah', 'SMK Negeri 1 Subang'),
            'alamat' => $pengaturan->ambil('alamat_sekolah', ''),
            'kepala' => $pengaturan->ambil('kepala_sekolah', ''),
            'logo'   => FCPATH . 'assets/img/logo-sekolah.png',
        ];
    }

    /**
     * Query penempatan terfilter — dasar bagi laporan monitoring, nilai, & kehadiran.
     * Bila $perPage null, seluruh baris diambil (dipakai export Excel/PDF).
     */
    private function penempatanTerfilter(array $filter, ?int $perPage = null, string $group = 'laporan', ?PenempatanPklModel $model = null): array
    {
        $model ??= new PenempatanPklModel();
        $query = $model->withRelasi();

        if (! empty($filter['kelas'])) {
            $query->where('siswa.kelas', $filter['kelas']);
        }
        if (! empty($filter['tempat_pkl_id'])) {
            $query->where('penempatan_pkl.tempat_pkl_id', $filter['tempat_pkl_id']);
        }
        if (! empty($filter['tahun_ajaran_id'])) {
            $query->where('siswa.tahun_ajaran_id', $filter['tahun_ajaran_id']);
        }
        if (! empty($filter['status']) && $filter['status'] !== 'semua') {
            $query->where('penempatan_pkl.status', $filter['status']);
        }
        if (! empty($filter['q'])) {
            $model->cari($filter['q']);
        }

        $query->orderBy('us.nama', 'ASC');

        return $perPage === null ? $query->findAll() : $query->paginate($perPage, $group);
    }

    private function dataMonitoring(array $filter, ?int $perPage = null, string $group = 'monitoring', ?PenempatanPklModel $model = null): array
    {
        $logbookModel = new LogbookModel();
        $penempatan   = $this->penempatanTerfilter($filter, $perPage, $group, $model);

        foreach ($penempatan as &$p) {
            $rekap  = $logbookModel->rekapStatus((int) $p['siswa_id']);
            $persen = $rekap['total'] > 0 ? (int) round(($rekap['disetujui'] / $rekap['total']) * 100) : 0;

            $p['rekap']  = $rekap;
            $p['persen'] = $persen;
        }
        unset($p);

        return $penempatan;
    }

    private function dataSebaran(?array $daftarTempat = null): array
    {
        if ($daftarTempat === null) {
            $daftarTempat = (new TempatPklModel())->orderBy('nama_perusahaan', 'ASC')->findAll();
        }

        if ($daftarTempat === []) {
            return [];
        }

        $penempatanAktif = (new PenempatanPklModel())->withRelasi()
            ->where('penempatan_pkl.status', 'aktif')
            ->whereIn('penempatan_pkl.tempat_pkl_id', array_column($daftarTempat, 'id'))
            ->orderBy('us.nama', 'ASC')
            ->findAll();

        $siswaPerTempat = [];
        foreach ($penempatanAktif as $p) {
            $siswaPerTempat[$p['tempat_pkl_id']][] = $p;
        }

        $hasil = [];
        foreach ($daftarTempat as $t) {
            $hasil[] = [
                'nama_perusahaan' => $t['nama_perusahaan'],
                'bidang_usaha'    => $t['bidang_usaha'],
                'kuota'           => (int) $t['kuota'],
                'terisi'          => count($siswaPerTempat[$t['id']] ?? []),
                'siswa'           => $siswaPerTempat[$t['id']] ?? [],
            ];
        }

        return $hasil;
    }

    private function dataNilai(array $filter, ?int $perPage = null, string $group = 'nilai', ?PenempatanPklModel $model = null): array
    {
        $penempatan     = $this->penempatanTerfilter($filter, $perPage, $group, $model);
        $penilaianModel = new PenilaianModel();
        $detailModel    = new DetailPenilaianModel();

        foreach ($penempatan as &$p) {
            $penilaian = $penilaianModel->findBySiswa((int) $p['siswa_id']);
            $p['penilaian']       = $penilaian;
            $p['nilai_per_aspek'] = [];

            if ($penilaian !== null) {
                foreach ($detailModel->byPenilaian($penilaian['id']) as $d) {
                    $p['nilai_per_aspek'][$d['aspek_penilaian_id']] = number_format((float) $d['nilai'], 1);
                }
            }
        }
        unset($p);

        return $penempatan;
    }

    private function dataKehadiran(array $filter, ?int $perPage = null, string $group = 'kehadiran', ?PenempatanPklModel $model = null): array
    {
        $penempatan   = $this->penempatanTerfilter($filter, $perPage, $group, $model);
        $logbookModel = new LogbookModel();
        $hariIni      = date('Y-m-d');

        foreach ($penempatan as &$p) {
            $sampai    = min($hariIni, $p['tanggal_selesai']);
            $hariKerja = jumlah_hari_kerja($p['tanggal_mulai'], $sampai);
            $terisi    = $logbookModel->hariTerisi((int) $p['siswa_id'], $p['tanggal_mulai'], $sampai);

            $p['hari_kerja']  = $hariKerja;
            $p['hari_terisi'] = $terisi;
            $p['persen']      = $hariKerja > 0 ? (int) round(($terisi / $hariKerja) * 100) : 0;
        }
        unset($p);

        return $penempatan;
    }

    // =====================================================================
    // 5. REKAP PLOTTING SISWA PKL (FORMAT DOKUMEN DATA PKL P2)
    // =====================================================================

    public function plotting()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataPlotting($filter);

        return view('admin/laporan/plotting', array_merge($this->opsiFilter(), [
            'title'        => 'Rekap Plotting Penempatan Siswa PKL (Format P2)',
            'filter'       => $filter,
            'items'        => $data['items'],
            'totalSiswa'   => $data['totalSiswa'],
            'periodeJudul' => $data['periodeJudul'],
        ]));
    }

    public function plottingExcel()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataPlotting($filter);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data PKL');

        // Judul laporan sesuai dokumen Data PKL P2
        $judul = 'DAFTAR SISWA, PEMBIMBING DAN TEMPAT PKL ' . strtoupper($data['periodeJudul']);
        $sheet->setCellValue('A2', $judul);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header tabel
        $headers = ['NO', 'TEMPAT PKL', 'NAMA PEMBIMBING', 'NAMA SISWA', 'KELAS', "Jumlah\nSiswa"];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F'];
        $barisHeader = 4;

        foreach ($headers as $i => $h) {
            $sheet->setCellValue("{$cols[$i]}{$barisHeader}", $h);
        }

        // Style header warna hijau pastel #C6E0B4 persis dokumen P2
        $sheet->getStyle("A{$barisHeader}:F{$barisHeader}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("A{$barisHeader}:F{$barisHeader}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle("A{$barisHeader}:F{$barisHeader}")
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6E0B4');
        $sheet->getRowDimension($barisHeader)->setRowHeight(28);

        $baris = 5;
        $no    = 1;

        foreach ($data['items'] as $item) {
            $jumlahSiswa = count($item['siswa']);
            $barisMulai  = $baris;
            $barisAkhir  = $baris + $jumlahSiswa - 1;

            if ($jumlahSiswa > 1) {
                $sheet->mergeCells("A{$barisMulai}:A{$barisAkhir}");
                $sheet->mergeCells("B{$barisMulai}:B{$barisAkhir}");
                $sheet->mergeCells("C{$barisMulai}:C{$barisAkhir}");
                $sheet->mergeCells("F{$barisMulai}:F{$barisAkhir}");
            }

            $sheet->setCellValue("A{$barisMulai}", $no++);
            $sheet->setCellValue("B{$barisMulai}", $item['nama_perusahaan']);
            $sheet->setCellValue("C{$barisMulai}", $item['nama_pembimbing']);
            $sheet->setCellValue("F{$barisMulai}", $jumlahSiswa);

            foreach ($item['siswa'] as $sIndex => $s) {
                $barisSiswa = $barisMulai + $sIndex;
                $sheet->setCellValue("D{$barisSiswa}", $s['nama_siswa']);
                $sheet->setCellValue("E{$barisSiswa}", $s['kelas']);
            }

            // Alignment
            $sheet->getStyle("A{$barisMulai}:A{$barisAkhir}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$barisMulai}:B{$barisAkhir}")->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle("C{$barisMulai}:C{$barisAkhir}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle("D{$barisMulai}:D{$barisAkhir}")->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("E{$barisMulai}:E{$barisAkhir}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("F{$barisMulai}:F{$barisAkhir}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

            $baris = $barisAkhir + 1;
        }

        // Baris kuning penutup di bawah (seperti di halaman 7 PDF)
        $barisTotal = $baris;
        $sheet->setCellValue("A{$barisTotal}", 'JUMLAH SISWA PKL ' . strtoupper($data['periodeJudul']));
        $sheet->mergeCells("A{$barisTotal}:E{$barisTotal}");
        $sheet->setCellValue("F{$barisTotal}", $data['totalSiswa']);

        $sheet->getStyle("A{$barisTotal}:F{$barisTotal}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("A{$barisTotal}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$barisTotal}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$barisTotal}:F{$barisTotal}")
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');

        // Border tipis semua sel
        $sheet->getStyle("A{$barisHeader}:F{$barisTotal}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(38);
        $sheet->getColumnDimension('C')->setWidth(26);
        $sheet->getColumnDimension('D')->setWidth(32);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(12);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        ob_start();
        $writer->save('php://output');
        $konten = ob_get_clean();

        return service('response')
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="data-pkl-p2-' . date('Y-m-d') . '.xlsx"')
            ->setBody($konten);
    }

    public function plottingPdf()
    {
        $filter = $this->filterUmum();
        $data   = $this->dataPlotting($filter);

        return (new PdfExport())->unduh('admin/laporan/pdf/plotting_p2', [
            'items'        => $data['items'],
            'totalSiswa'   => $data['totalSiswa'],
            'periodeJudul' => $data['periodeJudul'],
            'sekolah'      => $this->identitasSekolah(),
        ], 'data-pkl-p2-' . date('Y-m-d') . '.pdf', 'portrait');
    }

    private function dataPlotting(array $filter): array
    {
        $penempatanModel = new PenempatanPklModel();
        $builder = $penempatanModel->withRelasi();

        if (! empty($filter['tahun_ajaran_id'])) {
            $builder->where('siswa.tahun_ajaran_id', $filter['tahun_ajaran_id']);
        }

        if (! empty($filter['status']) && $filter['status'] !== 'semua') {
            $builder->where('penempatan_pkl.status', $filter['status']);
        }

        if (! empty($filter['q'])) {
            $penempatanModel->cari($filter['q']);
        }

        $raw = $builder->orderBy('tempat_pkl.nama_perusahaan', 'ASC')
            ->orderBy('penempatan_pkl.guru_pembimbing_id', 'ASC')
            ->orderBy('siswa.kelas', 'ASC')
            ->orderBy('us.nama', 'ASC')
            ->findAll();

        $grouped    = [];
        $totalSiswa = 0;

        foreach ($raw as $row) {
            $tempatId = (int) $row['tempat_pkl_id'];
            $guruId   = (int) $row['guru_pembimbing_id'];
            $key      = $tempatId . '_' . $guruId;

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'nama_perusahaan' => $row['nama_perusahaan'] . ($row['alamat_perusahaan'] ? ', ' . $row['alamat_perusahaan'] : ''),
                    'nama_pembimbing' => $row['nama_guru'],
                    'nama_instruktur' => $row['nama_pembimbing_lapangan'] ?? '-',
                    'siswa'           => [],
                ];
            }

            $grouped[$key]['siswa'][] = [
                'nama_siswa' => $row['nama_siswa'],
                'kelas'      => $row['kelas'],
                'nis'        => $row['nis'],
                'jurusan'    => $row['jurusan'],
            ];
            $totalSiswa++;
        }

        $periodeJudul = 'Periode 2 Juli - Desember 2026';
        if (! empty($filter['tahun_ajaran_id'])) {
            $tahun = (new TahunAjaranModel())->find($filter['tahun_ajaran_id']);
            if ($tahun) {
                $periodeJudul = $tahun['nama_tahun_ajaran'] . ' (' . ucfirst($tahun['semester']) . ')';
            }
        }

        return [
            'items'        => array_values($grouped),
            'totalSiswa'   => $totalSiswa,
            'periodeJudul' => $periodeJudul,
        ];
    }
}
