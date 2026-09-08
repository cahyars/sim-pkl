<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;

class PengaturanController extends BaseController
{
    /**
     * Kunci pengaturan yang boleh diedit lewat form ini, dikelompokkan per section tampilan.
     */
    private const GRUP = [
        'Reminder Notifikasi Logbook' => ['reminder_aktif', 'reminder_hari', 'reminder_tembusan_guru'],
        'Upload Dokumentasi Logbook'  => ['upload_max_size', 'upload_allowed_ext'],
        'Identitas Sekolah (Laporan)' => ['nama_sekolah', 'alamat_sekolah', 'kepala_sekolah'],
    ];

    public function index()
    {
        $model  = new PengaturanModel();
        $semua  = $model->semua();
        $byKunci = [];

        foreach ($semua as $row) {
            $byKunci[$row['kunci']] = $row;
        }

        return view('admin/pengaturan/index', [
            'title'    => 'Pengaturan Sistem',
            'grup'     => self::GRUP,
            'byKunci'  => $byKunci,
        ]);
    }

    public function update()
    {
        $model = new PengaturanModel();

        $rules = [
            'reminder_hari' => 'required|is_natural_no_zero',
            'upload_max_size' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/pengaturan')->withInput()->with('errors', $this->validator->getErrors());
        }

        $semuaKunci = array_merge(...array_values(self::GRUP));

        foreach ($semuaKunci as $kunci) {
            if ($kunci === 'reminder_aktif' || $kunci === 'reminder_tembusan_guru') {
                $model->simpan($kunci, $this->request->getPost($kunci) ? '1' : '0');

                continue;
            }

            $nilai = $this->request->getPost($kunci);

            if ($nilai !== null) {
                $model->simpan($kunci, $nilai);
            }
        }

        return redirect()->to('/admin/pengaturan')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
