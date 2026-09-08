<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenempatanSeeder extends Seeder
{
    /**
     * Periode PKL demo: 6 Juli 2026 s.d. 30 September 2026 (berjalan pada saat seeding).
     */
    public const TANGGAL_MULAI   = '2026-07-06';
    public const TANGGAL_SELESAI = '2026-09-30';

    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Peta NIS siswa -> nama perusahaan, NIP guru, dan nama pembimbing lapangan.
        $rencana = [
            '2324001' => ['PT Sinar Digital Nusantara',          '198504122010011003', 'Hendra Gunawan'],
            '2324002' => ['PT Sinar Digital Nusantara',          '198504122010011003', 'Hendra Gunawan'],
            '2324003' => ['PT Sinar Digital Nusantara',          '198504122010011003', 'Hendra Gunawan'],
            '2324004' => ['PT Sinar Digital Nusantara',          '198504122010011003', 'Maya Puspita'],
            '2324005' => ['PT Sinar Digital Nusantara',          '198504122010011003', 'Maya Puspita'],
            '2324006' => ['CV Jaringan Prima Subang',            '198811232011012004', 'Yusuf Ramdani'],
            '2324007' => ['CV Jaringan Prima Subang',            '198811232011012004', 'Yusuf Ramdani'],
            '2324008' => ['CV Jaringan Prima Subang',            '198811232011012004', 'Yusuf Ramdani'],
            '2324009' => ['Diskominfo Kabupaten Subang',         '198811232011012004', 'Rizal Fadillah'],
            '2324010' => ['Bengkel Resmi Auto Sejahtera',        '199002172014021005', 'Tono Suratno'],
            '2324011' => ['Bengkel Resmi Auto Sejahtera',        '199002172014021005', 'Tono Suratno'],
            '2324012' => ['Bengkel Resmi Auto Sejahtera',        '199002172014021005', 'Tono Suratno'],
            '2324013' => ['Koperasi Simpan Pinjam Mitra Warga',  '199206302015032006', 'Lilis Suryani'],
            '2324014' => ['Koperasi Simpan Pinjam Mitra Warga',  '199206302015032006', 'Lilis Suryani'],
            '2324015' => ['Koperasi Simpan Pinjam Mitra Warga',  '199206302015032006', 'Lilis Suryani'],
        ];

        foreach ($rencana as $nis => [$namaPerusahaan, $nip, $namaPembimbing]) {
            $siswa = $this->db->table('siswa')->where('nis', $nis)->get()->getRowArray();
            $tempat = $this->db->table('tempat_pkl')->where('nama_perusahaan', $namaPerusahaan)->get()->getRowArray();
            $guru = $this->db->table('guru_pembimbing')->where('nip', $nip)->get()->getRowArray();
            $pembimbing = $this->db->table('pembimbing_lapangan')
                ->where('nama', $namaPembimbing)
                ->where('tempat_pkl_id', $tempat['id'])
                ->get()->getRowArray();

            $this->db->table('penempatan_pkl')->insert([
                'siswa_id'               => $siswa['id'],
                'tempat_pkl_id'          => $tempat['id'],
                'guru_pembimbing_id'     => $guru['id'],
                'pembimbing_lapangan_id' => $pembimbing['id'],
                'tanggal_mulai'          => self::TANGGAL_MULAI,
                'tanggal_selesai'        => self::TANGGAL_SELESAI,
                'status'                 => 'aktif',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            $this->db->table('siswa')->where('id', $siswa['id'])->update(['status_pkl' => 'aktif']);
        }
    }
}
