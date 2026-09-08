<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Menjaga route agar hanya bisa diakses saat sudah login (RBAC §2 Tech Stack: Session-based
 * auth + RBAC via CI4 Filter). Argumen filter berisi daftar role yang diizinkan, contoh:
 * ['filter' => 'role:guru_pembimbing,admin'].
 */
class RoleFilter implements FilterInterface
{
    private const DASHBOARD_BY_ROLE = [
        'siswa'                => '/siswa/dashboard',
        'guru_pembimbing'      => '/guru/dashboard',
        'pembimbing_lapangan'  => '/pembimbing-lapangan/dashboard',
        'admin'                => '/admin/dashboard',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
            $session->set('redirect_url', current_url());

            return redirect()->to('/login');
        }

        $role = $session->get('role');

        if ($arguments !== null && $arguments !== [] && ! in_array($role, $arguments, true)) {
            $session->setFlashdata('error', 'Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.');

            $tujuan = self::DASHBOARD_BY_ROLE[$role] ?? '/login';

            return redirect()->to($tujuan);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
