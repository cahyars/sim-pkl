<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $now            = date('Y-m-d H:i:s');
        $tahunAjaranId  = $this->db->table('tahun_ajaran')->where('is_active', 1)->get()->getRowArray()['id'];

        $siswa = [
            ['nama' => 'Ahmad Fauzi Ramadhan',   'nis' => '2324001', 'nisn' => '0071234501', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Bella Anggraini',        'nis' => '2324002', 'nisn' => '0071234502', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Cahya Nugraha',          'nis' => '2324003', 'nisn' => '0071234503', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Dian Purnama Sari',      'nis' => '2324004', 'nisn' => '0071234504', 'kelas' => 'XII RPL 2', 'jurusan' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Eko Prasetyo',           'nis' => '2324005', 'nisn' => '0071234505', 'kelas' => 'XII RPL 2', 'jurusan' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Fitri Handayani',        'nis' => '2324006', 'nisn' => '0071234506', 'kelas' => 'XII TKJ 1', 'jurusan' => 'Teknik Komputer & Jaringan'],
            ['nama' => 'Gilang Ramadhan',        'nis' => '2324007', 'nisn' => '0071234507', 'kelas' => 'XII TKJ 1', 'jurusan' => 'Teknik Komputer & Jaringan'],
            ['nama' => 'Hana Salsabila',         'nis' => '2324008', 'nisn' => '0071234508', 'kelas' => 'XII TKJ 1', 'jurusan' => 'Teknik Komputer & Jaringan'],
            ['nama' => 'Irfan Maulana',          'nis' => '2324009', 'nisn' => '0071234509', 'kelas' => 'XII TKJ 2', 'jurusan' => 'Teknik Komputer & Jaringan'],
            ['nama' => 'Jihan Nabila Putri',     'nis' => '2324010', 'nisn' => '0071234510', 'kelas' => 'XII TKR 1', 'jurusan' => 'Teknik Kendaraan Ringan'],
            ['nama' => 'Krisna Adi Wijaya',      'nis' => '2324011', 'nisn' => '0071234511', 'kelas' => 'XII TKR 1', 'jurusan' => 'Teknik Kendaraan Ringan'],
            ['nama' => 'Lutfi Hakim',            'nis' => '2324012', 'nisn' => '0071234512', 'kelas' => 'XII TKR 1', 'jurusan' => 'Teknik Kendaraan Ringan'],
            ['nama' => 'Mira Oktaviani',         'nis' => '2324013', 'nisn' => '0071234513', 'kelas' => 'XII AKL 1', 'jurusan' => 'Akuntansi Keuangan'],
            ['nama' => 'Nanda Ayu Lestari',      'nis' => '2324014', 'nisn' => '0071234514', 'kelas' => 'XII AKL 1', 'jurusan' => 'Akuntansi Keuangan'],
            ['nama' => 'Oki Setiawan',           'nis' => '2324015', 'nisn' => '0071234515', 'kelas' => 'XII AKL 1', 'jurusan' => 'Akuntansi Keuangan'],
            // Tiga siswa terakhir sengaja dibiarkan belum ditempatkan untuk uji skenario blackbox no. 12.
            ['nama' => 'Putri Rahmawati',        'nis' => '2324016', 'nisn' => '0071234516', 'kelas' => 'XII RPL 2', 'jurusan' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Rangga Saputra',         'nis' => '2324017', 'nisn' => '0071234517', 'kelas' => 'XII TKJ 2', 'jurusan' => 'Teknik Komputer & Jaringan'],
            ['nama' => 'Siti Nurhaliza',         'nis' => '2324018', 'nisn' => '0071234518', 'kelas' => 'XII AKL 1', 'jurusan' => 'Akuntansi Keuangan'],
        ];

        foreach ($siswa as $s) {
            $this->db->table('users')->insert([
                'nama'       => $s['nama'],
                'email'      => $s['nis'] . '@siswa.smkn1subang.sch.id',
                'username'   => $s['nis'],
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'siswa',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->db->table('siswa')->insert([
                'user_id'         => $this->db->insertID(),
                'nis'             => $s['nis'],
                'nisn'            => $s['nisn'],
                'kelas'           => $s['kelas'],
                'jurusan'         => $s['jurusan'],
                'no_hp'           => '0812' . str_pad((string) random_int(10000000, 99999999), 8, '0'),
                'alamat'          => 'Kec. Subang, Kabupaten Subang, Jawa Barat',
                'tahun_ajaran_id' => $tahunAjaranId,
                'status_pkl'      => 'belum_ditempatkan',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
    }
}
