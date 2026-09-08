<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('users')->insert([
            'nama'       => 'Tata Usaha PKL',
            'email'      => 'admin@smkn1subang.sch.id',
            'username'   => 'admin',
            'password'   => password_hash('password123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
