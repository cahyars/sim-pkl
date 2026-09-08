<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenempatanPklModel;
use App\Models\SiswaModel;
use App\Models\TahunAjaranModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SiswaController extends BaseController
{
    public function index()
    {
        $siswaModel = new SiswaModel();
        $query      = $siswaModel->withUser();

        $keyword = trim((string) $this->request->getGet('q'));
        $kelas   = $this->request->getGet('kelas');
        $status  = $this->request->getGet('status');

        if ($keyword !== '') {
            $query->groupStart()
                ->like('users.nama', $keyword)
                ->orLike('siswa.nis', $keyword)
                ->orLike('siswa.nisn', $keyword)
                ->groupEnd();
        }

        if (! empty($kelas)) {
            $query->where('siswa.kelas', $kelas);
        }

        if (! empty($status)) {
            $query->where('siswa.status_pkl', $status);
        }

        $siswa = $query->orderBy('users.nama', 'ASC')->paginate(15, 'siswa');

        return view('admin/siswa/index', [
            'title'   => 'Data Siswa',
            'siswa'   => $siswa,
            'pager'   => $siswaModel->pager,
            'keyword' => $keyword,
            'kelas'   => $kelas,
            'status'  => $status,
            'daftarKelas' => $siswaModel->daftarKelas(),
        ]);
    }

    public function create()
    {
        $siswaModel = new SiswaModel();

        return view('admin/siswa/create', [
            'title'          => 'Tambah Siswa',
            'daftarJurusan'  => $siswaModel->daftarJurusan(),
            'daftarKelas'    => $siswaModel->daftarKelas(),
            'tahunAjaran'    => (new TahunAjaranModel())->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'nama'            => 'required|min_length[3]|max_length[100]',
            'nis'             => 'required|max_length[20]|is_unique[siswa.nis]|is_unique[users.username]',
            'nisn'            => 'permit_empty|max_length[20]',
            'kelas'           => 'required|max_length[20]',
            'jurusan'         => 'required|max_length[50]',
            'no_hp'           => 'permit_empty|max_length[20]',
            'alamat'          => 'permit_empty',
            'tahun_ajaran_id' => 'permit_empty|is_natural',
            'email'           => 'permit_empty|valid_email|is_unique[users.email]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nis   = trim($this->request->getPost('nis'));
        $email = trim((string) $this->request->getPost('email')) ?: $nis . '@siswa.smkn1subang.sch.id';

        $db = db_connect();
        $db->transStart();

        $userId = (new UserModel())->insert([
            'nama'      => $this->request->getPost('nama'),
            'email'     => $email,
            'username'  => $nis,
            'password'  => $nis,
            'role'      => 'siswa',
            'is_active' => 1,
        ], true);

        (new SiswaModel())->insert([
            'user_id'         => $userId,
            'nis'             => $nis,
            'nisn'            => $this->request->getPost('nisn'),
            'kelas'           => $this->request->getPost('kelas'),
            'jurusan'         => $this->request->getPost('jurusan'),
            'no_hp'           => $this->request->getPost('no_hp'),
            'alamat'          => $this->request->getPost('alamat'),
            'tahun_ajaran_id' => $this->request->getPost('tahun_ajaran_id') ?: null,
            'status_pkl'      => 'belum_ditempatkan',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data siswa. Silakan coba lagi.');
        }

        return redirect()->to('/admin/siswa')
            ->with('success', "Siswa {$this->request->getPost('nama')} berhasil ditambahkan. Username: {$nis}, password awal: {$nis}.");
    }

    public function edit(int $id)
    {
        $siswa = (new SiswaModel())->getDetail($id);

        if ($siswa === null) {
            return redirect()->to('/admin/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('admin/siswa/edit', [
            'title'         => 'Edit Siswa',
            'siswa'         => $siswa,
            'daftarJurusan' => (new SiswaModel())->daftarJurusan(),
            'tahunAjaran'   => (new TahunAjaranModel())->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $siswaModel = new SiswaModel();
        $siswa      = $siswaModel->find($id);

        if ($siswa === null) {
            return redirect()->to('/admin/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        $rules = [
            'nama'    => 'required|min_length[3]|max_length[100]',
            'nisn'    => 'permit_empty|max_length[20]',
            'kelas'   => 'required|max_length[20]',
            'jurusan' => 'required|max_length[50]',
            'no_hp'   => 'permit_empty|max_length[20]',
            'email'   => 'permit_empty|valid_email|is_unique[users.email,id,' . $siswa['user_id'] . ']',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $db->transStart();

        $userUpdate = ['nama' => $this->request->getPost('nama')];
        if ($this->request->getPost('email')) {
            $userUpdate['email'] = $this->request->getPost('email');
        }
        (new UserModel())->update($siswa['user_id'], $userUpdate);

        $siswaModel->update($id, [
            'nisn'            => $this->request->getPost('nisn'),
            'kelas'           => $this->request->getPost('kelas'),
            'jurusan'         => $this->request->getPost('jurusan'),
            'no_hp'           => $this->request->getPost('no_hp'),
            'alamat'          => $this->request->getPost('alamat'),
            'tahun_ajaran_id' => $this->request->getPost('tahun_ajaran_id') ?: null,
        ]);

        $db->transComplete();

        return redirect()->to('/admin/siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $siswaModel = new SiswaModel();
        $siswa      = $siswaModel->find($id);

        if ($siswa === null) {
            return redirect()->to('/admin/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        if ((new PenempatanPklModel())->punyaPenempatanAktif($id)) {
            return redirect()->to('/admin/siswa')
                ->with('error', 'Siswa masih memiliki penempatan PKL aktif. Batalkan/selesaikan penempatannya terlebih dahulu.');
        }

        $db = db_connect();
        $db->transStart();
        $siswaModel->delete($id);
        (new UserModel())->delete($siswa['user_id']);
        $db->transComplete();

        return redirect()->to('/admin/siswa')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function toggleAktif(int $id)
    {
        $siswa = (new SiswaModel())->find($id);

        if ($siswa === null) {
            return redirect()->to('/admin/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($siswa['user_id']);
        $userModel->update($siswa['user_id'], ['is_active' => ! $user['is_active']]);

        $status = $user['is_active'] ? 'dinonaktifkan' : 'diaktifkan';

        return redirect()->to('/admin/siswa')->with('success', "Akun siswa berhasil {$status}.");
    }

    public function resetPassword(int $id)
    {
        $siswa = (new SiswaModel())->find($id);

        if ($siswa === null) {
            return redirect()->to('/admin/siswa')->with('error', 'Data siswa tidak ditemukan.');
        }

        (new UserModel())->update($siswa['user_id'], ['password' => $siswa['nis']]);

        return redirect()->to('/admin/siswa')->with('success', "Password berhasil direset ke default (NIS: {$siswa['nis']}).");
    }

    public function importForm()
    {
        return view('admin/siswa/import', ['title' => 'Import Data Siswa']);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');

        $header = ['NIS', 'Nama', 'NISN', 'Kelas', 'Jurusan', 'No HP', 'Alamat'];
        $sheet->fromArray($header, null, 'A1');
        $sheet->fromArray(
            ['2324099', 'Contoh Nama Siswa', '0071299999', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '081234567890', 'Alamat contoh'],
            null,
            'A2'
        );

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="template_import_siswa.xlsx"');

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $this->response->setBody($content);
    }

    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if ($file === null || ! $file->isValid()) {
            return redirect()->to('/admin/siswa/import')->with('error', 'File tidak valid atau gagal diunggah.');
        }

        if (! in_array($file->getClientExtension(), ['xlsx', 'xls'], true)) {
            return redirect()->to('/admin/siswa/import')->with('error', 'Format file harus .xlsx atau .xls.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
        } catch (\Throwable $e) {
            return redirect()->to('/admin/siswa/import')->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        array_shift($rows); // buang baris header

        $tahunAjaranAktif = (new TahunAjaranModel())->getAktif();
        $siswaModel       = new SiswaModel();
        $userModel        = new UserModel();

        $berhasil = 0;
        $gagal    = [];

        foreach ($rows as $i => $row) {
            $baris = $i + 2; // nomor baris asli di excel (header = baris 1)

            [$nis, $nama, $nisn, $kelas, $jurusan, $noHp, $alamat] = array_pad($row, 7, null);
            $nis = trim((string) $nis);
            $nama = trim((string) $nama);

            if ($nis === '' && $nama === '') {
                continue; // baris kosong, lewati tanpa dihitung gagal
            }

            if ($nis === '' || $nama === '' || empty($kelas) || empty($jurusan)) {
                $gagal[] = "Baris {$baris}: NIS/Nama/Kelas/Jurusan wajib diisi.";
                continue;
            }

            if ($siswaModel->where('nis', $nis)->first() || $userModel->where('username', $nis)->first()) {
                $gagal[] = "Baris {$baris}: NIS {$nis} sudah terdaftar.";
                continue;
            }

            $db = db_connect();
            $db->transStart();

            $userId = $userModel->insert([
                'nama'      => $nama,
                'email'     => $nis . '@siswa.smkn1subang.sch.id',
                'username'  => $nis,
                'password'  => $nis,
                'role'      => 'siswa',
                'is_active' => 1,
            ], true);

            $siswaModel->insert([
                'user_id'         => $userId,
                'nis'             => $nis,
                'nisn'            => $nisn !== null ? trim((string) $nisn) : null,
                'kelas'           => trim((string) $kelas),
                'jurusan'         => trim((string) $jurusan),
                'no_hp'           => $noHp !== null ? trim((string) $noHp) : null,
                'alamat'          => $alamat !== null ? trim((string) $alamat) : null,
                'tahun_ajaran_id' => $tahunAjaranAktif['id'] ?? null,
                'status_pkl'      => 'belum_ditempatkan',
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                $gagal[] = "Baris {$baris}: gagal menyimpan ke database.";
                continue;
            }

            $berhasil++;
        }

        $pesan = "Import selesai: {$berhasil} siswa berhasil ditambahkan.";
        if ($gagal !== []) {
            $pesan .= ' ' . count($gagal) . ' baris gagal.';

            return redirect()->to('/admin/siswa/import')->with('success', $pesan)->with('errors', $gagal);
        }

        return redirect()->to('/admin/siswa')->with('success', $pesan);
    }
}
