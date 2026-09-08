<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruPembimbingModel;
use App\Models\PembimbingLapanganModel;
use App\Models\PenempatanPklModel;
use App\Models\SiswaModel;
use App\Models\TempatPklModel;

class PenempatanController extends BaseController
{
    public function index()
    {
        $model   = new PenempatanPklModel();
        $status  = $this->request->getGet('status') ?: 'aktif';
        $keyword = trim((string) $this->request->getGet('q'));

        $query = $model->withRelasi();

        if ($status !== 'semua') {
            $query->where('penempatan_pkl.status', $status);
        }

        if ($keyword !== '') {
            $model->cari($keyword);
        }

        $penempatan = $query->orderBy('penempatan_pkl.created_at', 'DESC')->paginate(15, 'penempatan');

        return view('admin/penempatan/index', [
            'title'      => 'Penempatan PKL',
            'penempatan' => $penempatan,
            'pager'      => $model->pager,
            'status'     => $status,
            'keyword'    => $keyword,
        ]);
    }

    public function create()
    {
        return view('admin/penempatan/create', [
            'title'       => 'Assign Penempatan PKL',
            'siswa'       => (new SiswaModel())->tanpaPenempatanAktif(),
            'tempat'      => (new TempatPklModel())->withKuotaTerpakai(),
            'guru'        => (new GuruPembimbingModel())->withUser()->orderBy('users.nama', 'ASC')->findAll(),
            'pembimbing'  => (new PembimbingLapanganModel())->withTempat()->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $penempatanModel = new PenempatanPklModel();

        $rules = [
            'siswa_id'               => 'required|is_natural_no_zero',
            'tempat_pkl_id'          => 'required|is_natural_no_zero',
            'guru_pembimbing_id'     => 'required|is_natural_no_zero',
            'pembimbing_lapangan_id' => 'permit_empty|is_natural_no_zero',
            'tanggal_mulai'          => 'required|valid_date[Y-m-d]',
            'tanggal_selesai'        => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $siswaId = (int) $this->request->getPost('siswa_id');

        if ($penempatanModel->punyaPenempatanAktif($siswaId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Siswa ini sudah memiliki penempatan PKL aktif. Selesaikan atau batalkan penempatan lama terlebih dahulu sebelum menempatkan ulang.');
        }

        if (strtotime((string) $this->request->getPost('tanggal_selesai')) < strtotime((string) $this->request->getPost('tanggal_mulai'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai tidak boleh sebelum tanggal mulai.');
        }

        $db = db_connect();
        $db->transStart();

        $penempatanModel->insert([
            'siswa_id'               => $siswaId,
            'tempat_pkl_id'          => $this->request->getPost('tempat_pkl_id'),
            'guru_pembimbing_id'     => $this->request->getPost('guru_pembimbing_id'),
            'pembimbing_lapangan_id' => $this->request->getPost('pembimbing_lapangan_id') ?: null,
            'tanggal_mulai'          => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'        => $this->request->getPost('tanggal_selesai'),
            'status'                 => 'aktif',
            'keterangan'             => $this->request->getPost('keterangan'),
        ]);

        (new SiswaModel())->update($siswaId, ['status_pkl' => 'aktif']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penempatan. Silakan coba lagi.');
        }

        return redirect()->to('/admin/penempatan')->with('success', 'Siswa berhasil ditempatkan PKL.');
    }

    public function riwayat(int $siswaId)
    {
        $siswa = (new SiswaModel())->getDetail($siswaId);

        if ($siswa === null) {
            return redirect()->to('/admin/penempatan')->with('error', 'Data siswa tidak ditemukan.');
        }

        $model  = new PenempatanPklModel();
        $status = $this->request->getGet('status') ?: '';

        $query = $model->withRelasi()->where('penempatan_pkl.siswa_id', $siswaId);

        if ($status !== '') {
            $query->where('penempatan_pkl.status', $status);
        }

        $riwayat = $query->orderBy('penempatan_pkl.tanggal_mulai', 'DESC')->paginate(10, 'riwayat');

        return view('admin/penempatan/riwayat', [
            'title'   => 'Riwayat Penempatan - ' . $siswa['nama'],
            'siswa'   => $siswa,
            'riwayat' => $riwayat,
            'pager'   => $model->pager,
            'status'  => $status,
        ]);
    }

    public function selesaikan(int $id)
    {
        $model      = new PenempatanPklModel();
        $penempatan = $model->find($id);

        if ($penempatan === null || $penempatan['status'] !== 'aktif') {
            return redirect()->to('/admin/penempatan')->with('error', 'Penempatan tidak ditemukan atau sudah tidak aktif.');
        }

        $db = db_connect();
        $db->transStart();
        $model->update($id, ['status' => 'selesai']);
        (new SiswaModel())->update($penempatan['siswa_id'], ['status_pkl' => 'selesai']);
        $db->transComplete();

        return redirect()->to('/admin/penempatan')->with('success', 'Penempatan PKL ditandai selesai.');
    }

    public function batalkan(int $id)
    {
        $model      = new PenempatanPklModel();
        $penempatan = $model->find($id);

        if ($penempatan === null || $penempatan['status'] !== 'aktif') {
            return redirect()->to('/admin/penempatan')->with('error', 'Penempatan tidak ditemukan atau sudah tidak aktif.');
        }

        $db = db_connect();
        $db->transStart();
        $model->update($id, ['status' => 'dibatalkan']);
        (new SiswaModel())->update($penempatan['siswa_id'], ['status_pkl' => 'belum_ditempatkan']);
        $db->transComplete();

        return redirect()->to('/admin/penempatan')->with('success', 'Penempatan PKL dibatalkan. Siswa dapat ditempatkan ulang.');
    }
}
