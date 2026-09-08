<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ArsipTahunAjaranSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $db  = $this->db;

        $daftarArsip = [
            [
                'nama_tahun_ajaran' => '2024/2025',
                'semester'          => 'genap',
                'tanggal_mulai'     => '2025-01-06',
                'tanggal_selesai'   => '2025-06-20',
                'is_active'         => 0,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'semester'          => 'ganjil',
                'tanggal_mulai'     => '2024-07-15',
                'tanggal_selesai'   => '2024-12-20',
                'is_active'         => 0,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        foreach ($daftarArsip as $ta) {
            $ada = $db->table('tahun_ajaran')
                ->where('nama_tahun_ajaran', $ta['nama_tahun_ajaran'])
                ->where('semester', $ta['semester'])
                ->countAllResults();

            if ($ada === 0) {
                $db->table('tahun_ajaran')->insert($ta);
            }
        }
    }
}
