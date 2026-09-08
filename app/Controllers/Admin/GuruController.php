<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruPembimbingModel;
use App\Models\PenempatanPklModel;
use App\Models\UserModel;

class GuruController extends BaseController
{
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $status   = $this->request->getGet('status') ?: '';

        $model = (new GuruPembimbingModel())->withUser();

        if ($keyword !== '') {
            $model->groupStart()
                ->like('users.nama', $keyword)
                ->orLike('guru_pembimbing.nip', $keyword)
                ->orLike('guru_pembimbing.jurusan_ampu', $keyword)
                ->orLike('users.email', $keyword)
                ->groupEnd();
        }

        if ($status !== '') {
            $model->where('users.is_active', $status === 'aktif' ? 1 : 0);
        }

        return view('admin/guru/index', [
            'title'   => 'Guru Pembimbing',
            'guru'    => $model->orderBy('users.nama', 'ASC')->paginate(15, 'guru'),
            'pager'   => $model->pager,
            'keyword' => $keyword,
            'status'  => $status,
        ]);
    }

    public function store()
    {
        $rules = [
            'nama'         => 'required|min_length[3]|max_length[100]',
            'username'     => 'required|regex_match[/^[a-zA-Z0-9_.-]+$/]|min_length[4]|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'nip'          => 'permit_empty|max_length[30]',
            'no_hp'        => 'permit_empty|max_length[20]',
            'jurusan_ampu' => 'permit_empty|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/guru')->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim($this->request->getPost('username'));

        $db = db_connect();
        $db->transStart();

        $userId = (new UserModel())->insert([
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'username'  => $username,
            'password'  => $username,
            'role'      => 'guru_pembimbing',
            'is_active' => 1,
        ], true);

        (new GuruPembimbingModel())->insert([
            'user_id'      => $userId,
            'nip'          => $this->request->getPost('nip'),
            'no_hp'        => $this->request->getPost('no_hp'),
            'jurusan_ampu' => $this->request->getPost('jurusan_ampu'),
        ]);

        $db->transComplete();

        return redirect()->to('/admin/guru')
            ->with('success', "Guru pembimbing berhasil ditambahkan. Username: {$username}, password awal sama dengan username.");
    }

    public function update(int $id)
    {
        $model = new GuruPembimbingModel();
        $guru  = $model->find($id);

        if ($guru === null) {
            return redirect()->to('/admin/guru')->with('error', 'Data guru pembimbing tidak ditemukan.');
        }

        $rules = [
            'nama'         => 'required|min_length[3]|max_length[100]',
            'email'        => 'required|valid_email|is_unique[users.email,id,' . $guru['user_id'] . ']',
            'nip'          => 'permit_empty|max_length[30]',
            'no_hp'        => 'permit_empty|max_length[20]',
            'jurusan_ampu' => 'permit_empty|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/guru')->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $db->transStart();

        (new UserModel())->update($guru['user_id'], [
            'nama'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
        ]);

        $model->update($id, [
            'nip'          => $this->request->getPost('nip'),
            'no_hp'        => $this->request->getPost('no_hp'),
            'jurusan_ampu' => $this->request->getPost('jurusan_ampu'),
        ]);

        $db->transComplete();

        return redirect()->to('/admin/guru')->with('success', 'Data guru pembimbing berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $model = new GuruPembimbingModel();
        $guru  = $model->find($id);

        if ($guru === null) {
            return redirect()->to('/admin/guru')->with('error', 'Data guru pembimbing tidak ditemukan.');
        }

        if ((new PenempatanPklModel())->where('guru_pembimbing_id', $id)->where('status', 'aktif')->countAllResults() > 0) {
            return redirect()->to('/admin/guru')
                ->with('error', 'Tidak dapat menghapus, guru ini masih membimbing siswa PKL aktif.');
        }

        $db = db_connect();
        $db->transStart();
        $model->delete($id);
        (new UserModel())->delete($guru['user_id']);
        $db->transComplete();

        return redirect()->to('/admin/guru')->with('success', 'Data guru pembimbing berhasil dihapus.');
    }

    public function toggleAktif(int $id)
    {
        $guru = (new GuruPembimbingModel())->find($id);

        if ($guru === null) {
            return redirect()->to('/admin/guru')->with('error', 'Data guru pembimbing tidak ditemukan.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($guru['user_id']);
        $userModel->update($guru['user_id'], ['is_active' => ! $user['is_active']]);

        $status = $user['is_active'] ? 'dinonaktifkan' : 'diaktifkan';

        return redirect()->to('/admin/guru')->with('success', "Akun guru berhasil {$status}.");
    }

    public function resetPassword(int $id)
    {
        $guru = (new GuruPembimbingModel())->find($id);

        if ($guru === null) {
            return redirect()->to('/admin/guru')->with('error', 'Data guru pembimbing tidak ditemukan.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($guru['user_id']);

        // Password default disamakan dengan username, konsisten dengan saat akun dibuat.
        $userModel->update($guru['user_id'], ['password' => $user['username']]);

        return redirect()->to('/admin/guru')
            ->with('success', "Password berhasil direset ke default (sama dengan username: {$user['username']}).");
    }
}
