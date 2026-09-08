<?php

namespace App\Models;

use CodeIgniter\Model;

class PenempatanPklModel extends Model
{
    protected $table         = 'penempatan_pkl';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'siswa_id', 'tempat_pkl_id', 'guru_pembimbing_id', 'pembimbing_lapangan_id',
        'tanggal_mulai', 'tanggal_selesai', 'status', 'keterangan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'siswa_id'           => 'required|is_natural_no_zero',
        'tempat_pkl_id'      => 'required|is_natural_no_zero',
        'guru_pembimbing_id' => 'required|is_natural_no_zero',
        'tanggal_mulai'      => 'required|valid_date[Y-m-d]',
        'tanggal_selesai'    => 'required|valid_date[Y-m-d]',
        'status'             => 'permit_empty|in_list[aktif,selesai,dibatalkan]',
    ];

    /**
     * Penempatan aktif milik siswa. Business rule ERD: maksimal satu per siswa.
     */
    public function getAktifBySiswa(int $siswaId): ?array
    {
        return $this->where('siswa_id', $siswaId)->where('status', 'aktif')->first();
    }

    public function punyaPenempatanAktif(int $siswaId, ?int $kecualiId = null): bool
    {
        $builder = $this->where('siswa_id', $siswaId)->where('status', 'aktif');

        if ($kecualiId !== null) {
            $builder->where('id !=', $kecualiId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Query builder penempatan lengkap dengan relasi siswa, tempat, dan kedua pembimbing.
     */
    public function withRelasi()
    {
        return $this->select('
                penempatan_pkl.*,
                siswa.nis, siswa.nisn, siswa.kelas, siswa.jurusan, siswa.status_pkl,
                us.nama AS nama_siswa, us.email AS email_siswa, us.id AS user_id_siswa,
                tempat_pkl.nama_perusahaan, tempat_pkl.alamat AS alamat_perusahaan,
                ug.nama AS nama_guru, ug.email AS email_guru, guru_pembimbing.nip AS nip_guru,
                pembimbing_lapangan.nama AS nama_pembimbing_lapangan, pembimbing_lapangan.jabatan AS jabatan_pembimbing_lapangan
            ')
            ->join('siswa', 'siswa.id = penempatan_pkl.siswa_id')
            ->join('users us', 'us.id = siswa.user_id')
            ->join('tempat_pkl', 'tempat_pkl.id = penempatan_pkl.tempat_pkl_id')
            ->join('guru_pembimbing', 'guru_pembimbing.id = penempatan_pkl.guru_pembimbing_id')
            ->join('users ug', 'ug.id = guru_pembimbing.user_id')
            ->join('pembimbing_lapangan', 'pembimbing_lapangan.id = penempatan_pkl.pembimbing_lapangan_id', 'left');
    }

    public function byGuru(int $guruPembimbingId, string $status = 'aktif'): array
    {
        return $this->byGuruBuilder($guruPembimbingId, $status)->findAll();
    }

    /**
     * Versi builder dari byGuru() supaya halaman daftar bisa memakai paginate()
     * dan menambah kondisi pencarian sendiri.
     */
    public function byGuruBuilder(int $guruPembimbingId, string $status = 'aktif')
    {
        $builder = $this->withRelasi()->where('penempatan_pkl.guru_pembimbing_id', $guruPembimbingId);

        if ($status !== '') {
            $builder->where('penempatan_pkl.status', $status);
        }

        return $builder->orderBy('us.nama', 'ASC');
    }

    public function byPembimbingLapangan(int $pembimbingLapanganId, string $status = 'aktif'): array
    {
        return $this->byPembimbingLapanganBuilder($pembimbingLapanganId, $status)->findAll();
    }

    /**
     * Versi builder dari byPembimbingLapangan() untuk kebutuhan paginate().
     */
    public function byPembimbingLapanganBuilder(int $pembimbingLapanganId, string $status = 'aktif')
    {
        $builder = $this->withRelasi()->where('penempatan_pkl.pembimbing_lapangan_id', $pembimbingLapanganId);

        if ($status !== '') {
            $builder->where('penempatan_pkl.status', $status);
        }

        return $builder->orderBy('us.nama', 'ASC');
    }

    /**
     * Kondisi pencarian bebas untuk daftar penempatan: nama siswa, NIS, kelas,
     * nama perusahaan, dan nama guru pembimbing.
     */
    public function cari(string $keyword)
    {
        if ($keyword === '') {
            return $this;
        }

        return $this->groupStart()
            ->like('us.nama', $keyword)
            ->orLike('siswa.nis', $keyword)
            ->orLike('siswa.kelas', $keyword)
            ->orLike('tempat_pkl.nama_perusahaan', $keyword)
            ->orLike('ug.nama', $keyword)
            ->groupEnd();
    }
}
