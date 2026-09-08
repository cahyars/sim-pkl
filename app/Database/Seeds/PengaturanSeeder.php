<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        $now  = date('Y-m-d H:i:s');
        $data = [
            [
                'kunci'      => 'reminder_hari',
                'nilai'      => '3',
                'keterangan' => 'Kirim reminder bila siswa tidak mengisi logbook selama X hari.',
            ],
            [
                'kunci'      => 'reminder_tembusan_guru',
                'nilai'      => '1',
                'keterangan' => 'Kirim tembusan email reminder ke guru pembimbing (1 = ya, 0 = tidak).',
            ],
            [
                'kunci'      => 'reminder_aktif',
                'nilai'      => '1',
                'keterangan' => 'Aktifkan pengiriman reminder otomatis (1 = ya, 0 = tidak).',
            ],
            [
                'kunci'      => 'upload_max_size',
                'nilai'      => '2048',
                'keterangan' => 'Ukuran maksimal file dokumentasi logbook dalam KB.',
            ],
            [
                'kunci'      => 'upload_allowed_ext',
                'nilai'      => 'jpg,jpeg,png,pdf',
                'keterangan' => 'Ekstensi file dokumentasi yang diizinkan, pisahkan dengan koma.',
            ],
            [
                'kunci'      => 'nama_sekolah',
                'nilai'      => 'SMK Negeri 1 Subang',
                'keterangan' => 'Nama sekolah yang tampil pada kop laporan.',
            ],
            [
                'kunci'      => 'alamat_sekolah',
                'nilai'      => 'Jl. Arief Rahman Hakim No. 35, Subang, Jawa Barat',
                'keterangan' => 'Alamat sekolah pada kop laporan.',
            ],
            [
                'kunci'      => 'kepala_sekolah',
                'nilai'      => 'Drs. H. Endang Suryana, M.Pd.',
                'keterangan' => 'Nama kepala sekolah untuk tanda tangan laporan.',
            ],
        ];

        foreach ($data as &$row) {
            $row['updated_at'] = $now;
        }

        $this->db->table('pengaturan')->insertBatch($data);
    }
}
