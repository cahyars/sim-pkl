<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table         = 'penilaian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'siswa_id', 'pembimbing_lapangan_id', 'nilai_akhir', 'feedback',
        'status', 'tanggal_penilaian', 'finalized_at',
        'sakit', 'izin', 'tanpa_keterangan',
        'pimpinan_nama', 'pimpinan_nip', 'instruktur_nama', 'instruktur_nip',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'siswa_id'               => 'required|is_natural_no_zero',
        'pembimbing_lapangan_id' => 'required|is_natural_no_zero',
        'status'                 => 'permit_empty|in_list[draft,final]',
    ];

    public function findBySiswa(int $siswaId): ?array
    {
        return $this->where('siswa_id', $siswaId)->first();
    }

    public function withSiswa()
    {
        return $this->select('
                penilaian.*,
                siswa.nis, siswa.nisn, siswa.kelas, siswa.jurusan,
                users.nama AS nama_siswa, users.email AS email_siswa
            ')
            ->join('siswa', 'siswa.id = penilaian.siswa_id')
            ->join('users', 'users.id = siswa.user_id');
    }

    /**
     * Hitung rekap kehadiran siswa dari data logbook & hari kerja berjalan.
     *
     * @return array{sakit: int, izin: int, tanpa_keterangan: int, masuk: int, total_hari_kerja: int}
     */
    public static function hitungKehadiranLogbook(int $siswaId, array $penempatan): array
    {
        $logbookModel = new LogbookModel();
        $hariIni      = date('Y-m-d');
        $sampai       = min($hariIni, $penempatan['tanggal_selesai'] ?? $hariIni);
        $mulai        = $penempatan['tanggal_mulai'] ?? $hariIni;

        $semuaLogbook = $logbookModel->where('siswa_id', $siswaId)
            ->where('tanggal_kegiatan >=', $mulai)
            ->where('tanggal_kegiatan <=', $sampai)
            ->findAll();

        $sakit = 0;
        $izin  = 0;
        $masuk = 0;

        foreach ($semuaLogbook as $lb) {
            $status = $lb['status_kehadiran'] ?? 'masuk';
            if ($status === 'sakit') {
                $sakit++;
            } elseif ($status === 'izin') {
                $izin++;
            } else {
                $masuk++;
            }
        }

        $totalHariKerja   = jumlah_hari_kerja($mulai, $sampai);
        $tanpaKeterangan  = max(0, $totalHariKerja - ($masuk + $izin + $sakit));

        return [
            'sakit'            => $sakit,
            'izin'             => $izin,
            'tanpa_keterangan' => $tanpaKeterangan,
            'masuk'            => $masuk,
            'total_hari_kerja' => $totalHariKerja,
        ];
    }

    /**
     * Nilai akhir = Σ(nilai aspek × bobot) / Σ(bobot), atau rata-rata skor.
     *
     * @param array<int, array{nilai: float, bobot: int}> $komponen
     */
    public static function hitungNilaiAkhir(array $komponen): float
    {
        $totalBobot = 0;
        $totalNilai = 0.0;

        foreach ($komponen as $item) {
            $bobot = (int) ($item['bobot'] ?? 1);
            if ($bobot <= 0) {
                $bobot = 1;
            }
            $totalBobot += $bobot;
            $totalNilai += (float) $item['nilai'] * $bobot;
        }

        if ($totalBobot === 0) {
            return 0.0;
        }

        return round($totalNilai / $totalBobot, 2);
    }

    /**
     * Predikat huruf resmi sesuai format SMKN 1 Subang:
     * 86 – 100 : A (Amat Baik)
     * 70 – 85  : B (Baik)
     * < 70     : C (Kurang)
     */
    public static function predikat(float $nilai): string
    {
        if ($nilai >= 86) {
            return 'A (Amat Baik)';
        }
        if ($nilai >= 70) {
            return 'B (Baik)';
        }

        return 'C (Kurang)';
    }
}
