<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;

class PresensiController extends BaseController
{
    public function index()
    {
        $siswaId    = (int) $this->session->get('profile_id');
        $penempatan = (new PenempatanPklModel())->getAktifBySiswa($siswaId);

        $bulan = $this->request->getGet('bulan') ?: date('Y-m');
        if (! preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $bulan = date('Y-m');
        }

        $kalender = ['hari' => [], 'rekap' => ['masuk' => 0, 'izin' => 0, 'sakit' => 0, 'belum_mengisi' => 0]];

        if ($penempatan !== null) {
            $kalender = (new LogbookModel())->kalenderPresensi($siswaId, $penempatan, $bulan);
        }

        $bulanSebelumnya = date('Y-m', strtotime($bulan . '-01 -1 month'));
        $bulanBerikutnya = date('Y-m', strtotime($bulan . '-01 +1 month'));

        return view('siswa/presensi/index', [
            'title'           => 'Presensi',
            'penempatan'      => $penempatan,
            'bulan'           => $bulan,
            'namaBulan'       => tanggal_bulan_tahun($bulan),
            'bulanSebelumnya' => $bulanSebelumnya,
            'bulanBerikutnya' => $bulanBerikutnya,
            'batasBulanAwal'  => $penempatan ? substr($penempatan['tanggal_mulai'], 0, 7) : null,
            'batasBulanAkhir' => $penempatan ? min(date('Y-m'), substr($penempatan['tanggal_selesai'], 0, 7)) : null,
            'hari'            => $kalender['hari'],
            'rekap'           => $kalender['rekap'],
        ]);
    }
}
