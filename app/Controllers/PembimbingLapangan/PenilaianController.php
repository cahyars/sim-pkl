<?php

namespace App\Controllers\PembimbingLapangan;

use App\Controllers\BaseController;
use App\Libraries\PdfExport;
use App\Models\AspekPenilaianModel;
use App\Models\DetailPenilaianModel;
use App\Models\GuruPembimbingModel;
use App\Models\NotifikasiModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;
use App\Models\PenilaianModel;
use App\Models\SiswaModel;
use App\Models\TahunAjaranModel;

class PenilaianController extends BaseController
{
    public function form(int $siswaId)
    {
        $penempatan = $this->ambilBimbingan($siswaId);

        if ($penempatan === null) {
            return redirect()->to('/pembimbing-lapangan/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        // Ambil aspek penilaian dinamis sesuai jurusan siswa
        $aspekModel = new AspekPenilaianModel();
        $aspek      = $aspekModel->getByJurusan($penempatan['jurusan'] ?? '');

        $penilaian  = (new PenilaianModel())->findBySiswa($siswaId);
        $detail     = $penilaian ? (new DetailPenilaianModel())->byPenilaian($penilaian['id']) : [];

        $nilaiByAspek     = [];
        $deskripsiByAspek = [];
        foreach ($detail as $d) {
            $nilaiByAspek[$d['aspek_penilaian_id']]     = $d['nilai'];
            $deskripsiByAspek[$d['aspek_penilaian_id']] = $d['deskripsi'] ?? '';
        }

        // Rekapitulasi kehadiran dari logbook
        $kehadiranLogbook = PenilaianModel::hitungKehadiranLogbook($siswaId, $penempatan);
        $kehadiran = [
            'sakit'            => $penilaian ? (int) $penilaian['sakit'] : $kehadiranLogbook['sakit'],
            'izin'             => $penilaian ? (int) $penilaian['izin'] : $kehadiranLogbook['izin'],
            'tanpa_keterangan' => $penilaian ? (int) $penilaian['tanpa_keterangan'] : $kehadiranLogbook['tanpa_keterangan'],
        ];

        return view('pembimbing_lapangan/penilaian/form', [
            'title'            => 'Penilaian Akhir - ' . $penempatan['nama_siswa'],
            'siswa'            => $penempatan,
            'aspek'            => $aspek,
            'penilaian'        => $penilaian,
            'nilaiByAspek'     => $nilaiByAspek,
            'deskripsiByAspek' => $deskripsiByAspek,
            'kehadiran'        => $kehadiran,
            'terkunci'         => $penilaian !== null && $penilaian['status'] === 'final',
        ]);
    }

    public function store(int $siswaId)
    {
        $penempatan = $this->ambilBimbingan($siswaId);

        if ($penempatan === null) {
            return redirect()->to('/pembimbing-lapangan/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $pembimbingId   = (int) $this->session->get('profile_id');
        $penilaianModel = new PenilaianModel();
        $existing       = $penilaianModel->findBySiswa($siswaId);

        if ($existing !== null && $existing['status'] === 'final') {
            return redirect()->to('/pembimbing-lapangan/siswa/' . $siswaId)
                ->with('error', 'Penilaian sudah final dan tidak dapat diubah lagi.');
        }

        // Aspek dinamis per jurusan siswa
        $aspekAktif = (new AspekPenilaianModel())->getByJurusan($penempatan['jurusan'] ?? '');

        $rules    = [
            'sakit'            => 'permit_empty|is_natural',
            'izin'             => 'permit_empty|is_natural',
            'tanpa_keterangan' => 'permit_empty|is_natural',
            'pimpinan_nama'    => 'permit_empty|max_length[150]',
            'pimpinan_nip'     => 'permit_empty|max_length[50]',
            'instruktur_nama'  => 'permit_empty|max_length[150]',
            'instruktur_nip'   => 'permit_empty|max_length[50]',
        ];
        $messages = [];

        foreach ($aspekAktif as $a) {
            $field            = 'nilai_' . $a['id'];
            $rules[$field]    = 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]';
            $messages[$field] = [
                'required'               => "Skor {$a['nama_aspek']} wajib diisi.",
                'numeric'                => "Skor {$a['nama_aspek']} harus berupa angka.",
                'greater_than_equal_to'  => "Skor {$a['nama_aspek']} minimal 0.",
                'less_than_equal_to'     => "Skor {$a['nama_aspek']} maksimal 100.",
            ];
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/pembimbing-lapangan/penilaian/' . $siswaId)
                ->withInput()->with('errors', $this->validator->getErrors());
        }

        $aksi = $this->request->getPost('aksi') === 'final' ? 'final' : 'draft';

        $komponen = [];
        foreach ($aspekAktif as $a) {
            $komponen[] = [
                'nilai' => (float) $this->request->getPost('nilai_' . $a['id']),
                'bobot' => (int) ($a['bobot'] ?? 1),
            ];
        }
        $nilaiAkhir = PenilaianModel::hitungNilaiAkhir($komponen);

        $data = [
            'siswa_id'               => $siswaId,
            'pembimbing_lapangan_id' => $pembimbingId,
            'nilai_akhir'            => $nilaiAkhir,
            'feedback'               => $this->request->getPost('feedback'),
            'sakit'                  => (int) $this->request->getPost('sakit'),
            'izin'                   => (int) $this->request->getPost('izin'),
            'tanpa_keterangan'       => (int) $this->request->getPost('tanpa_keterangan'),
            'pimpinan_nama'          => $this->request->getPost('pimpinan_nama') ?: null,
            'pimpinan_nip'           => $this->request->getPost('pimpinan_nip') ?: null,
            'instruktur_nama'        => $this->request->getPost('instruktur_nama') ?: $penempatan['nama_pembimbing_lapangan'] ?: null,
            'instruktur_nip'         => $this->request->getPost('instruktur_nip') ?: null,
            'status'                 => $aksi,
            'tanggal_penilaian'      => date('Y-m-d'),
        ];

        if ($aksi === 'final') {
            $data['finalized_at'] = date('Y-m-d H:i:s');
        }

        $db = db_connect();
        $db->transStart();

        if ($existing !== null) {
            $penilaianModel->update($existing['id'], $data);
            $penilaianId = $existing['id'];
        } else {
            $penilaianId = $penilaianModel->insert($data, true);
        }

        $detailModel = new DetailPenilaianModel();
        $detailModel->where('penilaian_id', $penilaianId)->delete();

        foreach ($aspekAktif as $a) {
            $detailModel->insert([
                'penilaian_id'       => $penilaianId,
                'aspek_penilaian_id' => $a['id'],
                'nilai'              => (float) $this->request->getPost('nilai_' . $a['id']),
                'deskripsi'          => $this->request->getPost('deskripsi_' . $a['id']) ?: null,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/pembimbing-lapangan/penilaian/' . $siswaId)
                ->withInput()->with('error', 'Gagal menyimpan penilaian. Silakan coba lagi.');
        }

        if ($aksi === 'final') {
            $this->kirimNotifikasiFinalisasi($penempatan, $nilaiAkhir);
        }

        $pesan = $aksi === 'final'
            ? 'Penilaian berhasil difinalisasi. Lembar nilai resmi kini dapat diunduh (PDF).'
            : 'Penilaian berhasil disimpan sebagai draft.';

        return redirect()->to('/pembimbing-lapangan/penilaian/' . $siswaId)->with('success', $pesan);
    }

    /**
     * Unduh lembar nilai resmi (PDF 2 halaman) sesuai format dokumen SMKN 1 Subang.
     */
    public function pdf(int $siswaId)
    {
        $penempatan = $this->ambilBimbingan($siswaId);

        if ($penempatan === null) {
            return redirect()->to('/pembimbing-lapangan/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $penilaian = (new PenilaianModel())->findBySiswa($siswaId);
        if ($penilaian === null) {
            return redirect()->to('/pembimbing-lapangan/penilaian/' . $siswaId)->with('error', 'Nilai belum diisi.');
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

        // Penentuan Program Keahlian & Konsentrasi Keahlian
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

    private function ambilBimbingan(int $siswaId): ?array
    {
        $pembimbingId = (int) $this->session->get('profile_id');

        return (new PenempatanPklModel())->withRelasi()
            ->where('penempatan_pkl.siswa_id', $siswaId)
            ->where('penempatan_pkl.pembimbing_lapangan_id', $pembimbingId)
            ->orderBy('penempatan_pkl.tanggal_mulai', 'DESC')
            ->first();
    }

    private function kirimNotifikasiFinalisasi(array $penempatan, float $nilaiAkhir): void
    {
        $notifModel = new NotifikasiModel();
        $pesanSiswa = 'Penilaian akhir PKL kamu telah difinalisasi dengan nilai ' . number_format($nilaiAkhir, 2) . ' (' . PenilaianModel::predikat($nilaiAkhir) . ').';

        $siswa = (new SiswaModel())->find($penempatan['siswa_id']);
        if ($siswa !== null) {
            $notifModel->kirim((int) $siswa['user_id'], 'Penilaian Akhir Selesai', $pesanSiswa, 'penilaian', '/siswa/nilai');
        }

        $guru = (new GuruPembimbingModel())->find($penempatan['guru_pembimbing_id']);
        if ($guru !== null) {
            $pesanGuru = 'Penilaian akhir untuk ' . $penempatan['nama_siswa'] . ' telah difinalisasi oleh pembimbing lapangan dengan nilai ' . number_format($nilaiAkhir, 2) . '.';
            $notifModel->kirim((int) $guru['user_id'], 'Penilaian Akhir Selesai', $pesanGuru, 'penilaian', '/guru/siswa/' . $penempatan['siswa_id']);
        }
    }
}
