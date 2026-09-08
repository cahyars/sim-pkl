<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table         = 'notifikasi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'judul', 'pesan', 'jenis', 'link', 'is_read', 'is_emailed', 'created_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'user_id' => 'required|is_natural_no_zero',
        'judul'   => 'required|max_length[150]',
        'pesan'   => 'required',
        'jenis'   => 'permit_empty|in_list[reminder_logbook,validasi,penilaian,umum]',
    ];

    public function kirim(int $userId, string $judul, string $pesan, string $jenis = 'umum', ?string $link = null, bool $isEmailed = false): int
    {
        return (int) $this->insert([
            'user_id'    => $userId,
            'judul'      => $judul,
            'pesan'      => $pesan,
            'jenis'      => $jenis,
            'link'       => $link,
            'is_emailed' => $isEmailed,
        ], true);
    }

    public function belumDibaca(int $userId): int
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function terbaru(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    public function tandaiDibaca(int $id, int $userId): bool
    {
        return $this->where('id', $id)->where('user_id', $userId)->set(['is_read' => 1])->update();
    }

    public function tandaiSemuaDibaca(int $userId): bool
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->set(['is_read' => 1])->update();
    }

    /**
     * Guard anti-kirim-ganda untuk cron reminder (Fitur §4.5).
     */
    public function sudahDikirimHariIni(int $userId, string $jenis, string $tanggal): bool
    {
        return $this->where('user_id', $userId)
            ->where('jenis', $jenis)
            ->where('DATE(created_at)', $tanggal)
            ->countAllResults() > 0;
    }
}
