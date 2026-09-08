<?php

namespace App\Controllers;

class Home extends BaseController
{
    private const DASHBOARD_BY_ROLE = [
        'siswa'                => '/siswa/dashboard',
        'guru_pembimbing'      => '/guru/dashboard',
        'pembimbing_lapangan'  => '/pembimbing-lapangan/dashboard',
        'admin'                => '/admin/dashboard',
    ];

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(self::DASHBOARD_BY_ROLE[session()->get('role')]);
        }

        return redirect()->to('/login');
    }
}
