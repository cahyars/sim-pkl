<?php

namespace App\Models;

use CodeIgniter\Model;

class AspekPenilaianModel extends Model
{
    protected $table         = 'aspek_penilaian';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_aspek', 'deskripsi', 'bobot', 'is_active', 'jurusan', 'urutan'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $validationRules = [
        'nama_aspek' => 'required|max_length[255]',
        'bobot'      => 'required|is_natural|greater_than[0]|less_than_equal_to[100]',
        'jurusan'    => 'permit_empty|max_length[100]',
        'urutan'     => 'permit_empty|is_natural',
    ];

    protected $validationMessages = [
        'bobot' => [
            'greater_than'       => 'Bobot harus lebih besar dari 0.',
            'less_than_equal_to' => 'Bobot maksimal 100.',
        ],
    ];

    public function getAktif(): array
    {
        return $this->where('is_active', 1)->orderBy('urutan', 'ASC')->orderBy('id', 'ASC')->findAll();
    }

    /**
     * Ambil aspek penilaian aktif spesifik untuk suatu jurusan (misal: "Akuntansi Keuangan",
     * "Teknik Komputer & Jaringan"). Jika belum ada aspek khusus jurusan tsb, fallback ke
     * jurusan 'Umum' atau semua aspek aktif.
     */
    public function getByJurusan(string $jurusan): array
    {
        $jurusan = trim($jurusan);

        // 1. Coba pencocokan tepat nama jurusan
        $aspek = $this->where('is_active', 1)
            ->where('jurusan', $jurusan)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if ($aspek !== []) {
            return $aspek;
        }

        // 2. Coba pencocokan parsial kata kunci (misal "Akuntansi" atau "TKJ" / "Komputer")
        $kataKunci = explode(' ', $jurusan)[0] ?? '';
        if ($kataKunci !== '') {
            $aspek = $this->where('is_active', 1)
                ->like('jurusan', $kataKunci)
                ->orderBy('urutan', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();

            if ($aspek !== []) {
                return $aspek;
            }
        }

        // 3. Fallback ke aspek bertanda "Umum"
        $aspekUmum = $this->where('is_active', 1)
            ->where('jurusan', 'Umum')
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if ($aspekUmum !== []) {
            return $aspekUmum;
        }

        // 4. Terakhir, fallback ke semua aspek aktif
        return $this->getAktif();
    }

    /**
     * Hitung total bobot aktif, di-scope per jurusan jika dispesifikasi.
     */
    public function totalBobotAktif(?string $jurusan = null, ?int $kecualiId = null): int
    {
        $builder = $this->select('SUM(bobot) AS total')->where('is_active', 1);

        if ($jurusan !== null && $jurusan !== '') {
            $builder->where('jurusan', $jurusan);
        }

        if ($kecualiId !== null) {
            $builder->where('id !=', $kecualiId);
        }

        return (int) ($builder->first()['total'] ?? 0);
    }

    /**
     * Daftar distinct jurusan yang ada di tabel aspek penilaian dan siswa.
     */
    public function daftarJurusanTersedia(): array
    {
        $dariAspek = array_column(
            $this->distinct()->select('jurusan')->where('jurusan !=', '')->orderBy('jurusan', 'ASC')->findAll(),
            'jurusan'
        );

        $dariSiswa = (new SiswaModel())->daftarJurusan();

        $semua = array_unique(array_merge(['Umum', 'Akuntansi Keuangan', 'Teknik Komputer & Jaringan', 'Rekayasa Perangkat Lunak', 'Teknik Kendaraan Ringan'], $dariAspek, $dariSiswa));
        sort($semua);

        return $semua;
    }
}

