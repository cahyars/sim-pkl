<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Libraries\PdfExport;
use App\Models\AspekPenilaianModel;
use App\Models\DetailPenilaianModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;
use App\Models\PenilaianModel;
use App\Models\TahunAjaranModel;

class NilaiController extends BaseController
{
    public function index()
    {
        $siswaId   = (int) $this->session->get('profile_id');
        $penilaian = (new PenilaianModel())->findBySiswa($siswaId);

        // Nilai draft belum boleh dilihat siswa — hanya tampil setelah difinalisasi (Fitur §4.4).
        $sudahFinal = $penilaian !== null && $penilaian['status'] === 'final';

        return view('siswa/nilai/index', [
            'title'     => 'Nilai Akhir',
            'penilaian' => $sudahFinal ? $penilaian : null,
            'detail'    => $sudahFinal ? (new DetailPenilaianModel())->byPenilaian($penilaian['id']) : [],
        ]);
    }

    public function pdf()
    {
        $siswaId   = (int) $this->session->get('profile_id');
        $penilaian = (new PenilaianModel())->findBySiswa($siswaId);

        if ($penilaian === null || $penilaian['status'] !== 'final') {
            return redirect()->to('/siswa/nilai')->with('error', 'Nilai akhir belum tersedia atau belum difinalisasi.');
        }

        $penempatan = (new PenempatanPklModel())->withRelasi()
            ->where('penempatan_pkl.siswa_id', $siswaId)
            ->orderBy('penempatan_pkl.tanggal_mulai', 'DESC')
            ->first();

        if ($penempatan === null) {
            return redirect()->to('/siswa/nilai')->with('error', 'Data penempatan tidak ditemukan.');
        }

        $aspekModel = new AspekPenilaianModel();
        $aspek      = $aspekModel->getByJurusan($penempatan['jurusan'] ?? '');
        $detail     = (new DetailPenilaianModel())->byPenilaian($penilaian['id']);

        $nilaiByAspek     = [];
        $deskripsiByAspek = [];
        foreach ($detail as $d) {
            $nilaiByAspek[$d['aspek_penilaian_id']]     = $d['nilai'];
            $deskripsiByAspek[$d['aspek_penilaian_id']] = $d['deskripsi'] ?? '';
        }

        $pengaturan = new PengaturanModel();
        $sekolah = [
            'nama'   => $pengaturan->ambil('nama_sekolah', 'SMK NEGERI 1 SUBANG'),
            'alamat' => $pengaturan->ambil('alamat_sekolah', 'Jl. Arif Rahman Hakim No. 35, Subang'),
            'logo'   => FCPATH . 'assets/img/logo-sekolah.png',
        ];

        $tahunAktif  = (new TahunAjaranModel())->getAktif();
        $tahunAjaran = $tahunAktif ? $tahunAktif['nama_tahun_ajaran'] : '2025/2026';

        [$programKeahlian, $konsentrasiKeahlian] = $this->uraiJurusan($penempatan['jurusan'] ?? '');

        return (new PdfExport())->unduh('pembimbing_lapangan/penilaian/pdf_lembar_nilai', [
            'siswa'               => $penempatan,
            'penilaian'           => $penilaian,
            'aspek'               => $aspek,
            'nilaiByAspek'        => $nilaiByAspek,
            'deskripsiByAspek'    => $deskripsiByAspek,
            'sekolah'             => $sekolah,
            'tahunAjaran'         => $tahunAjaran,
            'programKeahlian'     => $programKeahlian,
            'konsentrasiKeahlian' => $konsentrasiKeahlian,
        ], 'daftar-nilai-' . $penempatan['nis'] . '.pdf', 'portrait');
    }

    private function uraiJurusan(string $jurusan): array
    {
        $j = strtolower($jurusan);

        if (str_contains($j, 'akuntansi')) {
            return ['Akuntansi dan Keuangan Lembaga', 'Akuntansi'];
        }
        if (str_contains($j, 'komputer') || str_contains($j, 'jaringan') || str_contains($j, 'tkj')) {
            return ['Teknik Jaringan Komputer dan Telekomunikasi', 'Teknik Komputer dan Jaringan'];
        }
        if (str_contains($j, 'perangkat lunak') || str_contains($j, 'rpl')) {
            return ['Pengembangan Perangkat Lunak dan Gim', 'Rekayasa Perangkat Lunak'];
        }
        if (str_contains($j, 'kendaraan') || str_contains($j, 'tkr') || str_contains($j, 'tsm')) {
            return ['Teknik Otomotif', 'Teknik Kendaraan Ringan'];
        }

        return [$jurusan, $jurusan];
    }
}
