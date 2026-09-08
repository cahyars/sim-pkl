<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembimbingLapanganModel;
use App\Models\PenempatanPklModel;
use App\Models\TempatPklModel;
use App\Models\UserModel;

class PembimbingLapanganController extends BaseController
{
    public function store(int $tempatPklId)
    {
        $tempat = (new TempatPklModel())->find($tempatPklId);

        if ($tempat === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Tempat PKL tidak ditemukan.');
        }

        $rules = [
            'nama'   => 'required|max_length[100]',
            'jabatan' => 'permit_empty|max_length[100]',
            'no_hp'  => 'permit_empty|max_length[20]',
            'email'  => 'permit_empty|valid_email',
        ];

        $buatAkun = (bool) $this->request->getPost('buat_akun');

        if ($buatAkun) {
            $rules['username'] = 'required|regex_match[/^[a-zA-Z0-9_.-]+$/]|min_length[4]|is_unique[users.username]';
            $rules['email']    = 'required|valid_email|is_unique[users.email]';
        }

        if (! $this->validate($rules)) {
            return redirect()->to("/admin/tempat-pkl/{$tempatPklId}")->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = db_connect();
        $db->transStart();

        $userId = null;

        if ($buatAkun) {
            $username = trim($this->request->getPost('username'));
            $userId   = (new UserModel())->insert([
                'nama'      => $this->request->getPost('nama'),
                'email'     => $this->request->getPost('email'),
                'username'  => $username,
                'password'  => $username,
                'role'      => 'pembimbing_lapangan',
                'is_active' => 1,
            ], true);
        }

        (new PembimbingLapanganModel())->insert([
            'user_id'       => $userId,
            'tempat_pkl_id' => $tempatPklId,
            'nama'          => $this->request->getPost('nama'),
            'jabatan'       => $this->request->getPost('jabatan'),
            'no_hp'         => $this->request->getPost('no_hp'),
            'email'         => $this->request->getPost('email'),
        ]);

        $db->transComplete();

        $pesan = 'Pembimbing lapangan berhasil ditambahkan.';
        if ($buatAkun) {
            $pesan .= " Username: {$this->request->getPost('username')}, password awal sama dengan username.";
        }

        return redirect()->to("/admin/tempat-pkl/{$tempatPklId}")->with('success', $pesan);
    }

    public function update(int $id)
    {
        $model      = new PembimbingLapanganModel();
        $pembimbing = $model->find($id);

        if ($pembimbing === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Data pembimbing lapangan tidak ditemukan.');
        }

        $rules = [
            'nama'    => 'required|max_length[100]',
            'jabatan' => 'permit_empty|max_length[100]',
            'no_hp'   => 'permit_empty|max_length[20]',
            'email'   => 'permit_empty|valid_email',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to("/admin/tempat-pkl/{$pembimbing['tempat_pkl_id']}")->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'nama'    => $this->request->getPost('nama'),
            'jabatan' => $this->request->getPost('jabatan'),
            'no_hp'   => $this->request->getPost('no_hp'),
            'email'   => $this->request->getPost('email'),
        ]);

        if ($pembimbing['user_id']) {
            (new UserModel())->update($pembimbing['user_id'], [
                'nama'  => $this->request->getPost('nama'),
                'email' => $this->request->getPost('email') ?: (new UserModel())->find($pembimbing['user_id'])['email'],
            ]);
        }

        return redirect()->to("/admin/tempat-pkl/{$pembimbing['tempat_pkl_id']}")->with('success', 'Data pembimbing lapangan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $model      = new PembimbingLapanganModel();
        $pembimbing = $model->find($id);

        if ($pembimbing === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Data pembimbing lapangan tidak ditemukan.');
        }

        $tempatPklId = $pembimbing['tempat_pkl_id'];

        if ((new PenempatanPklModel())->where('pembimbing_lapangan_id', $id)->where('status', 'aktif')->countAllResults() > 0) {
            return redirect()->to("/admin/tempat-pkl/{$tempatPklId}")
                ->with('error', 'Tidak dapat menghapus, pembimbing ini masih membimbing siswa PKL aktif.');
        }

        $db = db_connect();
        $db->transStart();
        $model->delete($id);
        if ($pembimbing['user_id']) {
            (new UserModel())->delete($pembimbing['user_id']);
        }
        $db->transComplete();

        return redirect()->to("/admin/tempat-pkl/{$tempatPklId}")->with('success', 'Pembimbing lapangan berhasil dihapus.');
    }

    public function toggleAktif(int $id)
    {
        $pembimbing = (new PembimbingLapanganModel())->find($id);

        if ($pembimbing === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Data pembimbing lapangan tidak ditemukan.');
        }

        if (empty($pembimbing['user_id'])) {
            return redirect()->to("/admin/tempat-pkl/{$pembimbing['tempat_pkl_id']}")
                ->with('error', 'Pembimbing ini belum memiliki akun login.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($pembimbing['user_id']);
        $userModel->update($pembimbing['user_id'], ['is_active' => ! $user['is_active']]);

        $status = $user['is_active'] ? 'dinonaktifkan' : 'diaktifkan';

        return redirect()->to("/admin/tempat-pkl/{$pembimbing['tempat_pkl_id']}")->with('success', "Akun pembimbing lapangan berhasil {$status}.");
    }

    public function resetPassword(int $id)
    {
        $pembimbing = (new PembimbingLapanganModel())->find($id);

        if ($pembimbing === null) {
            return redirect()->to('/admin/tempat-pkl')->with('error', 'Data pembimbing lapangan tidak ditemukan.');
        }

        if (empty($pembimbing['user_id'])) {
            return redirect()->to("/admin/tempat-pkl/{$pembimbing['tempat_pkl_id']}")
                ->with('error', 'Pembimbing ini belum memiliki akun login.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($pembimbing['user_id']);

        // Password default disamakan dengan username, konsisten dengan saat akun dibuat.
        $userModel->update($pembimbing['user_id'], ['password' => $user['username']]);

        return redirect()->to("/admin/tempat-pkl/{$pembimbing['tempat_pkl_id']}")
            ->with('success', "Password berhasil direset ke default (sama dengan username: {$user['username']}).");
    }
}
