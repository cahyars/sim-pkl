<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruPembimbingSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $guru = [
            ['nama' => 'Dedi Kurniawan, S.Kom.',    'username' => 'dedi.k',    'nip' => '198504122010011003', 'jurusan_ampu' => 'Rekayasa Perangkat Lunak',  'no_hp' => '081234110001'],
            ['nama' => 'Sri Wahyuni, S.Pd.',        'username' => 'sri.w',     'nip' => '198811232011012004', 'jurusan_ampu' => 'Teknik Komputer & Jaringan', 'no_hp' => '081234110002'],
            ['nama' => 'Agus Setiawan, S.T.',       'username' => 'agus.s',    'nip' => '199002172014021005', 'jurusan_ampu' => 'Teknik Kendaraan Ringan',    'no_hp' => '081234110003'],
            ['nama' => 'Rina Marlina, S.E.',        'username' => 'rina.m',    'nip' => '199206302015032006', 'jurusan_ampu' => 'Akuntansi Keuangan',         'no_hp' => '081234110004'],
        ];

        foreach ($guru as $g) {
            $this->db->table('users')->insert([
                'nama'       => $g['nama'],
                'email'      => $g['username'] . '@smkn1subang.sch.id',
                'username'   => $g['username'],
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'guru_pembimbing',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->db->table('guru_pembimbing')->insert([
                'user_id'      => $this->db->insertID(),
                'nip'          => $g['nip'],
                'no_hp'        => $g['no_hp'],
                'jurusan_ampu' => $g['jurusan_ampu'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
