<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Kebalikan dari RoleFilter: mencegah pengguna yang sudah login membuka
 * kembali halaman login, langsung diarahkan ke dashboard sesuai role-nya.
 */
class GuestFilter implements FilterInterface
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

        if ($session->get('isLoggedIn')) {
            $tujuan = self::DASHBOARD_BY_ROLE[$session->get('role')] ?? '/login';

            return redirect()->to($tujuan);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
