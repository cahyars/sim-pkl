<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\SiswaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $siswaId = $this->session->get('profile_id');
        $siswa   = (new SiswaModel())->getDetail((int) $siswaId);

        $penempatan   = (new PenempatanPklModel())->withRelasi()
            ->where('penempatan_pkl.siswa_id', $siswaId)
            ->where('penempatan_pkl.status', 'aktif')
            ->first();

        $logbookModel = new LogbookModel();
        $rekap        = $siswaId ? $logbookModel->rekapStatus((int) $siswaId) : [
            'draft' => 0, 'menunggu_validasi' => 0, 'disetujui' => 0, 'revisi' => 0, 'ditolak' => 0, 'total' => 0,
        ];

        $logbookTerbaru = $siswaId
            ? $logbookModel->where('siswa_id', $siswaId)->orderBy('tanggal_kegiatan', 'DESC')->findAll(5)
            : [];

        return view('siswa/dashboard', [
            'title'          => 'Dashboard',
            'siswa'          => $siswa,
            'penempatan'     => $penempatan,
            'rekap'          => $rekap,
            'logbookTerbaru' => $logbookTerbaru,
        ]);
    }
}
