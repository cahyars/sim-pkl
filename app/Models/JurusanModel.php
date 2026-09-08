<?php

namespace App\Models;

use CodeIgniter\Model;

class JurusanModel extends Model
{
    protected $table         = 'jurusan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'kode',
        'nama',
        'bidang_keahlian',
        'program_keahlian',
        'konsentrasi_keahlian',
        'kepala_program',
        'nip_kepala_program',
        'deskripsi',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'kode'                 => 'required|min_length[2]|max_length[20]|is_unique[jurusan.kode,id,{id}]',
        'nama'                 => 'required|min_length[3]|max_length[100]',
        'program_keahlian'     => 'permit_empty|max_length[150]',
        'konsentrasi_keahlian' => 'permit_empty|max_length[150]',
        'kepala_program'       => 'permit_empty|max_length[100]',
        'nip_kepala_program'   => 'permit_empty|max_length[50]',
        'is_active'            => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'kode' => [
            'required'  => 'Kode jurusan wajib diisi.',
            'is_unique' => 'Kode jurusan sudah terdaftar.',
        ],
        'nama' => [
            'required' => 'Nama jurusan wajib diisi.',
        ],
    ];

    /**
     * Ambil seluruh jurusan aktif.
     */
    public function getAktif(): array
    {
        return $this->where('is_active', 1)->orderBy('nama', 'ASC')->findAll();
    }

    /**
     * Ambil data jurusan lengkap dengan agregasi jumlah siswa & aspek penilaian (TP).
     */
    public function withStatistik(?string $cari = null): array
    {
        $builder = $this->orderBy('is_active', 'DESC')->orderBy('nama', 'ASC');

        if (! empty($cari)) {
            $builder->groupStart()
                ->like('kode', $cari)
                ->orLike('nama', $cari)
                ->orLike('program_keahlian', $cari)
                ->orLike('konsentrasi_keahlian', $cari)
                ->orLike('kepala_program', $cari)
                ->groupEnd();
        }

        $daftar = $builder->findAll();
        $db     = \Config\Database::connect();

        // Hitung siswa per jurusan
        $siswaCounts = [];
        $rowsSiswa = $db->table('siswa')->select('jurusan, COUNT(id) as total')->groupBy('jurusan')->get()->getResultArray();
        foreach ($rowsSiswa as $r) {
            $siswaCounts[$r['jurusan']] = (int) $r['total'];
        }

        // Hitung aspek TP per jurusan
        $aspekCounts = [];
        $rowsAspek = $db->table('aspek_penilaian')->select('jurusan, COUNT(id) as total')->groupBy('jurusan')->get()->getResultArray();
        foreach ($rowsAspek as $r) {
            $aspekCounts[$r['jurusan']] = (int) $r['total'];
        }

        foreach ($daftar as &$j) {
            $nama = $j['nama'];
            $j['total_siswa'] = $siswaCounts[$nama] ?? 0;
            $j['total_aspek'] = $aspekCounts[$nama] ?? 0;
        }
        unset($j);

        return $daftar;
    }

    /**
     * Daftar nama jurusan untuk opsi select/dropdown.
     */
    public function daftarOpsi(): array
    {
        $aktif = $this->getAktif();
        $opsi = [];
        foreach ($aktif as $row) {
            $opsi[$row['nama']] = $row['nama'] . ' (' . $row['kode'] . ')';
        }
        return $opsi;
    }
}
