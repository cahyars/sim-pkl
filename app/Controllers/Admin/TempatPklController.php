<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembimbingLapanganModel;
use App\Models\PenempatanPklModel;
use App\Models\TempatPklModel;

class TempatPklController extends BaseController
{
    public function index()
    {
        $model   = new TempatPklModel();
        $keyword = trim((string) $this->request->getGet('q'));
        $status  = $this->request->getGet('status') ?: '';

        // Pakai builder berkuota agar jumlah siswa terisi ikut dihitung dalam
        // satu query (tanpa memanggil terisi() per baris).
        $query = $model->withKuotaTerpakaiBuilder();

        if ($keyword !== '') {
            $query->groupStart()
                ->like('tempat_pkl.nama_perusahaan', $keyword)
                ->orLike('tempat_pkl.bidang_usaha', $keyword)
                ->orLike('tempat_pkl.alamat', $keyword)
                ->orLike('tempat_pkl.penanggung_jawab', $keyword)
                ->groupEnd();
        }

        if ($status !== '') {
            $query->where('tempat_pkl.is_active', $status === 'aktif' ? 1 : 0);
        }

        return view('admin/tempat_pkl/index', [
            'title'   => 'Tempat PKL',
            'tempat'  => $query->paginate(12, 'tempat'),
            'pager'   => $model->pager,
            'keyword' => $keyword,
            'status'  => $status,
        ]);
    }

    public function create()
    {
        return view('admin/tempat_pkl/create', ['title' => 'Tambah Tempat PKL']);
    }

    public function store()
    {
        $model = new TempatPklModel();

        if (! $this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->insert([
            'nama_perusahaan'  => $this->request->getPost('nama_perusahaan'),
            'alamat'           => $this->request->getPost('alamat'),
            'no_telp'          => $this->request->getPost('no_telp'),
            'email'            => $this->request->getPost('email'),
            'bidang_usaha'     => $this->request->getPost('bidang_usaha'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'kuota'            => $this->request->getPost('kuota') ?: 0,
            'is_active'        => 1,
        ]);

        return redirect()->to('/admin/tempat-pkl')->with('success', 'Tempat PKL berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $model  = new TempatPklModel();
        $tempat = $model->find($id);

        if ($tempat === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Tempat PKL tidak ditemukan.');
        }

        $tempat['terisi'] = $model->terisi($id);

        $keyword         = trim((string) $this->request->getGet('q'));
        $pembimbingModel = new PembimbingLapanganModel();
        $query           = $pembimbingModel->byTempatBuilder($id);

        if ($keyword !== '') {
            $query->groupStart()
                ->like('pembimbing_lapangan.nama', $keyword)
                ->orLike('pembimbing_lapangan.jabatan', $keyword)
                ->orLike('pembimbing_lapangan.email', $keyword)
                ->groupEnd();
        }

        return view('admin/tempat_pkl/show', [
            'title'      => $tempat['nama_perusahaan'],
            'tempat'     => $tempat,
            'pembimbing' => $query->paginate(10, 'pembimbing'),
            'pager'      => $pembimbingModel->pager,
            'keyword'    => $keyword,
        ]);
    }

    public function edit(int $id)
    {
        $tempat = (new TempatPklModel())->find($id);

        if ($tempat === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Tempat PKL tidak ditemukan.');
        }

        return view('admin/tempat_pkl/edit', ['title' => 'Edit Tempat PKL', 'tempat' => $tempat]);
    }

    public function update(int $id)
    {
        $model  = new TempatPklModel();
        $tempat = $model->find($id);

        if ($tempat === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Tempat PKL tidak ditemukan.');
        }

        if (! $this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'nama_perusahaan'  => $this->request->getPost('nama_perusahaan'),
            'alamat'           => $this->request->getPost('alamat'),
            'no_telp'          => $this->request->getPost('no_telp'),
            'email'            => $this->request->getPost('email'),
            'bidang_usaha'     => $this->request->getPost('bidang_usaha'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'kuota'            => $this->request->getPost('kuota') ?: 0,
        ]);

        return redirect()->to('/admin/tempat-pkl')->with('success', 'Data tempat PKL berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $model  = new TempatPklModel();
        $tempat = $model->find($id);

        if ($tempat === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Tempat PKL tidak ditemukan.');
        }

        if ($model->terisi($id) > 0) {
            return redirect()->to('/admin/tempat-pkl')
                ->with('error', "Tidak dapat menghapus {$tempat['nama_perusahaan']} karena masih ada siswa PKL aktif di tempat ini.");
        }

        if ((new PembimbingLapanganModel())->where('tempat_pkl_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/tempat-pkl')
                ->with('error', "Tidak dapat menghapus {$tempat['nama_perusahaan']} karena masih memiliki data pembimbing lapangan. Hapus pembimbing lapangan terkait terlebih dahulu.");
        }

        $model->delete($id);

        return redirect()->to('/admin/tempat-pkl')->with('success', 'Tempat PKL berhasil dihapus.');
    }
}
