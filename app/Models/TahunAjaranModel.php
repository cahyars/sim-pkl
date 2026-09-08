<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table         = 'tahun_ajaran';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nama_tahun_ajaran', 'semester', 'tanggal_mulai', 'tanggal_selesai', 'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'nama_tahun_ajaran' => 'required|max_length[20]',
        'semester'          => 'required|in_list[ganjil,genap]',
    ];

    public function getAktif(): ?array
    {
        return $this->where('is_active', 1)->first();
    }

    /**
     * Hanya satu tahun ajaran boleh aktif — nonaktifkan sisanya.
     */
    public function setAktif(int $id): void
    {
        $this->db->table($this->table)->set('is_active', 0)->update();
        $this->db->table($this->table)->where('id', $id)->update(['is_active' => 1]);
    }
}
