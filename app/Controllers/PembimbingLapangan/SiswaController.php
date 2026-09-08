<?php

namespace App\Controllers\PembimbingLapangan;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\PenilaianModel;
use App\Models\SiswaModel;

class SiswaController extends BaseController
{
    public function index()
    {
        $pembimbingId = (int) $this->session->get('profile_id');
        $status       = $this->request->getGet('status') ?: 'aktif';
        $keyword      = trim((string) $this->request->getGet('q'));

        $penempatanModel = new PenempatanPklModel();
        $query           = $penempatanModel->byPembimbingLapanganBuilder($pembimbingId, $status === 'semua' ? '' : $status);

        if ($keyword !== '') {
            $penempatanModel->cari($keyword);
        }

        $binaan         = $query->paginate(15, 'binaan');
        $penilaianModel = new PenilaianModel();

        $siswaBimbingan = [];

        foreach ($binaan as $p) {
            $siswaBimbingan[] = [
                'penempatan' => $p,
                'penilaian'  => $penilaianModel->findBySiswa((int) $p['siswa_id']),
            ];
        }

        return view('pembimbing_lapangan/siswa/index', [
            'title'          => 'Siswa Bimbingan',
            'siswaBimbingan' => $siswaBimbingan,
            'status'         => $status,
            'keyword'        => $keyword,
            'pager'          => $penempatanModel->pager,
        ]);
    }

    public function show(int $siswaId)
    {
        $pembimbingId = (int) $this->session->get('profile_id');

        $pernahMembimbing = (new PenempatanPklModel())
            ->where('siswa_id', $siswaId)
            ->where('pembimbing_lapangan_id', $pembimbingId)
            ->countAllResults() > 0;

        if (! $pernahMembimbing) {
            return redirect()->to('/pembimbing-lapangan/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $siswa        = (new SiswaModel())->getDetail($siswaId);
        $logbookModel = new LogbookModel();

        return view('pembimbing_lapangan/siswa/show', [
            'title'          => $siswa['nama'] ?? 'Detail Siswa',
            'siswa'          => $siswa,
            'rekap'          => $logbookModel->rekapStatus($siswaId),
            'logbookTerbaru' => $logbookModel->where('siswa_id', $siswaId)->where('status !=', 'draft')->orderBy('tanggal_kegiatan', 'DESC')->findAll(8),
            'penempatan'     => (new PenempatanPklModel())->withRelasi()->where('penempatan_pkl.siswa_id', $siswaId)->where('penempatan_pkl.pembimbing_lapangan_id', $pembimbingId)->orderBy('penempatan_pkl.tanggal_mulai', 'DESC')->first(),
            'penilaian'      => (new PenilaianModel())->findBySiswa($siswaId),
        ]);
    }
}
