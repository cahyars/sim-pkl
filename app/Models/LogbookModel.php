<?php

namespace App\Models;

use CodeIgniter\Model;

class LogbookModel extends Model
{
    protected $table         = 'logbook';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'siswa_id', 'penempatan_pkl_id', 'tanggal_kegiatan', 'status_kehadiran', 'jam_mulai', 'jam_selesai',
        'uraian_kegiatan', 'kendala', 'status', 'catatan_guru', 'validated_by',
        'validated_at', 'submitted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'siswa_id'          => 'required|is_natural_no_zero',
        'penempatan_pkl_id' => 'required|is_natural_no_zero',
        'tanggal_kegiatan'  => 'required|valid_date[Y-m-d]',
        'status_kehadiran'  => 'permit_empty|in_list[masuk,izin,sakit]',
        'uraian_kegiatan'   => 'required|min_length[10]',
        'status'            => 'permit_empty|in_list[draft,menunggu_validasi,disetujui,revisi,ditolak]',
    ];

    protected $validationMessages = [
        'uraian_kegiatan' => [
            'required'   => 'Uraian kegiatan wajib diisi.',
            'min_length' => 'Uraian kegiatan minimal 10 karakter.',
        ],
    ];

    /**
     * Status yang masih boleh diedit siswa.
     */
    public const EDITABLE = ['draft', 'revisi'];

    /**
     * Status kehadiran yang berarti siswa benar-benar hadir (punya jam masuk/pulang).
     */
    public const HADIR = 'masuk';

    public function withSiswa()
    {
        return $this->select('
                logbook.*,
                siswa.nis, siswa.kelas, siswa.jurusan,
                users.nama AS nama_siswa, users.email AS email_siswa
            ')
            ->join('siswa', 'siswa.id = logbook.siswa_id')
            ->join('users', 'users.id = siswa.user_id');
    }

    public function sudahAdaDiTanggal(int $siswaId, string $tanggal, ?int $kecualiId = null): bool
    {
        $builder = $this->where('siswa_id', $siswaId)->where('tanggal_kegiatan', $tanggal);

        if ($kecualiId !== null) {
            $builder->where('id !=', $kecualiId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Rekap jumlah logbook per status untuk satu siswa.
     */
    public function rekapStatus(int $siswaId): array
    {
        $rows = $this->select('status, COUNT(*) AS jumlah')
            ->where('siswa_id', $siswaId)
            ->groupBy('status')
            ->findAll();

        $rekap = ['draft' => 0, 'menunggu_validasi' => 0, 'disetujui' => 0, 'revisi' => 0, 'ditolak' => 0];

        foreach ($rows as $row) {
            $rekap[$row['status']] = (int) $row['jumlah'];
        }

        $rekap['total'] = array_sum($rekap);

        return $rekap;
    }

    public function tanggalLogbookTerakhir(int $siswaId): ?string
    {
        $row = $this->select('MAX(tanggal_kegiatan) AS terakhir')
            ->where('siswa_id', $siswaId)
            ->where('status !=', 'draft')
            ->first();

        return $row['terakhir'] ?? null;
    }

    /**
     * Jumlah hari unik logbook (bukan draft) terisi dalam rentang tanggal —
     * dipakai laporan rekap kehadiran/keaktifan (Fitur §4.6).
     */
    public function hariTerisi(int $siswaId, string $mulai, string $sampai): int
    {
        return $this->where('siswa_id', $siswaId)
            ->where('status !=', 'draft')
            ->where('tanggal_kegiatan >=', $mulai)
            ->where('tanggal_kegiatan <=', $sampai)
            ->countAllResults();
    }

    /**
     * Logbook (bukan draft) dalam satu rentang tanggal, diindeks per tanggal (Y-m-d)
     * supaya gampang dicocokkan ke kalender presensi.
     *
     * @return array<string, array>
     */
    public function presensiPerTanggal(int $siswaId, string $mulai, string $sampai): array
    {
        $rows = $this->where('siswa_id', $siswaId)
            ->where('status !=', 'draft')
            ->where('tanggal_kegiatan >=', $mulai)
            ->where('tanggal_kegiatan <=', $sampai)
            ->findAll();

        $hasil = [];
        foreach ($rows as $row) {
            $hasil[$row['tanggal_kegiatan']] = $row;
        }

        return $hasil;
    }

    /**
     * Bangun data kalender presensi satu bulan penuh untuk satu siswa, dipakai
     * bersama oleh halaman Presensi (siswa) dan Daftar Hadir (guru & export).
     *
     * @param array{tanggal_mulai: string, tanggal_selesai: string} $penempatan
     *
     * @return array{hari: list<array>, rekap: array<string, int>}
     */
    public function kalenderPresensi(int $siswaId, array $penempatan, string $bulan): array
    {
        $rekap = ['masuk' => 0, 'izin' => 0, 'sakit' => 0, 'belum_mengisi' => 0];
        $hari  = [];

        $awalBulan  = $bulan . '-01';
        $akhirBulan = date('Y-m-t', strtotime($awalBulan));

        $mulai  = max($awalBulan, $penempatan['tanggal_mulai']);
        $sampai = min($akhirBulan, min(date('Y-m-d'), $penempatan['tanggal_selesai']));

        if ($mulai > $sampai) {
            return ['hari' => [], 'rekap' => $rekap];
        }

        $logbookPerTanggal = $this->presensiPerTanggal($siswaId, $awalBulan, $akhirBulan);

        $periode = new \DatePeriod(
            new \DateTime($awalBulan),
            new \DateInterval('P1D'),
            (new \DateTime($akhirBulan))->modify('+1 day')
        );

        foreach ($periode as $tanggalObj) {
            $tanggalStr = $tanggalObj->format('Y-m-d');

            if ($tanggalStr < $penempatan['tanggal_mulai'] || $tanggalStr > $penempatan['tanggal_selesai']) {
                continue;
            }

            $akhirPekan = (int) $tanggalObj->format('N') >= 6;
            $entry      = $logbookPerTanggal[$tanggalStr] ?? null;
            $belumTiba  = $tanggalStr > date('Y-m-d');

            if ($entry !== null) {
                $rekap[$entry['status_kehadiran']]++;
            } elseif (! $akhirPekan && ! $belumTiba) {
                $rekap['belum_mengisi']++;
            }

            $hari[] = [
                'tanggal'    => $tanggalStr,
                'akhirPekan' => $akhirPekan,
                'belumTiba'  => $belumTiba,
                'entry'      => $entry,
            ];
        }

        return ['hari' => $hari, 'rekap' => $rekap];
    }
}
