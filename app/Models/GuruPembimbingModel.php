<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruPembimbingModel extends Model
{
    protected $table         = 'guru_pembimbing';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'nip', 'no_hp', 'jurusan_ampu'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
    ];

    public function withUser()
    {
        return $this->select('guru_pembimbing.*, users.nama, users.email, users.username, users.is_active, users.foto')
            ->join('users', 'users.id = guru_pembimbing.user_id');
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->withUser()->where('guru_pembimbing.user_id', $userId)->first();
    }
}
