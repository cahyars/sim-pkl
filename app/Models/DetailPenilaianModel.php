<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPenilaianModel extends Model
{
    protected $table         = 'detail_penilaian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['penilaian_id', 'aspek_penilaian_id', 'nilai', 'deskripsi'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'penilaian_id'       => 'required|is_natural_no_zero',
        'aspek_penilaian_id' => 'required|is_natural_no_zero',
        'nilai'              => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'deskripsi'          => 'permit_empty',
    ];

    protected $validationMessages = [
        'nilai' => [
            'greater_than_equal_to' => 'Nilai minimal 0.',
            'less_than_equal_to'    => 'Nilai maksimal 100.',
        ],
    ];

    /**
     * Detail nilai + metadata aspeknya, untuk form & cetak penilaian.
     */
    public function byPenilaian(int $penilaianId): array
    {
        return $this->select('detail_penilaian.*, aspek_penilaian.nama_aspek, aspek_penilaian.bobot, aspek_penilaian.jurusan, aspek_penilaian.urutan, aspek_penilaian.deskripsi AS deskripsi_aspek')
            ->join('aspek_penilaian', 'aspek_penilaian.id = detail_penilaian.aspek_penilaian_id')
            ->where('detail_penilaian.penilaian_id', $penilaianId)
            ->orderBy('aspek_penilaian.urutan', 'ASC')
            ->orderBy('aspek_penilaian.id', 'ASC')
            ->findAll();
    }
}

