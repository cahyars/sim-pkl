<?php

namespace App\Controllers\PembimbingLapangan;

use App\Controllers\BaseController;
use App\Models\PenempatanPklModel;
use App\Models\PenilaianModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $pembimbingId = $this->session->get('profile_id');
        $binaan       = $pembimbingId ? (new PenempatanPklModel())->byPembimbingLapangan((int) $pembimbingId, 'aktif') : [];
        $penilaianModel = new PenilaianModel();

        $siswaBimbingan = [];

        foreach ($binaan as $p) {
            $penilaian = $penilaianModel->findBySiswa((int) $p['siswa_id']);

            $siswaBimbingan[] = [
                'penempatan' => $p,
                'penilaian'  => $penilaian,
            ];
        }

        $totalSudahDinilai = count(array_filter($siswaBimbingan, static fn ($s) => $s['penilaian'] !== null));

        return view('pembimbing_lapangan/dashboard', [
            'title'             => 'Dashboard',
            'siswaBimbingan'    => $siswaBimbingan,
            'totalBimbingan'    => count($siswaBimbingan),
            'totalSudahDinilai' => $totalSudahDinilai,
        ]);
    }
}
