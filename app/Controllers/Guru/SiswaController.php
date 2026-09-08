<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;
use App\Models\PenilaianModel;
use App\Models\SiswaModel;

class SiswaController extends BaseController
{
    public function index()
    {
        $guruId  = (int) $this->session->get('profile_id');
        $status  = $this->request->getGet('status') ?: 'aktif';
        $keyword = trim((string) $this->request->getGet('q'));

        $penempatanModel = new PenempatanPklModel();
        $query           = $penempatanModel->byGuruBuilder($guruId, $status === 'semua' ? '' : $status);

        if ($keyword !== '') {
            $penempatanModel->cari($keyword);
        }

        $binaan       = $query->paginate(15, 'binaan');
        $logbookModel = new LogbookModel();
        $reminderHari = (int) (new PengaturanModel())->ambil('reminder_hari', 3);
        $hariIni      = date('Y-m-d');

        $siswaBimbingan = [];

        foreach ($binaan as $p) {
            $rekap       = $logbookModel->rekapStatus((int) $p['siswa_id']);
            $terakhirIsi = $logbookModel->tanggalLogbookTerakhir((int) $p['siswa_id']);
            $selisihHari = $terakhirIsi ? (int) ((strtotime($hariIni) - strtotime($terakhirIsi)) / 86400) : null;

            $siswaBimbingan[] = [
                'penempatan'   => $p,
                'rekap'        => $rekap,
                'terakhir_isi' => $terakhirIsi,
                'selisih_hari' => $selisihHari,
                'belum_aktif'  => $p['status'] === 'aktif' && ($selisihHari === null || $selisihHari > $reminderHari),
            ];
        }

        return view('guru/siswa/index', [
            'title'          => 'Siswa Bimbingan',
            'siswaBimbingan' => $siswaBimbingan,
            'status'         => $status,
            'keyword'        => $keyword,
            'pager'          => $penempatanModel->pager,
        ]);
    }

    public function show(int $siswaId)
    {
        $guruId = (int) $this->session->get('profile_id');

        $pernahMembimbing = (new PenempatanPklModel())
            ->where('siswa_id', $siswaId)
            ->where('guru_pembimbing_id', $guruId)
            ->countAllResults() > 0;

        if (! $pernahMembimbing) {
            return redirect()->to('/guru/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $siswa        = (new SiswaModel())->getDetail($siswaId);
        $logbookModel = new LogbookModel();

        $status = $this->request->getGet('status');
        $keyword = trim((string) $this->request->getGet('q'));
        $query  = $logbookModel->where('siswa_id', $siswaId);
        if (! empty($status)) {
            $query->where('status', $status);
        }
        if ($keyword !== '') {
            $query->like('uraian_kegiatan', $keyword);
        }
        $logbook = $query->orderBy('tanggal_kegiatan', 'DESC')->paginate(15, 'logbook');

        return view('guru/siswa/show', [
            'title'      => $siswa['nama'] ?? 'Detail Siswa',
            'siswa'      => $siswa,
            'rekap'      => $logbookModel->rekapStatus($siswaId),
            'logbook'    => $logbook,
            'pager'      => $logbookModel->pager,
            'status'     => $status,
            'keyword'    => $keyword,
            'penempatan' => (new PenempatanPklModel())->withRelasi()->where('penempatan_pkl.siswa_id', $siswaId)->where('penempatan_pkl.guru_pembimbing_id', $guruId)->orderBy('penempatan_pkl.tanggal_mulai', 'DESC')->first(),
            'penilaian'  => (new PenilaianModel())->findBySiswa($siswaId),
        ]);
    }
}
