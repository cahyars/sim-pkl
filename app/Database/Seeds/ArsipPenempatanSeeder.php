<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ArsipPenempatanSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        // Cek apakah sudah ada siswa di tahun ajaran 4
        $ada = $db->table('siswa')->where('tahun_ajaran_id', 4)->countAllResults();
        if ($ada > 0) {
            return;
        }

        // Siswa arsip tahun ajaran 2024/2025 Genap
        $siswaArsip = [
            [
                'user' => [
                    'username' => '2223001',
                    'email'    => 'dimas.saputra.alumni@smkn1subang.sch.id',
                    'password' => password_hash('password123', PASSWORD_BCRYPT),
                    'role'     => 'siswa',
                    'nama'     => 'Dimas Saputra (Alumni)',
                    'is_active'=> 0,
                    'created_at' => '2025-01-01 08:00:00',
                ],
                'siswa' => [
                    'nis'             => '2223001',
                    'nisn'            => '0051234001',
                    'kelas'           => 'XII RPL 1',
                    'jurusan'         => 'Rekayasa Perangkat Lunak',
                    'no_hp'           => '08123456001',
                    'alamat'          => 'Jl. Raya Ciereng No. 12, Subang',
                    'tahun_ajaran_id' => 4,
                    'status_pkl'      => 'selesai',
                    'created_at'      => '2025-01-01 08:00:00',
                ],
            ],
            [
                'user' => [
                    'username' => '2223002',
                    'email'    => 'anisa.lestari.alumni@smkn1subang.sch.id',
                    'password' => password_hash('password123', PASSWORD_BCRYPT),
                    'role'     => 'siswa',
                    'nama'     => 'Anisa Tri Lestari (Alumni)',
                    'is_active'=> 0,
                    'created_at' => '2025-01-01 08:00:00',
                ],
                'siswa' => [
                    'nis'             => '2223002',
                    'nisn'            => '0051234002',
                    'kelas'           => 'XII AKL 2',
                    'jurusan'         => 'Akuntansi Keuangan',
                    'no_hp'           => '08123456002',
                    'alamat'          => 'Jl. Mayjen Sutoyo No. 45, Subang',
                    'tahun_ajaran_id' => 4,
                    'status_pkl'      => 'selesai',
                    'created_at'      => '2025-01-01 08:00:00',
                ],
            ],
            [
                'user' => [
                    'username' => '2223003',
                    'email'    => 'bagas.pratama.alumni@smkn1subang.sch.id',
                    'password' => password_hash('password123', PASSWORD_BCRYPT),
                    'role'     => 'siswa',
                    'nama'     => 'Bagas Pratama (Alumni)',
                    'is_active'=> 0,
                    'created_at' => '2025-01-01 08:00:00',
                ],
                'siswa' => [
                    'nis'             => '2223003',
                    'nisn'            => '0051234003',
                    'kelas'           => 'XII TKJ 1',
                    'jurusan'         => 'Teknik Komputer & Jaringan',
                    'no_hp'           => '08123456003',
                    'alamat'          => 'Jl. Veteran No. 8, Subang',
                    'tahun_ajaran_id' => 4,
                    'status_pkl'      => 'selesai',
                    'created_at'      => '2025-01-01 08:00:00',
                ],
            ],
        ];

        $tempat = $db->table('tempat_pkl')->get()->getFirstRow('array');
        $guru   = $db->table('guru_pembimbing')->get()->getFirstRow('array');
        $pl     = $db->table('pembimbing_lapangan')->get()->getFirstRow('array');

        foreach ($siswaArsip as $sa) {
            $db->table('users')->insert($sa['user']);
            $userId = $db->insertID();

            $sa['siswa']['user_id'] = $userId;
            $db->table('siswa')->insert($sa['siswa']);
            $siswaId = $db->insertID();

            if ($tempat && $guru) {
                $db->table('penempatan_pkl')->insert([
                    'siswa_id'               => $siswaId,
                    'tempat_pkl_id'          => $tempat['id'],
                    'guru_pembimbing_id'     => $guru['id'],
                    'pembimbing_lapangan_id' => $pl ? $pl['id'] : null,
                    'tanggal_mulai'          => '2025-01-13',
                    'tanggal_selesai'        => '2025-05-30',
                    'status'                 => 'selesai',
                    'keterangan'             => 'Penempatan PKL Arsip Periode 2024/2025',
                    'created_at'             => '2025-01-13 08:00:00',
                    'updated_at'             => '2025-05-30 15:00:00',
                ]);
                $penempatanId = $db->insertID();

                // Tambahkan nilai akhir arsip
                $db->table('penilaian')->insert([
                    'siswa_id'               => $siswaId,
                    'pembimbing_lapangan_id' => $pl ? $pl['id'] : 1,
                    'nilai_akhir'            => 90.50,
                    'feedback'               => 'Siswa menunjukkan kedisiplinan dan kinerja sangat baik sepanjang masa PKL.',
                    'status'                 => 'final',
                    'tanggal_penilaian'      => '2025-05-28',
                    'finalized_at'           => '2025-05-28 14:00:00',
                    'sakit'                  => 0,
                    'izin'                   => 1,
                    'tanpa_keterangan'       => 0,
                    'pimpinan_nama'          => 'IR. H. BAMBANG SURYO, M.M.',
                    'pimpinan_nip'           => 'DIR-19750812-001',
                    'instruktur_nama'        => 'HENDRA GUNAWAN, S.T.',
                    'instruktur_nip'         => 'DEV-19920315-042',
                    'created_at'             => '2025-05-28 14:00:00',
                    'updated_at'             => '2025-05-28 14:00:00',
                ]);
            }
        }
    }
}
