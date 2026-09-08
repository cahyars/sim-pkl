<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruPembimbingModel;
use App\Models\PembimbingLapanganModel;
use App\Models\SiswaModel;
use App\Models\TahunAjaranModel;
use App\Models\TempatPklModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $siswaModel  = new SiswaModel();
        $tempatModel = new TempatPklModel();
        $tahunModel  = new TahunAjaranModel();

        $sesiTahunId  = (int) (session()->get('tahun_ajaran_id') ?: 0);
        $tahunAktif   = $tahunModel->getAktif();
        $tahunPilihan = $sesiTahunId > 0 ? $tahunModel->find($sesiTahunId) : $tahunAktif;
        if ($tahunPilihan === null) {
            $tahunPilihan = $tahunAktif ?? $tahunModel->orderBy('id', 'DESC')->first();
        }

        $isArsip = $tahunPilihan && (int) $tahunPilihan['is_active'] !== 1;

        // Filter siswa berdasarkan tahun ajaran terpilih
        $siswaQuery = (new SiswaModel())->where('tahun_ajaran_id', $tahunPilihan['id'] ?? 0);
        $totalSiswa = (clone $siswaQuery)->countAllResults();
        $siswaAktif = (clone $siswaQuery)->where('status_pkl', 'aktif')->countAllResults();
        $siswaBelumTempat = (clone $siswaQuery)->where('status_pkl', 'belum_ditempatkan')->countAllResults();
        $siswaSelesai = (clone $siswaQuery)->where('status_pkl', 'selesai')->countAllResults();

        // Jika bukan arsip dan filter nol karena siswa belum dialokasikan ke id tahun baru, ambil semua
        if ($totalSiswa === 0 && ! $isArsip) {
            $totalSiswa       = $siswaModel->countAllResults();
            $siswaAktif       = $siswaModel->where('status_pkl', 'aktif')->countAllResults();
            $siswaBelumTempat = $siswaModel->where('status_pkl', 'belum_ditempatkan')->countAllResults();
            $siswaSelesai     = $siswaModel->where('status_pkl', 'selesai')->countAllResults();
        }

        $sebaranTempat = $tempatModel->withKuotaTerpakai();
        $totalKuota    = array_sum(array_column($sebaranTempat, 'kuota'));
        $totalTerisi   = array_sum(array_column($sebaranTempat, 'terisi'));

        $data = [
            'title'            => 'Dashboard',
            'totalSiswa'       => $totalSiswa,
            'siswaAktif'       => $siswaAktif,
            'siswaBelumTempat' => $siswaBelumTempat,
            'siswaSelesai'     => $siswaSelesai,
            'totalTempatPkl'   => $tempatModel->where('is_active', 1)->countAllResults(),
            'totalGuru'        => (new GuruPembimbingModel())->countAllResults(),
            'totalPembimbing'  => (new PembimbingLapanganModel())->countAllResults(),
            'tahunAjaranAktif' => $tahunPilihan,
            'isArsip'          => $isArsip,
            'sebaranTempat'    => $sebaranTempat,
            'totalKuota'       => $totalKuota,
            'totalTerisi'      => $totalTerisi,
        ];

        return view('admin/dashboard', $data);
    }
}
