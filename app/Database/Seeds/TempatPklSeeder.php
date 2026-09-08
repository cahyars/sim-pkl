<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TempatPklSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $tempat = [
            [
                'nama_perusahaan'  => 'PT Sinar Digital Nusantara',
                'alamat'           => 'Jl. Otista No. 112, Subang, Jawa Barat',
                'no_telp'          => '0260-411223',
                'email'            => 'hrd@sinardigital.co.id',
                'bidang_usaha'     => 'Software House',
                'penanggung_jawab' => 'Bpk. Hendra Gunawan',
                'kuota'            => 6,
                'pembimbing'       => [
                    ['nama' => 'Hendra Gunawan',  'jabatan' => 'Lead Developer', 'no_hp' => '081234220001', 'username' => 'hendra.g'],
                    ['nama' => 'Maya Puspita',    'jabatan' => 'HR Officer',     'no_hp' => '081234220002', 'username' => null],
                ],
            ],
            [
                'nama_perusahaan'  => 'CV Jaringan Prima Subang',
                'alamat'           => 'Jl. Dewi Sartika No. 8, Subang, Jawa Barat',
                'no_telp'          => '0260-412887',
                'email'            => 'info@jaringanprima.id',
                'bidang_usaha'     => 'Jasa Instalasi Jaringan & ISP',
                'penanggung_jawab' => 'Bpk. Yusuf Ramdani',
                'kuota'            => 4,
                'pembimbing'       => [
                    ['nama' => 'Yusuf Ramdani',   'jabatan' => 'Network Engineer', 'no_hp' => '081234220003', 'username' => 'yusuf.r'],
                ],
            ],
            [
                'nama_perusahaan'  => 'Bengkel Resmi Auto Sejahtera',
                'alamat'           => 'Jl. Raya Pagaden KM 5, Subang, Jawa Barat',
                'no_telp'          => '0260-450991',
                'email'            => 'autosejahtera@gmail.com',
                'bidang_usaha'     => 'Otomotif & Perbengkelan',
                'penanggung_jawab' => 'Bpk. Tono Suratno',
                'kuota'            => 5,
                'pembimbing'       => [
                    ['nama' => 'Tono Suratno',    'jabatan' => 'Kepala Mekanik',  'no_hp' => '081234220004', 'username' => 'tono.s'],
                ],
            ],
            [
                'nama_perusahaan'  => 'Koperasi Simpan Pinjam Mitra Warga',
                'alamat'           => 'Jl. Ahmad Yani No. 47, Subang, Jawa Barat',
                'no_telp'          => '0260-417650',
                'email'            => 'admin@mitrawarga.co.id',
                'bidang_usaha'     => 'Keuangan & Koperasi',
                'penanggung_jawab' => 'Ibu Lilis Suryani',
                'kuota'            => 4,
                'pembimbing'       => [
                    ['nama' => 'Lilis Suryani',   'jabatan' => 'Kepala Administrasi', 'no_hp' => '081234220005', 'username' => 'lilis.s'],
                ],
            ],
            [
                'nama_perusahaan'  => 'Diskominfo Kabupaten Subang',
                'alamat'           => 'Jl. Mayjend Sutoyo No. 46, Subang, Jawa Barat',
                'no_telp'          => '0260-420100',
                'email'            => 'diskominfo@subang.go.id',
                'bidang_usaha'     => 'Instansi Pemerintah - Teknologi Informasi',
                'penanggung_jawab' => 'Bpk. Rizal Fadillah',
                'kuota'            => 4,
                'pembimbing'       => [
                    ['nama' => 'Rizal Fadillah',  'jabatan' => 'Kasi Pengelolaan Data', 'no_hp' => '081234220006', 'username' => 'rizal.f'],
                ],
            ],
        ];

        foreach ($tempat as $t) {
            $this->db->table('tempat_pkl')->insert([
                'nama_perusahaan'  => $t['nama_perusahaan'],
                'alamat'           => $t['alamat'],
                'no_telp'          => $t['no_telp'],
                'email'            => $t['email'],
                'bidang_usaha'     => $t['bidang_usaha'],
                'penanggung_jawab' => $t['penanggung_jawab'],
                'kuota'            => $t['kuota'],
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            $tempatId = $this->db->insertID();

            foreach ($t['pembimbing'] as $p) {
                $userId = null;

                // Sebagian pembimbing lapangan dibuatkan akun login, sebagian hanya didata (ERD: user_id nullable).
                if ($p['username'] !== null) {
                    $this->db->table('users')->insert([
                        'nama'       => $p['nama'],
                        'email'      => $p['username'] . '@dudi.test',
                        'username'   => $p['username'],
                        'password'   => password_hash('password123', PASSWORD_DEFAULT),
                        'role'       => 'pembimbing_lapangan',
                        'is_active'  => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $userId = $this->db->insertID();
                }

                $this->db->table('pembimbing_lapangan')->insert([
                    'user_id'       => $userId,
                    'tempat_pkl_id' => $tempatId,
                    'nama'          => $p['nama'],
                    'jabatan'       => $p['jabatan'],
                    'no_hp'         => $p['no_hp'],
                    'email'         => $p['username'] !== null ? $p['username'] . '@dudi.test' : null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
    }
}
