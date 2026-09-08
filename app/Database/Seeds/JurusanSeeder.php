<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'kode'                 => 'AKL',
                'nama'                 => 'Akuntansi Keuangan',
                'bidang_keahlian'      => 'Bisnis dan Manajemen',
                'program_keahlian'     => 'Akuntansi dan Keuangan Lembaga',
                'konsentrasi_keahlian' => 'Akuntansi',
                'kepala_program'       => 'Hj. Siti Rohmah, S.Pd., M.M.',
                'nip_kepala_program'   => '197605122002122001',
                'deskripsi'            => 'Konsentrasi keahlian yang membekali peserta didik dengan keterampilan akuntansi manual dan komputerisasi, perpajakan, serta pengelolaan keuangan lembaga.',
                'is_active'            => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'kode'                 => 'TKJ',
                'nama'                 => 'Teknik Komputer & Jaringan',
                'bidang_keahlian'      => 'Teknologi Informasi dan Komunikasi',
                'program_keahlian'     => 'Teknik Jaringan Komputer dan Telekomunikasi',
                'konsentrasi_keahlian' => 'Teknik Komputer dan Jaringan',
                'kepala_program'       => 'Cecep Hendrayana, S.T., M.Kom.',
                'nip_kepala_program'   => '198208152008011005',
                'deskripsi'            => 'Mempersiapkan tenaga teknisi yang andal dalam instalasi jaringan lokal & nirkabel, administrasi server Linux/Windows, serta keamanan jaringan.',
                'is_active'            => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'kode'                 => 'RPL',
                'nama'                 => 'Rekayasa Perangkat Lunak',
                'bidang_keahlian'      => 'Teknologi Informasi dan Komunikasi',
                'program_keahlian'     => 'Pengembangan Perangkat Lunak dan Gim',
                'konsentrasi_keahlian' => 'Rekayasa Perangkat Lunak',
                'kepala_program'       => 'Dedi Kurniawan, S.Kom.',
                'nip_kepala_program'   => '198501012010011003',
                'deskripsi'            => 'Fokus pada pengembangan aplikasi web, mobile, basis data, dan rekayasa perangkat lunak modern berbasis industri 4.0.',
                'is_active'            => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
            [
                'kode'                 => 'TKR',
                'nama'                 => 'Teknik Kendaraan Ringan',
                'bidang_keahlian'      => 'Teknologi dan Rekayasa',
                'program_keahlian'     => 'Teknik Otomotif',
                'konsentrasi_keahlian' => 'Teknik Kendaraan Ringan',
                'kepala_program'       => 'Agus Supriatna, S.T.',
                'nip_kepala_program'   => '197903202005011008',
                'deskripsi'            => 'Mencetak teknisi otomotif profesional yang menguasai perawatan dan perbaikan mesin berkala, chasis, pemindah tenaga, dan sistem kelistrikan otomotif modern.',
                'is_active'            => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ],
        ];

        $builder = $this->db->table('jurusan');

        foreach ($data as $item) {
            $ada = $builder->where('kode', $item['kode'])->countAllResults();
            if ($ada === 0) {
                $builder->insert($item);
            } else {
                $builder->where('kode', $item['kode'])->update([
                    'nama'                 => $item['nama'],
                    'bidang_keahlian'      => $item['bidang_keahlian'],
                    'program_keahlian'     => $item['program_keahlian'],
                    'konsentrasi_keahlian' => $item['konsentrasi_keahlian'],
                    'kepala_program'       => $item['kepala_program'],
                    'nip_kepala_program'   => $item['nip_kepala_program'],
                    'deskripsi'            => $item['deskripsi'],
                    'updated_at'           => $now,
                ]);
            }
        }
    }
}
