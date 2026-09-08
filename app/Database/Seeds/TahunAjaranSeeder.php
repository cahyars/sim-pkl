<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('tahun_ajaran')->insertBatch([
            [
                'nama_tahun_ajaran' => '2025/2026',
                'semester'          => 'genap',
                'tanggal_mulai'     => '2026-01-05',
                'tanggal_selesai'   => '2026-06-20',
                'is_active'         => 0,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nama_tahun_ajaran' => '2026/2027',
                'semester'          => 'ganjil',
                'tanggal_mulai'     => '2026-07-06',
                'tanggal_selesai'   => '2026-12-18',
                'is_active'         => 1,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);
    }
}
