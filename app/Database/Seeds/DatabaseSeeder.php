<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder utama SIM-PKL. Jalankan dengan: php spark db:seed DatabaseSeeder
 * Urutan penting karena antar tabel terikat foreign key.
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('PengaturanSeeder');
        $this->call('TahunAjaranSeeder');
        $this->call('AspekPenilaianSeeder');
        $this->call('UserAdminSeeder');
        $this->call('GuruPembimbingSeeder');
        $this->call('TempatPklSeeder');
        $this->call('SiswaSeeder');
        $this->call('PenempatanSeeder');
        $this->call('LogbookSeeder');
        $this->call('PenilaianSeeder');
    }
}
