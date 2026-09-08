<?php

namespace App\Database\Seeds;

use App\Models\PenilaianModel;
use CodeIgniter\Database\Seeder;

class PenilaianSeeder extends Seeder
{
    /**
     * Contoh penilaian: dua sudah final (nilai tampil ke siswa & guru),
     * satu masih draft (masih bisa diedit pembimbing lapangan).
     */
    private array $contoh = [
        '2324001' => ['status' => 'final', 'nilai' => [90, 88, 85, 92, 90], 'feedback' => 'Ananda menunjukkan kemampuan teknis di atas rata-rata siswa PKL. Cepat memahami instruksi dan berani bertanya. Perlu meningkatkan ketelitian dalam penulisan dokumentasi kode.'],
        '2324006' => ['status' => 'final', 'nilai' => [85, 90, 78, 82, 88], 'feedback' => 'Ananda disiplin dan mudah bekerja sama dengan tim teknisi. Sudah mampu melakukan crimping dan konfigurasi dasar MikroTik secara mandiri. Inisiatif masih perlu ditingkatkan.'],
        '2324013' => ['status' => 'draft', 'nilai' => [88, 85, 80, 86, 90], 'feedback' => 'Catatan sementara: ananda teliti dalam pencatatan transaksi dan sopan dalam melayani anggota koperasi.'],
    ];

    public function run()
    {
        $now   = date('Y-m-d H:i:s');
        $aspek = $this->db->table('aspek_penilaian')->where('is_active', 1)->orderBy('id', 'ASC')->get()->getResultArray();

        foreach ($this->contoh as $nis => $data) {
            $siswa = $this->db->table('siswa')->where('nis', $nis)->get()->getRowArray();

            if ($siswa === null) {
                continue;
            }

            $penempatan = $this->db->table('penempatan_pkl')
                ->where('siswa_id', $siswa['id'])
                ->where('status', 'aktif')
                ->get()->getRowArray();

            if ($penempatan === null || $penempatan['pembimbing_lapangan_id'] === null) {
                continue;
            }

            $komponen = [];

            foreach ($aspek as $i => $a) {
                $komponen[] = ['nilai' => $data['nilai'][$i] ?? 80, 'bobot' => (int) $a['bobot']];
            }

            $nilaiAkhir = PenilaianModel::hitungNilaiAkhir($komponen);

            $this->db->table('penilaian')->insert([
                'siswa_id'               => $siswa['id'],
                'pembimbing_lapangan_id' => $penempatan['pembimbing_lapangan_id'],
                'nilai_akhir'            => $nilaiAkhir,
                'feedback'               => $data['feedback'],
                'status'                 => $data['status'],
                'tanggal_penilaian'      => date('Y-m-d'),
                'finalized_at'           => $data['status'] === 'final' ? $now : null,
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            $penilaianId = $this->db->insertID();
            $detail      = [];

            foreach ($aspek as $i => $a) {
                $detail[] = [
                    'penilaian_id'       => $penilaianId,
                    'aspek_penilaian_id' => $a['id'],
                    'nilai'              => $data['nilai'][$i] ?? 80,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }

            $this->db->table('detail_penilaian')->insertBatch($detail);
        }
    }
}
