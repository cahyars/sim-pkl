<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AspekPenilaianModel;
use App\Models\DetailPenilaianModel;

class AspekPenilaianController extends BaseController
{
    public function index()
    {
        $model         = new AspekPenilaianModel();
        $jurusanFilter = trim((string) $this->request->getGet('jurusan'));

        $query = $model->orderBy('jurusan', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC');

        if ($jurusanFilter !== '' && $jurusanFilter !== 'semua') {
            $query->where('jurusan', $jurusanFilter);
        }

        $daftar        = $query->findAll();
        $daftarJurusan = $model->daftarJurusanTersedia();
        $totalBobot    = $model->totalBobotAktif($jurusanFilter !== '' && $jurusanFilter !== 'semua' ? $jurusanFilter : null);

        return view('admin/aspek_penilaian/index', [
            'title'         => 'Aspek Penilaian',
            'daftar'        => $daftar,
            'daftarJurusan' => $daftarJurusan,
            'jurusanFilter' => $jurusanFilter ?: 'semua',
            'totalBobot'    => $totalBobot,
        ]);
    }

    public function store()
    {
        $rules = [
            'nama_aspek' => 'required|max_length[255]',
            'bobot'      => 'required|is_natural|greater_than[0]|less_than_equal_to[100]',
            'jurusan'    => 'permit_empty|max_length[100]',
            'urutan'     => 'permit_empty|is_natural',
            'deskripsi'  => 'permit_empty',
        ];

        $messages = [
            'bobot' => [
                'greater_than'       => 'Bobot harus lebih besar dari 0.',
                'less_than_equal_to' => 'Bobot maksimal 100.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/admin/aspek-penilaian')->withInput()->with('errors', $this->validator->getErrors());
        }

        $jurusan = trim((string) $this->request->getPost('jurusan')) ?: 'Umum';
        $urutan  = (int) $this->request->getPost('urutan') ?: 1;

        (new AspekPenilaianModel())->insert([
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'jurusan'    => $jurusan,
            'urutan'     => $urutan,
            'bobot'      => (int) $this->request->getPost('bobot'),
            'is_active'  => 1,
        ]);

        return redirect()->to('/admin/aspek-penilaian?jurusan=' . urlencode($jurusan))
            ->with('success', "Aspek penilaian untuk jurusan {$jurusan} berhasil ditambahkan.");
    }

    public function update(int $id)
    {
        $model = new AspekPenilaianModel();
        $aspek = $model->find($id);

        if ($aspek === null) {
            return redirect()->to('/admin/aspek-penilaian')->with('error', 'Aspek penilaian tidak ditemukan.');
        }

        $rules = [
            'nama_aspek' => 'required|max_length[255]',
            'bobot'      => 'required|is_natural|greater_than[0]|less_than_equal_to[100]',
            'jurusan'    => 'permit_empty|max_length[100]',
            'urutan'     => 'permit_empty|is_natural',
            'deskripsi'  => 'permit_empty',
        ];

        $messages = [
            'bobot' => [
                'greater_than'       => 'Bobot harus lebih besar dari 0.',
                'less_than_equal_to' => 'Bobot maksimal 100.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/admin/aspek-penilaian')->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->dipakaiPenilaian($id)) {
            return redirect()->to('/admin/aspek-penilaian')
                ->with('error', 'Aspek ini sudah dipakai pada penilaian yang tersimpan. Bobot/nama tidak dapat diubah agar nilai yang sudah ada tetap konsisten. Nonaktifkan aspek ini lalu buat aspek baru jika ingin mengubah komposisi penilaian.');
        }

        $jurusan = trim((string) $this->request->getPost('jurusan')) ?: 'Umum';
        $urutan  = (int) $this->request->getPost('urutan') ?: 1;

        $model->update($id, [
            'nama_aspek' => $this->request->getPost('nama_aspek'),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'jurusan'    => $jurusan,
            'urutan'     => $urutan,
            'bobot'      => (int) $this->request->getPost('bobot'),
        ]);

        return redirect()->to('/admin/aspek-penilaian?jurusan=' . urlencode($jurusan))
            ->with('success', 'Aspek penilaian berhasil diperbarui.');
    }

    public function toggleAktif(int $id)
    {
        $model = new AspekPenilaianModel();
        $aspek = $model->find($id);

        if ($aspek === null) {
            return redirect()->to('/admin/aspek-penilaian')->with('error', 'Aspek penilaian tidak ditemukan.');
        }

        $akanDinonaktifkan = (int) $aspek['is_active'] === 1;

        // Jangan sampai semua aspek nonaktif
        if ($akanDinonaktifkan && $model->where('is_active', 1)->countAllResults() <= 1) {
            return redirect()->to('/admin/aspek-penilaian')
                ->with('error', 'Minimal harus ada satu aspek penilaian yang aktif.');
        }

        $model->update($id, ['is_active' => $akanDinonaktifkan ? 0 : 1]);

        return redirect()->to('/admin/aspek-penilaian?jurusan=' . urlencode($aspek['jurusan'] ?? 'semua'))
            ->with('success', 'Aspek penilaian berhasil ' . ($akanDinonaktifkan ? 'dinonaktifkan' : 'diaktifkan') . '.');
    }

    public function delete(int $id)
    {
        $model = new AspekPenilaianModel();
        $aspek = $model->find($id);

        if ($aspek === null) {
            return redirect()->to('/admin/aspek-penilaian')->with('error', 'Aspek penilaian tidak ditemukan.');
        }

        if ($this->dipakaiPenilaian($id)) {
            return redirect()->to('/admin/aspek-penilaian')
                ->with('error', 'Tidak dapat menghapus, aspek ini sudah dipakai pada penilaian siswa. Gunakan tombol Nonaktifkan agar tidak lagi muncul di form penilaian baru.');
        }

        if ($model->where('is_active', 1)->countAllResults() <= 1 && (int) $aspek['is_active'] === 1) {
            return redirect()->to('/admin/aspek-penilaian')
                ->with('error', 'Minimal harus ada satu aspek penilaian yang aktif.');
        }

        $model->delete($id);

        return redirect()->to('/admin/aspek-penilaian?jurusan=' . urlencode($aspek['jurusan'] ?? 'semua'))
            ->with('success', 'Aspek penilaian berhasil dihapus.');
    }

    private function dipakaiPenilaian(int $aspekId): bool
    {
        return (new DetailPenilaianModel())->where('aspek_penilaian_id', $aspekId)->countAllResults() > 0;
    }
}

