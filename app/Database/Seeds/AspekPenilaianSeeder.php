<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AspekPenilaianSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Lima aspek default sesuai contoh Fitur §4.4, total bobot 100.
        $data = [
            ['nama_aspek' => 'Disiplin',         'deskripsi' => 'Kehadiran, ketepatan waktu, dan kepatuhan pada aturan perusahaan.', 'bobot' => 20],
            ['nama_aspek' => 'Kerjasama',        'deskripsi' => 'Kemampuan bekerja dalam tim dan berkomunikasi dengan rekan kerja.', 'bobot' => 20],
            ['nama_aspek' => 'Inisiatif',        'deskripsi' => 'Kemauan mengambil tindakan tanpa harus selalu diperintah.',          'bobot' => 20],
            ['nama_aspek' => 'Kualitas Kerja',   'deskripsi' => 'Ketelitian, kerapian, dan hasil akhir pekerjaan yang diberikan.',    'bobot' => 25],
            ['nama_aspek' => 'Tanggung Jawab',   'deskripsi' => 'Penyelesaian tugas sesuai target dan kesediaan menanggung hasil.',   'bobot' => 15],
        ];

        foreach ($data as &$row) {
            $row['is_active']  = 1;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        $this->db->table('aspek_penilaian')->insertBatch($data);
    }
}
