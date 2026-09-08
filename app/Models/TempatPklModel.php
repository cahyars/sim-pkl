<?php

namespace App\Models;

use CodeIgniter\Model;

class TempatPklModel extends Model
{
    protected $table         = 'tempat_pkl';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nama_perusahaan', 'alamat', 'no_telp', 'email', 'bidang_usaha',
        'penanggung_jawab', 'kuota', 'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'nama_perusahaan' => 'required|max_length[150]',
        'alamat'          => 'required',
        'kuota'           => 'permit_empty|is_natural',
        'email'           => 'permit_empty|valid_email|max_length[150]',
    ];

    /**
     * Jumlah siswa yang sedang PKL aktif di tempat ini.
     */
    public function terisi(int $tempatPklId): int
    {
        return $this->db->table('penempatan_pkl')
            ->where('tempat_pkl_id', $tempatPklId)
            ->where('status', 'aktif')
            ->countAllResults();
    }

    /**
     * Daftar tempat PKL beserta kuota terpakai — dipakai di halaman penempatan.
     */
    public function withKuotaTerpakai(): array
    {
        return $this->withKuotaTerpakaiBuilder()->findAll();
    }

    /**
     * Versi builder: kuota terpakai dihitung lewat derived table (satu query)
     * sehingga halaman daftar tidak perlu memanggil terisi() per baris.
     */
    public function withKuotaTerpakaiBuilder()
    {
        return $this->select('tempat_pkl.*, COALESCE(p.terisi, 0) AS terisi')
            ->join(
                '(SELECT tempat_pkl_id, COUNT(*) AS terisi FROM penempatan_pkl WHERE status = "aktif" GROUP BY tempat_pkl_id) p',
                'p.tempat_pkl_id = tempat_pkl.id',
                'left',
                false
            )
            ->orderBy('tempat_pkl.nama_perusahaan', 'ASC');
    }
}
