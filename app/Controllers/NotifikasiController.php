<?php

namespace App\Controllers;

use App\Models\NotifikasiModel;

/**
 * Endpoint notifikasi bersama untuk semua role yang sudah login
 * (dipakai oleh lonceng notifikasi di kedua layout).
 */
class NotifikasiController extends BaseController
{
    /**
     * Endpoint JSON yang di-polling berkala oleh lonceng notifikasi (lihat assets/js/app.js)
     * supaya notifikasi baru muncul tanpa perlu reload halaman.
     */
    public function unread()
    {
        $userId = (int) $this->session->get('user_id');
        $model  = new NotifikasiModel();

        $items = array_map(static function (array $n) {
            return [
                'judul'    => $n['judul'],
                'pesan'    => $n['pesan'],
                'waktu'    => tanggal_indo(substr($n['created_at'], 0, 10)),
                'is_read'  => (bool) $n['is_read'],
                'baca_url' => site_url('notifikasi/' . $n['id'] . '/baca'),
            ];
        }, $model->terbaru($userId, 8));

        return $this->response->setJSON([
            'count'         => $model->belumDibaca($userId),
            'items'         => $items,
            'baca_semua_url' => site_url('notifikasi/baca-semua'),
        ]);
    }

    public function baca(int $id)
    {
        $userId = (int) $this->session->get('user_id');
        $notif  = (new NotifikasiModel())->find($id);

        (new NotifikasiModel())->tandaiDibaca($id, $userId);

        $tujuan = ($notif !== null && (int) $notif['user_id'] === $userId && $notif['link']) ? $notif['link'] : null;

        return redirect()->to($tujuan ?: $this->request->getHeaderLine('Referer') ?: '/');
    }

    public function bacaSemua()
    {
        (new NotifikasiModel())->tandaiSemuaDibaca((int) $this->session->get('user_id'));

        return redirect()->to($this->request->getHeaderLine('Referer') ?: '/');
    }
}
