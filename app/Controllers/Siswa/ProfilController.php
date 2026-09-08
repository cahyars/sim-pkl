<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProfilController extends BaseController
{
    public function index()
    {
        $siswa = (new SiswaModel())->getDetail((int) $this->session->get('profile_id'));

        return view('siswa/profil/index', [
            'title' => 'Profil Saya',
            'siswa' => $siswa,
        ]);
    }

    public function update()
    {
        $siswaId = (int) $this->session->get('profile_id');
        $siswa   = (new SiswaModel())->find($siswaId);

        if ($siswa === null) {
            return redirect()->to('/siswa/profil')->with('error', 'Data siswa tidak ditemukan.');
        }

        $rules = [
            'no_hp'  => 'permit_empty|max_length[20]',
            'alamat' => 'permit_empty',
            'email'  => 'permit_empty|valid_email|is_unique[users.email,id,' . $siswa['user_id'] . ']',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/siswa/profil')->withInput()->with('errors', $this->validator->getErrors());
        }

        $foto = $this->request->getFile('foto');
        $namaFotoBaru = null;

        if ($foto !== null && $foto->isValid() && ! $foto->hasMoved()) {
            $ekstensiDiizinkan = ['jpg', 'jpeg', 'png'];
            $ekstensi = strtolower($foto->getClientExtension());

            if (! in_array($ekstensi, $ekstensiDiizinkan, true)) {
                return redirect()->to('/siswa/profil')->withInput()->with('error', 'Foto profil harus berformat JPG atau PNG.');
            }
            if ($foto->getSize() > 1024 * 1024) {
                return redirect()->to('/siswa/profil')->withInput()->with('error', 'Ukuran foto profil maksimal 1 MB.');
            }

            $namaFotoBaru = $foto->getRandomName();
        }

        $db = db_connect();
        $db->transStart();

        $userModel  = new UserModel();
        $userUpdate = [];

        if ($this->request->getPost('email')) {
            $userUpdate['email'] = $this->request->getPost('email');
        }

        if ($namaFotoBaru !== null) {
            $fotoLama = $userModel->find($siswa['user_id'])['foto'] ?? null;

            $foto->move(WRITEPATH . 'uploads/foto_profil', $namaFotoBaru);
            $userUpdate['foto'] = $namaFotoBaru;

            if ($fotoLama && is_file(WRITEPATH . 'uploads/foto_profil/' . $fotoLama)) {
                unlink(WRITEPATH . 'uploads/foto_profil/' . $fotoLama);
            }
        }

        if ($userUpdate !== []) {
            $userModel->update($siswa['user_id'], $userUpdate);
        }

        (new SiswaModel())->update($siswaId, [
            'no_hp'  => $this->request->getPost('no_hp'),
            'alamat' => $this->request->getPost('alamat'),
        ]);

        $db->transComplete();

        return redirect()->to('/siswa/profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $userId = (int) $this->session->get('user_id');
        $user   = (new UserModel())->find($userId);

        $rules = [
            'password_lama'          => 'required',
            'password_baru'          => 'required|min_length[6]',
            'password_baru_konfirmasi' => 'required|matches[password_baru]',
        ];

        $messages = [
            'password_baru_konfirmasi' => [
                'matches' => 'Konfirmasi password baru tidak cocok.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/siswa/profil')->with('errors', $this->validator->getErrors());
        }

        if (! password_verify((string) $this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->to('/siswa/profil')->with('error', 'Password lama yang kamu masukkan salah.');
        }

        (new UserModel())->update($userId, ['password' => $this->request->getPost('password_baru')]);

        return redirect()->to('/siswa/profil')->with('success', 'Password berhasil diganti.');
    }

    public function foto(string $namaFile)
    {
        $path = WRITEPATH . 'uploads/foto_profil/' . basename($namaFile);

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->setContentType(mime_content_type($path))->setBody(file_get_contents($path));
    }
}
