<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $guruId        = $this->session->get('profile_id');
        $binaan        = $guruId ? (new PenempatanPklModel())->byGuru((int) $guruId, 'aktif') : [];
        $logbookModel  = new LogbookModel();
        $reminderHari  = (int) (new PengaturanModel())->ambil('reminder_hari', 3);
        $hariIni       = date('Y-m-d');

        $siswaBimbingan = [];

        foreach ($binaan as $p) {
            $rekap          = $logbookModel->rekapStatus((int) $p['siswa_id']);
            $terakhirIsi    = $logbookModel->tanggalLogbookTerakhir((int) $p['siswa_id']);
            $selisihHari    = $terakhirIsi ? (int) ((strtotime($hariIni) - strtotime($terakhirIsi)) / 86400) : null;

            $siswaBimbingan[] = [
                'penempatan'    => $p,
                'rekap'         => $rekap,
                'terakhir_isi'  => $terakhirIsi,
                'selisih_hari'  => $selisihHari,
                'belum_aktif'   => $selisihHari === null || $selisihHari > $reminderHari,
            ];
        }

        $totalMenunggu = array_sum(array_column(array_column($siswaBimbingan, 'rekap'), 'menunggu_validasi'));
        $totalBelumAktif = count(array_filter($siswaBimbingan, static fn ($s) => $s['belum_aktif']));

        return view('guru/dashboard', [
            'title'           => 'Dashboard',
            'siswaBimbingan'  => $siswaBimbingan,
            'reminderHari'    => $reminderHari,
            'totalBimbingan'  => count($siswaBimbingan),
            'totalMenunggu'   => $totalMenunggu,
            'totalBelumAktif' => $totalBelumAktif,
            'grafik'          => $this->dataGrafik($siswaBimbingan),
        ]);
    }

    /**
     * Siapkan data siap-pakai untuk grafik progres di dashboard (Fitur §4.3):
     * 1. Bar chart persentase logbook disetujui per siswa bimbingan.
     * 2. Doughnut chart komposisi status seluruh logbook siswa bimbingan.
     */
    private function dataGrafik(array $siswaBimbingan): array
    {
        $label   = [];
        $persen  = [];
        $komposisi = ['disetujui' => 0, 'menunggu_validasi' => 0, 'revisi' => 0, 'ditolak' => 0, 'draft' => 0];

        foreach ($siswaBimbingan as $s) {
            $rekap = $s['rekap'];

            // Nama depan saja supaya label sumbu X tidak terlalu panjang.
            $label[]  = explode(' ', trim($s['penempatan']['nama_siswa']))[0];
            $persen[] = $rekap['total'] > 0 ? (int) round(($rekap['disetujui'] / $rekap['total']) * 100) : 0;

            foreach (array_keys($komposisi) as $status) {
                $komposisi[$status] += $rekap[$status] ?? 0;
            }
        }

        return [
            'label'     => $label,
            'persen'    => $persen,
            'komposisi' => array_values($komposisi),
            'adaData'   => array_sum($komposisi) > 0,
        ];
    }
}
