<?php

namespace App\Models;

use CodeIgniter\Model;

class PembimbingLapanganModel extends Model
{
    protected $table         = 'pembimbing_lapangan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'tempat_pkl_id', 'nama', 'jabatan', 'no_hp', 'email'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'tempat_pkl_id' => 'required|is_natural_no_zero',
        'nama'          => 'required|max_length[100]',
        'email'         => 'permit_empty|valid_email|max_length[150]',
    ];

    public function withTempat()
    {
        return $this->select('pembimbing_lapangan.*, tempat_pkl.nama_perusahaan')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_lapangan.tempat_pkl_id');
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->withTempat()->where('pembimbing_lapangan.user_id', $userId)->first();
    }

    public function byTempat(int $tempatPklId): array
    {
        return $this->byTempatBuilder($tempatPklId)->findAll();
    }

    /**
     * Versi builder dari byTempat() supaya bisa dipaginate & ditambah pencarian.
     */
    public function byTempatBuilder(int $tempatPklId)
    {
        return $this->select('pembimbing_lapangan.*, users.is_active AS akun_aktif')
            ->join('users', 'users.id = pembimbing_lapangan.user_id', 'left')
            ->where('tempat_pkl_id', $tempatPklId)
            ->orderBy('nama', 'ASC');
    }
}
