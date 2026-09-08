<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama', 'email', 'username', 'password', 'role', 'foto', 'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|max_length[150]|is_unique[users.email,id,{id}]',
        'username' => 'required|regex_match[/^[a-zA-Z0-9_.-]+$/]|min_length[4]|max_length[50]|is_unique[users.username,id,{id}]',
        'role'     => 'required|in_list[siswa,guru_pembimbing,pembimbing_lapangan,admin]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar pada akun lain.',
        ],
        'username' => [
            'is_unique'    => 'Username sudah dipakai akun lain.',
            'regex_match'  => 'Username hanya boleh huruf, angka, titik, garis bawah, dan strip.',
        ],
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password hanya bila diisi. Update tanpa field password dibiarkan apa adanya.
     */
    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        if ($data['data']['password'] === '' || $data['data']['password'] === null) {
            unset($data['data']['password']);

            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

        return $data;
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }
}
