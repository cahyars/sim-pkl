<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AspekPenilaianModel;
use App\Models\JurusanModel;
use App\Models\SiswaModel;

class JurusanController extends BaseController
{
    protected JurusanModel $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    public function index()
    {
        $cari    = trim((string) $this->request->getGet('q'));
        $jurusan = $this->jurusanModel->withStatistik($cari ?: null);

        $totalJurusan = count($jurusan);
        $totalAktif   = 0;
        $totalSiswa   = 0;
        $jurusanSiapTp = 0;

        foreach ($jurusan as $j) {
            if ($j['is_active']) {
                $totalAktif++;
            }
            $totalSiswa += $j['total_siswa'];
            if ($j['total_aspek'] > 0) {
                $jurusanSiapTp++;
            }
        }

        return view('admin/jurusan/index', [
            'title'         => 'Kelola Jurusan & Konsentrasi Keahlian',
            'jurusan'       => $jurusan,
            'cari'          => $cari,
            'totalJurusan'  => $totalJurusan,
            'totalAktif'    => $totalAktif,
            'totalSiswa'    => $totalSiswa,
            'jurusanSiapTp' => $jurusanSiapTp,
        ]);
    }

    public function store()
    {
        $data = [
            'kode'                 => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'                 => trim((string) $this->request->getPost('nama')),
            'bidang_keahlian'      => trim((string) $this->request->getPost('bidang_keahlian')),
            'program_keahlian'     => trim((string) $this->request->getPost('program_keahlian')),
            'konsentrasi_keahlian' => trim((string) $this->request->getPost('konsentrasi_keahlian')),
            'kepala_program'       => trim((string) $this->request->getPost('kepala_program')),
            'nip_kepala_program'   => trim((string) $this->request->getPost('nip_kepala_program')),
            'deskripsi'            => trim((string) $this->request->getPost('deskripsi')),
            'is_active'            => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $this->jurusanModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->jurusanModel->errors());
        }

        return redirect()->to('/admin/jurusan')->with('success', 'Jurusan baru berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $jurusan = $this->jurusanModel->find($id);
        if ($jurusan === null) {
            return redirect()->to('/admin/jurusan')->with('error', 'Data jurusan tidak ditemukan.');
        }

        $namaLama = $jurusan['nama'];
        $namaBaru = trim((string) $this->request->getPost('nama'));

        $data = [
            'id'                   => $id,
            'kode'                 => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'                 => $namaBaru,
            'bidang_keahlian'      => trim((string) $this->request->getPost('bidang_keahlian')),
            'program_keahlian'     => trim((string) $this->request->getPost('program_keahlian')),
            'konsentrasi_keahlian' => trim((string) $this->request->getPost('konsentrasi_keahlian')),
            'kepala_program'       => trim((string) $this->request->getPost('kepala_program')),
            'nip_kepala_program'   => trim((string) $this->request->getPost('nip_kepala_program')),
            'deskripsi'            => trim((string) $this->request->getPost('deskripsi')),
            'is_active'            => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $this->jurusanModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->jurusanModel->errors());
        }

        // Jika nama jurusan berubah, sinkronkan nama di aspek_penilaian dan siswa
        if ($namaLama !== $namaBaru && $namaBaru !== '') {
            $db = \Config\Database::connect();
            $db->table('aspek_penilaian')->where('jurusan', $namaLama)->update(['jurusan' => $namaBaru]);
            $db->table('siswa')->where('jurusan', $namaLama)->update(['jurusan' => $namaBaru]);
        }

        return redirect()->to('/admin/jurusan')->with('success', 'Data jurusan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $jurusan = $this->jurusanModel->find($id);
        if ($jurusan === null) {
            return redirect()->to('/admin/jurusan')->with('error', 'Data jurusan tidak ditemukan.');
        }

        $siswaCount = (new SiswaModel())->where('jurusan', $jurusan['nama'])->countAllResults();
        if ($siswaCount > 0) {
            return redirect()->to('/admin/jurusan')->with('error', "Jurusan '{$jurusan['nama']}' tidak dapat dihapus karena masih digunakan oleh {$siswaCount} siswa. Anda dapat menonaktifkan statusnya.");
        }

        $aspekCount = (new AspekPenilaianModel())->where('jurusan', $jurusan['nama'])->countAllResults();
        if ($aspekCount > 0) {
            return redirect()->to('/admin/jurusan')->with('error', "Jurusan '{$jurusan['nama']}' memiliki {$aspekCount} Tujuan Pembelajaran (TP) aktif. Hapus atau pindahkan TP terlebih dahulu.");
        }

        $this->jurusanModel->delete($id);

        return redirect()->to('/admin/jurusan')->with('success', "Jurusan '{$jurusan['nama']}' berhasil dihapus.");
    }

    public function toggleAktif(int $id)
    {
        $jurusan = $this->jurusanModel->find($id);
        if ($jurusan === null) {
            return redirect()->to('/admin/jurusan')->with('error', 'Data jurusan tidak ditemukan.');
        }

        $statusBaru = $jurusan['is_active'] ? 0 : 1;
        $this->jurusanModel->update($id, ['is_active' => $statusBaru]);

        $pesan = $statusBaru ? "Jurusan '{$jurusan['nama']}' diaktifkan." : "Jurusan '{$jurusan['nama']}' dinonaktifkan.";

        return redirect()->to('/admin/jurusan')->with('success', $pesan);
    }
}
