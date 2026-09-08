<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\GuruPembimbingModel;
use App\Models\PembimbingLapanganModel;
use App\Models\SiswaModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    private const DASHBOARD_BY_ROLE = [
        'siswa'                => '/siswa/dashboard',
        'guru_pembimbing'      => '/guru/dashboard',
        'pembimbing_lapangan'  => '/pembimbing-lapangan/dashboard',
        'admin'                => '/admin/dashboard',
    ];

    public function login()
    {
        $tahunAjaranModel  = new \App\Models\TahunAjaranModel();
        $daftarTahunAjaran = $tahunAjaranModel->orderBy('is_active', 'DESC')
            ->orderBy('nama_tahun_ajaran', 'DESC')
            ->orderBy('semester', 'DESC')
            ->findAll();

        return view('auth/login', [
            'daftarTahunAjaran' => $daftarTahunAjaran,
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/login')->withInput()->with('error', 'Silakan masukkan username dan password Anda.');
        }

        $username = trim($this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username) ?? $userModel->findByEmail($username);

        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()->to('/login')->withInput()->with('error', 'Username / email atau password salah. Silakan coba lagi.');
        }

        if (! (bool) $user['is_active']) {
            return redirect()->to('/login')->withInput()->with('error', 'Akun Anda nonaktif. Silakan hubungi Admin/Tata Usaha PKL.');
        }

        $this->buatSesi($user);

        $redirectUrl = session()->get('redirect_url');
        session()->remove('redirect_url');

        $pesanSukses = 'Login berhasil. Selamat datang, ' . $user['nama'] . '.';
        if ($user['role'] === 'admin' && session()->get('is_arsip')) {
            $pesanSukses .= ' (Mode Arsip: ' . session()->get('nama_tahun_ajaran') . ' ' . ucfirst(session()->get('semester_tahun_ajaran')) . ')';
        }

        if ($redirectUrl) {
            return redirect()->to($redirectUrl)->with('success', $pesanSukses);
        }

        return redirect()->to(self::DASHBOARD_BY_ROLE[$user['role']])
            ->with('success', $pesanSukses);
    }

    public function logout()
    {
        // Bersihkan data auth lalu regenerasi ID sesi (bukan destroy()) —
        // destroy() menghapus storage sesi sebelum flashdata sempat ditulis,
        // sehingga pesan "Anda telah logout" tidak pernah sampai ke halaman login.
        session()->remove(['isLoggedIn', 'user_id', 'nama', 'username', 'email', 'role', 'foto', 'profile_id', 'tahun_ajaran_id', 'nama_tahun_ajaran', 'semester_tahun_ajaran', 'is_arsip']);
        session()->regenerate(true);

        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Beralih tahun ajaran aktif/arsip langsung dari topbar Admin.
     */
    public function switchTahunAjaran()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $tahunId          = (int) $this->request->getPost('tahun_ajaran_id');
        $tahunAjaranModel = new \App\Models\TahunAjaranModel();
        $tahun            = $tahunAjaranModel->find($tahunId);

        if ($tahun !== null) {
            session()->set([
                'tahun_ajaran_id'       => (int) $tahun['id'],
                'nama_tahun_ajaran'     => $tahun['nama_tahun_ajaran'],
                'semester_tahun_ajaran' => $tahun['semester'],
                'is_arsip'              => (int) $tahun['is_active'] !== 1,
            ]);

            $mode = $tahun['is_active'] ? 'Periode Aktif' : 'Mode Arsip';
            return redirect()->back()->with('success', "Konteks data dialihkan ke {$tahun['nama_tahun_ajaran']} (" . ucfirst($tahun['semester']) . ") &ndash; {$mode}.");
        }

        return redirect()->back();
    }

    /**
     * Simpan data pengguna ke session, termasuk id profil turunannya
     * (siswa_id/guru_pembimbing_id/pembimbing_lapangan_id) agar tidak perlu
     * query ulang di setiap controller yang butuh identitas pemilik data.
     */
    private function buatSesi(array $user): void
    {
        $data = [
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'nama'       => $user['nama'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'foto'       => $user['foto'],
            'profile_id' => null,
        ];

        switch ($user['role']) {
            case 'siswa':
                $siswa = (new SiswaModel())->findByUserId((int) $user['id']);
                $data['profile_id'] = $siswa['id'] ?? null;
                break;

            case 'guru_pembimbing':
                $guru = (new GuruPembimbingModel())->findByUserId((int) $user['id']);
                $data['profile_id'] = $guru['id'] ?? null;
                break;

            case 'pembimbing_lapangan':
                $pembimbing = (new PembimbingLapanganModel())->findByUserId((int) $user['id']);
                $data['profile_id'] = $pembimbing['id'] ?? null;
                break;
        }

        // Tentukan tahun ajaran: dari pilihan form login, atau default tahun ajaran aktif
        $tahunAjaranId    = (int) $this->request->getPost('tahun_ajaran_id');
        $tahunAjaranModel = new \App\Models\TahunAjaranModel();
        $tahun            = null;

        if ($tahunAjaranId > 0) {
            $tahun = $tahunAjaranModel->find($tahunAjaranId);
        }

        if ($tahun === null) {
            $tahun = $tahunAjaranModel->getAktif() ?? $tahunAjaranModel->orderBy('id', 'DESC')->first();
        }

        if ($tahun !== null) {
            $data['tahun_ajaran_id']       = (int) $tahun['id'];
            $data['nama_tahun_ajaran']     = $tahun['nama_tahun_ajaran'];
            $data['semester_tahun_ajaran'] = $tahun['semester'];
            $data['is_arsip']              = (int) $tahun['is_active'] !== 1;
        }

        session()->regenerate();
        session()->set($data);
    }
}
