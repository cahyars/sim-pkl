<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table         = 'siswa';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id', 'nis', 'nisn', 'kelas', 'jurusan', 'no_hp', 'alamat',
        'tahun_ajaran_id', 'status_pkl',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'nis'     => 'required|max_length[20]|is_unique[siswa.nis,id,{id}]',
        'kelas'   => 'required|max_length[20]',
        'jurusan' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'nis' => ['is_unique' => 'NIS sudah terdaftar pada siswa lain.'],
    ];

    /**
     * Query builder siswa lengkap dengan data user-nya.
     */
    public function withUser()
    {
        return $this->select('siswa.*, users.nama, users.email, users.username, users.is_active, users.foto')
            ->join('users', 'users.id = siswa.user_id');
    }

    public function getDetail(int $id): ?array
    {
        return $this->withUser()
            ->select('tahun_ajaran.nama_tahun_ajaran, tahun_ajaran.semester')
            ->join('tahun_ajaran', 'tahun_ajaran.id = siswa.tahun_ajaran_id', 'left')
            ->where('siswa.id', $id)
            ->first();
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->withUser()->where('siswa.user_id', $userId)->first();
    }

    /**
     * Siswa yang belum punya penempatan aktif — dipakai dropdown assign penempatan.
     */
    public function tanpaPenempatanAktif(): array
    {
        return $this->withUser()
            ->where('siswa.id NOT IN (SELECT siswa_id FROM penempatan_pkl WHERE status = "aktif")', null, false)
            ->orderBy('users.nama', 'ASC')
            ->findAll();
    }

    /**
     * Daftar jurusan aktif dari master jurusan atau fallback distinct tabel siswa.
     */
    public function daftarJurusan(): array
    {
        $jurusanMaster = (new JurusanModel())->getAktif();
        if (! empty($jurusanMaster)) {
            return array_column($jurusanMaster, 'nama');
        }

        return array_column(
            $this->distinct()->select('jurusan')->orderBy('jurusan', 'ASC')->findAll(),
            'jurusan'
        );
    }

    /**
     * Daftar distinct kelas yang sudah pernah dipakai — untuk saran input & filter.
     */
    public function daftarKelas(): array
    {
        return array_column(
            $this->distinct()->select('kelas')->orderBy('kelas', 'ASC')->findAll(),
            'kelas'
        );
    }
}
