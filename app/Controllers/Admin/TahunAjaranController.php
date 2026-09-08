<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;

class TahunAjaranController extends BaseController
{
    public function index()
    {
        $keyword  = trim((string) $this->request->getGet('q'));
        $semester = $this->request->getGet('semester') ?: '';

        $model = new TahunAjaranModel();

        if ($keyword !== '') {
            $model->like('nama_tahun_ajaran', $keyword);
        }

        if ($semester !== '') {
            $model->where('semester', $semester);
        }

        $daftar = $model->orderBy('nama_tahun_ajaran', 'DESC')
            ->orderBy('semester', 'DESC')
            ->paginate(15, 'tahun');

        return view('admin/tahun_ajaran/index', [
            'title'    => 'Tahun Ajaran',
            'daftar'   => $daftar,
            'pager'    => $model->pager,
            'keyword'  => $keyword,
            'semester' => $semester,
        ]);
    }

    public function store()
    {
        $model = new TahunAjaranModel();

        $rules = [
            'nama_tahun_ajaran' => 'required|max_length[20]',
            'semester'          => 'required|in_list[ganjil,genap]',
            'tanggal_mulai'     => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai'   => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/tahun-ajaran')->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $model->insert([
            'nama_tahun_ajaran' => $this->request->getPost('nama_tahun_ajaran'),
            'semester'          => $this->request->getPost('semester'),
            'tanggal_mulai'     => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'   => $this->request->getPost('tanggal_selesai') ?: null,
            'is_active'         => 0,
        ], true);

        if ($this->request->getPost('jadikan_aktif')) {
            $model->setAktif((int) $id);
        }

        return redirect()->to('/admin/tahun-ajaran')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $model = new TahunAjaranModel();
        $data  = $model->find($id);

        if ($data === null) {
            return redirect()->to('/admin/tahun-ajaran')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'nama_tahun_ajaran' => 'required|max_length[20]',
            'semester'          => 'required|in_list[ganjil,genap]',
            'tanggal_mulai'     => 'permit_empty|valid_date[Y-m-d]',
            'tanggal_selesai'   => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/tahun-ajaran')->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'nama_tahun_ajaran' => $this->request->getPost('nama_tahun_ajaran'),
            'semester'          => $this->request->getPost('semester'),
            'tanggal_mulai'     => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai'   => $this->request->getPost('tanggal_selesai') ?: null,
        ]);

        return redirect()->to('/admin/tahun-ajaran')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function jadikanAktif(int $id)
    {
        $model = new TahunAjaranModel();

        if ($model->find($id) === null) {
            return redirect()->to('/admin/tahun-ajaran')->with('error', 'Data tidak ditemukan.');
        }

        $model->setAktif($id);

        return redirect()->to('/admin/tahun-ajaran')->with('success', 'Tahun ajaran aktif berhasil diubah.');
    }
}
