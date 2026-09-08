<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Libraries\ExcelExport;
use App\Libraries\PdfExport;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;

class LaporanController extends BaseController
{
    public function index()
    {
        $status  = $this->request->getGet('status') ?: 'aktif';
        $keyword = trim((string) $this->request->getGet('q'));
        $model   = new PenempatanPklModel();

        return view('guru/laporan/index', [
            'title'   => 'Laporan Monitoring',
            'status'  => $status,
            'keyword' => $keyword,
            'data'    => $this->dataLaporan($status, $keyword, 15, 'laporan', $model),
            'pager'   => $model->pager,
        ]);
    }

    public function excel()
    {
        $status  = $this->request->getGet('status') ?: 'aktif';
        $data    = $this->dataLaporan($status, trim((string) $this->request->getGet('q')));
        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Tempat PKL', 'Total Logbook', 'Disetujui', 'Menunggu', 'Revisi', 'Ditolak', 'Progres (%)', 'Keaktifan (%)'];

        $rows = [];
        foreach ($data as $i => $d) {
            $rows[] = [
                $i + 1, $d['nis'], $d['nama_siswa'], $d['kelas'], $d['nama_perusahaan'],
                $d['rekap']['total'], $d['rekap']['disetujui'], $d['rekap']['menunggu_validasi'],
                $d['rekap']['revisi'], $d['rekap']['ditolak'], $d['persen'], $d['keaktifan'],
            ];
        }

        return (new ExcelExport())->unduh(
            'Laporan Monitoring Siswa Bimbingan',
            ['Guru: ' . $this->session->get('nama'), 'Status: ' . penempatan_status_label($status === 'semua' ? 'aktif' : $status), 'Dicetak: ' . tanggal_indo(date('Y-m-d'), true)],
            $headers,
            $rows,
            'laporan-monitoring-bimbingan-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function pdf()
    {
        $status = $this->request->getGet('status') ?: 'aktif';
        $data   = $this->dataLaporan($status, trim((string) $this->request->getGet('q')));

        $pengaturan = new PengaturanModel();

        return (new PdfExport())->unduh('guru/laporan/pdf', [
            'data'    => $data,
            'guru'    => $this->session->get('nama'),
            'status'  => $status,
            'sekolah' => [
                'nama'   => $pengaturan->ambil('nama_sekolah', 'SMK Negeri 1 Subang'),
                'alamat' => $pengaturan->ambil('alamat_sekolah', ''),
                'kepala' => $pengaturan->ambil('kepala_sekolah', ''),
                'logo'   => FCPATH . 'assets/img/logo-sekolah.png',
            ],
        ], 'laporan-monitoring-bimbingan-' . date('Y-m-d') . '.pdf', 'landscape');
    }

    /**
     * Data laporan monitoring bimbingan. Bila $perPage null, seluruh baris diambil
     * (dipakai export Excel/PDF).
     */
    private function dataLaporan(string $status, string $keyword = '', ?int $perPage = null, string $group = 'laporan', ?PenempatanPklModel $model = null): array
    {
        $guruId = (int) $this->session->get('profile_id');
        $model ??= new PenempatanPklModel();
        $query  = $model->byGuruBuilder($guruId, $status === 'semua' ? '' : $status);

        if ($keyword !== '') {
            $model->cari($keyword);
        }

        $binaan       = $perPage === null ? $query->findAll() : $query->paginate($perPage, $group);
        $logbookModel = new LogbookModel();
        $hariIni      = date('Y-m-d');

        foreach ($binaan as &$p) {
            $rekap  = $logbookModel->rekapStatus((int) $p['siswa_id']);
            $sampai = min($hariIni, $p['tanggal_selesai']);

            $hariKerja = jumlah_hari_kerja($p['tanggal_mulai'], $sampai);
            $terisi    = $logbookModel->hariTerisi((int) $p['siswa_id'], $p['tanggal_mulai'], $sampai);

            $p['rekap']     = $rekap;
            $p['persen']    = $rekap['total'] > 0 ? (int) round(($rekap['disetujui'] / $rekap['total']) * 100) : 0;
            $p['keaktifan'] = $hariKerja > 0 ? (int) round(($terisi / $hariKerja) * 100) : 0;
        }
        unset($p);

        return $binaan;
    }
}
