<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table         = 'pengaturan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kunci', 'nilai', 'keterangan'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    /** @var array<string, string>|null */
    private static ?array $cache = null;

    public function ambil(string $kunci, $default = null)
    {
        if (self::$cache === null) {
            self::$cache = [];

            foreach ($this->findAll() as $row) {
                self::$cache[$row['kunci']] = $row['nilai'];
            }
        }

        return self::$cache[$kunci] ?? $default;
    }

    public function simpan(string $kunci, $nilai, ?string $keterangan = null): void
    {
        $existing = $this->where('kunci', $kunci)->first();

        if ($existing) {
            $this->update($existing['id'], ['nilai' => (string) $nilai]);
        } else {
            $this->insert([
                'kunci'      => $kunci,
                'nilai'      => (string) $nilai,
                'keterangan' => $keterangan,
            ]);
        }

        self::$cache = null;
    }

    public function semua(): array
    {
        return $this->orderBy('id', 'ASC')->findAll();
    }
}
