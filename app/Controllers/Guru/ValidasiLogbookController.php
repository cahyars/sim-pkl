<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\DokumentasiLogbookModel;
use App\Models\LogbookModel;
use App\Models\NotifikasiModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class ValidasiLogbookController extends BaseController
{
    public function index()
    {
        $guruId  = (int) $this->session->get('profile_id');
        $status  = $this->request->getGet('status') ?: 'menunggu_validasi';
        $keyword = trim((string) $this->request->getGet('q'));

        $query = (new LogbookModel())->withSiswa()
            ->join('penempatan_pkl', 'penempatan_pkl.id = logbook.penempatan_pkl_id')
            ->where('penempatan_pkl.guru_pembimbing_id', $guruId);

        if ($status !== 'semua') {
            $query->where('logbook.status', $status);
        }

        if ($keyword !== '') {
            $query->groupStart()
                ->like('users.nama', $keyword)
                ->orLike('siswa.nis', $keyword)
                ->orLike('siswa.kelas', $keyword)
                ->orLike('logbook.uraian_kegiatan', $keyword)
                ->groupEnd();
        }

        $logbook = $query->orderBy('logbook.tanggal_kegiatan', 'DESC')->paginate(15, 'validasi');

        return view('guru/validasi/index', [
            'title'   => 'Validasi Logbook',
            'logbook' => $logbook,
            'pager'   => $query->pager,
            'status'  => $status,
            'keyword' => $keyword,
        ]);
    }

    public function show(int $id)
    {
        $kembali = $this->amankanKembali($this->request->getGet('kembali'));
        $logbook = $this->ambilBimbingan($id);

        if ($logbook === null) {
            return redirect()->to($kembali)->with('error', 'Logbook tidak ditemukan atau bukan siswa bimbingan Anda.');
        }

        return view('guru/validasi/show', [
            'title'       => 'Detail Logbook',
            'logbook'     => $logbook,
            'dokumentasi' => (new DokumentasiLogbookModel())->byLogbook($id),
            'kembali'     => $kembali,
        ]);
    }

    public function setujui(int $id)
    {
        $kembali = $this->amankanKembali($this->request->getPost('kembali'));
        $logbook = $this->pastikanBisaDivalidasi($id, $kembali);
        if ($logbook instanceof RedirectResponse) {
            return $logbook;
        }

        $catatan = trim((string) $this->request->getPost('catatan_guru'));

        (new LogbookModel())->update($id, [
            'status'       => 'disetujui',
            'catatan_guru' => $catatan !== '' ? $catatan : null,
            'validated_by' => (int) $this->session->get('profile_id'),
            'validated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->kirimNotifikasiSiswa($logbook, 'disetujui', $catatan);

        return redirect()->to($kembali)
            ->with('success', "Logbook {$logbook['nama_siswa']} tanggal " . tanggal_indo($logbook['tanggal_kegiatan']) . ' disetujui.');
    }

    public function revisi(int $id)
    {
        $kembali = $this->amankanKembali($this->request->getPost('kembali'));
        $logbook = $this->pastikanBisaDivalidasi($id, $kembali);
        if ($logbook instanceof RedirectResponse) {
            return $logbook;
        }

        $catatan = trim((string) $this->request->getPost('catatan_guru'));

        if ($catatan === '') {
            return redirect()->to('/guru/validasi/' . $id . '?kembali=' . urlencode($kembali))
                ->withInput()->with('error', 'Catatan revisi wajib diisi.');
        }

        (new LogbookModel())->update($id, [
            'status'       => 'revisi',
            'catatan_guru' => $catatan,
            'validated_by' => (int) $this->session->get('profile_id'),
            'validated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->kirimNotifikasiSiswa($logbook, 'revisi', $catatan);

        return redirect()->to($kembali)
            ->with('success', "Logbook {$logbook['nama_siswa']} tanggal " . tanggal_indo($logbook['tanggal_kegiatan']) . ' dikembalikan untuk revisi.');
    }

    public function tolak(int $id)
    {
        $kembali = $this->amankanKembali($this->request->getPost('kembali'));
        $logbook = $this->pastikanBisaDivalidasi($id, $kembali);
        if ($logbook instanceof RedirectResponse) {
            return $logbook;
        }

        $catatan = trim((string) $this->request->getPost('catatan_guru'));

        if ($catatan === '') {
            return redirect()->to('/guru/validasi/' . $id . '?kembali=' . urlencode($kembali))
                ->withInput()->with('error', 'Alasan penolakan wajib diisi.');
        }

        (new LogbookModel())->update($id, [
            'status'       => 'ditolak',
            'catatan_guru' => $catatan,
            'validated_by' => (int) $this->session->get('profile_id'),
            'validated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->kirimNotifikasiSiswa($logbook, 'ditolak', $catatan);

        return redirect()->to($kembali)
            ->with('success', "Logbook {$logbook['nama_siswa']} tanggal " . tanggal_indo($logbook['tanggal_kegiatan']) . ' ditolak.');
    }

    public function file(int $dokId)
    {
        $dok = (new DokumentasiLogbookModel())->find($dokId);

        if ($dok === null || $this->ambilBimbingan((int) $dok['logbook_id']) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/dokumentasi/' . $dok['file_path'];

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->setContentType($dok['file_type'])->setBody(file_get_contents($path));
    }

    /**
     * Ambil logbook milik siswa bimbingan guru yang login. Mengembalikan null
     * kalau logbook tidak ada ATAU siswa tersebut bukan bimbingan guru ini
     * (tanpa membedakan pesan, supaya tidak membocorkan data siswa lain — blackbox #17).
     */
    private function ambilBimbingan(int $id): ?array
    {
        $guruId = (int) $this->session->get('profile_id');

        return (new LogbookModel())->withSiswa()
            ->join('penempatan_pkl', 'penempatan_pkl.id = logbook.penempatan_pkl_id')
            ->where('logbook.id', $id)
            ->where('penempatan_pkl.guru_pembimbing_id', $guruId)
            ->first();
    }

    /**
     * @return array|RedirectResponse Array logbook jika valid, atau response redirect siap dikembalikan.
     */
    private function pastikanBisaDivalidasi(int $id, string $kembali)
    {
        $logbook = $this->ambilBimbingan($id);

        if ($logbook === null) {
            return redirect()->to($kembali)->with('error', 'Logbook tidak ditemukan atau bukan siswa bimbingan Anda.');
        }

        if ($logbook['status'] !== 'menunggu_validasi') {
            return redirect()->to($kembali)
                ->with('error', 'Logbook ini berstatus "' . logbook_status_label($logbook['status']) . '", tidak dapat divalidasi ulang.');
        }

        return $logbook;
    }

    /**
     * Terima URL "kembali ke sini setelah validasi" dari query/form, tapi hanya
     * kalau mengarah ke halaman guru sendiri — mencegah open-redirect.
     */
    private function amankanKembali(?string $url): string
    {
        $default = site_url('guru/validasi');

        if (empty($url) || ! str_starts_with($url, site_url('guru/'))) {
            return $default;
        }

        return $url;
    }

    private function kirimNotifikasiSiswa(array $logbook, string $status, string $catatan): void
    {
        $tanggal = tanggal_indo($logbook['tanggal_kegiatan']);

        $judul = [
            'disetujui' => 'Logbook Disetujui',
            'revisi'    => 'Logbook Perlu Direvisi',
            'ditolak'   => 'Logbook Ditolak',
        ][$status];

        $pesan = match ($status) {
            'disetujui' => "Logbook tanggal {$tanggal} telah disetujui oleh guru pembimbing." . ($catatan !== '' ? " Catatan: {$catatan}" : ''),
            'revisi'    => "Logbook tanggal {$tanggal} perlu direvisi. Catatan guru: {$catatan}",
            'ditolak'   => "Logbook tanggal {$tanggal} ditolak oleh guru pembimbing. Alasan: {$catatan}",
        };

        $siswa = (new SiswaModel())->find($logbook['siswa_id']);

        if ($siswa !== null) {
            (new NotifikasiModel())->kirim((int) $siswa['user_id'], $judul, $pesan, 'validasi', '/siswa/logbook/' . $logbook['id']);
        }
    }
}
