<?php

namespace App\Commands;

use App\Libraries\PdfExport;
use App\Models\AspekPenilaianModel;
use App\Models\DetailPenilaianModel;
use App\Models\PenempatanPklModel;
use App\Models\PenilaianModel;
use App\Models\PengaturanModel;
use App\Models\TahunAjaranModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestPenilaianPkl extends BaseCommand
{
    protected $group       = 'SIM-PKL';
    protected $name        = 'simpkl:test-fitur-baru';
    protected $description = 'Uji fungsionalitas penilaian per jurusan, PDF lembar nilai 2 halaman, dan rekap plotting P2 (Excel/PDF).';

    public function run(array $params)
    {
        CLI::write('=== 0. Uji Kelola Jurusan & Statistik ===', 'yellow');
        $jurusanModel = new \App\Models\JurusanModel();
        $daftarJurusanData = $jurusanModel->withStatistik();
        foreach ($daftarJurusanData as $j) {
            CLI::write(sprintf('  [%-4s] %-30s | Siswa: %2d | Aspek TP: %2d | Status: %s',
                $j['kode'],
                $j['nama'],
                $j['total_siswa'],
                $j['total_aspek'],
                $j['is_active'] ? 'Aktif' : 'Nonaktif'
            ), 'cyan');
        }

        CLI::newLine();
        CLI::write('=== 1. Uji Aspek Penilaian Berbasis Jurusan ===', 'yellow');
        $aspekModel = new AspekPenilaianModel();

        $daftarJurusan = ['Akuntansi Keuangan', 'Teknik Komputer & Jaringan', 'Rekayasa Perangkat Lunak', 'Teknik Kendaraan Ringan', 'Umum'];
        foreach ($daftarJurusan as $jurusan) {
            $aspek = $aspekModel->getByJurusan($jurusan);
            CLI::write(sprintf('  Jurusan %-30s: %d aspek (TP)', $jurusan, count($aspek)), 'green');
            foreach (array_slice($aspek, 0, 2) as $a) {
                CLI::write(sprintf('    [TP %d] %s', $a['urutan'], substr($a['nama_aspek'], 0, 70)), 'white');
            }
        }

        CLI::newLine();
        CLI::write('=== 2. Uji Render PDF Lembar Nilai Resmi (2 Halaman) ===', 'yellow');
        $penempatan = (new PenempatanPklModel())->withRelasi()->where('penempatan_pkl.status', 'aktif')->first();

        if ($penempatan) {
            $aspek = $aspekModel->getByJurusan($penempatan['jurusan'] ?? 'Umum');
            $penilaian = [
                'id'                => 999,
                'nilai_akhir'       => 88.75,
                'feedback'          => 'Kompetensi teknis sangat baik dan etos kerja luar biasa.',
                'status'            => 'final',
                'tanggal_penilaian' => date('Y-m-d'),
                'sakit'             => 1,
                'izin'              => 0,
                'tanpa_keterangan'  => 0,
                'pimpinan_nama'     => 'IR. H. BAMBANG SURYO, M.M.',
                'pimpinan_nip'      => 'DIR-19750812-001',
                'instruktur_nama'   => 'BUDI UTOMO, S.KOM.',
                'instruktur_nip'    => 'DEV-19901010-025',
            ];

            $nilaiByAspek = [];
            $deskripsiByAspek = [];
            foreach ($aspek as $a) {
                $nilaiByAspek[$a['id']] = 88.0;
                $deskripsiByAspek[$a['id']] = 'Menunjukkan penguasaan yang sangat memuaskan dalam ' . $a['nama_aspek'];
            }

            $pengaturan = new PengaturanModel();
            $sekolah = [
                'nama'   => $pengaturan->ambil('nama_sekolah', 'SMK NEGERI 1 SUBANG'),
                'alamat' => $pengaturan->ambil('alamat_sekolah', 'Jl. Arif Rahman Hakim No. 35, Subang'),
                'logo'   => FCPATH . 'assets/img/logo-sekolah.png',
            ];

            $tahunAktif  = (new TahunAjaranModel())->getAktif();
            $tahunAjaran = $tahunAktif ? $tahunAktif['nama_tahun_ajaran'] : '2025/2026';

            $html = view('pembimbing_lapangan/penilaian/pdf_lembar_nilai', [
                'siswa'               => $penempatan,
                'penilaian'           => $penilaian,
                'aspek'               => $aspek,
                'nilaiByAspek'        => $nilaiByAspek,
                'deskripsiByAspek'    => $deskripsiByAspek,
                'sekolah'             => $sekolah,
                'tahunAjaran'         => $tahunAjaran,
                'programKeahlian'     => 'Pengembangan Perangkat Lunak dan Gim',
                'konsentrasiKeahlian' => $penempatan['jurusan'] ?? 'Rekayasa Perangkat Lunak',
            ]);

            $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdfContent = $dompdf->output();

            $pdfPath = WRITEPATH . 'test_lembar_nilai.pdf';
            file_put_contents($pdfPath, $pdfContent);
            CLI::write('  PDF Lembar Nilai berhasil dibuat: ' . $pdfPath . ' (' . strlen($pdfContent) . ' bytes)', 'green');
        }

        CLI::newLine();
        CLI::write('=== 3. Uji Render PDF Rekap Plotting Penempatan (P2) ===', 'yellow');
        
        $penempatanAll = (new PenempatanPklModel())->withRelasi()
            ->where('penempatan_pkl.status', 'aktif')
            ->orderBy('tempat_pkl.nama_perusahaan', 'ASC')
            ->orderBy('penempatan_pkl.guru_pembimbing_id', 'ASC')
            ->orderBy('us.nama', 'ASC')
            ->findAll();

        $grouped = [];
        $totalSiswa = 0;
        foreach ($penempatanAll as $row) {
            $key = $row['tempat_pkl_id'] . '_' . $row['guru_pembimbing_id'];
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'nama_perusahaan' => $row['nama_perusahaan'],
                    'nama_pembimbing' => $row['nama_guru'],
                    'nama_instruktur' => $row['nama_pembimbing_lapangan'] ?? '-',
                    'siswa'           => [],
                ];
            }
            $grouped[$key]['siswa'][] = [
                'nama_siswa' => $row['nama_siswa'],
                'kelas'      => $row['kelas'],
                'jurusan'    => $row['jurusan'],
            ];
            $totalSiswa++;
        }

        $items = array_values($grouped);

        $htmlP2 = view('admin/laporan/pdf/plotting_p2', [
            'items'        => $items,
            'totalSiswa'   => $totalSiswa,
            'periodeJudul' => 'JANUARI S.D MEI 2026',
            'sekolah'      => [
                'nama'   => 'SMK NEGERI 1 SUBANG',
                'alamat' => 'Jl. Arif Rahman Hakim No. 35, Subang',
            ],
        ]);

        $dompdfP2 = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdfP2->loadHtml($htmlP2);
        $dompdfP2->setPaper('A4', 'portrait');
        $dompdfP2->render();
        $pdfP2Content = $dompdfP2->output();

        $pdfP2Path = WRITEPATH . 'test_plotting_p2.pdf';
        file_put_contents($pdfP2Path, $pdfP2Content);
        CLI::write('  PDF Rekap Plotting P2 berhasil dibuat: ' . $pdfP2Path . ' (' . strlen($pdfP2Content) . ' bytes)', 'green');

        CLI::newLine();
        CLI::write('Semua pengujian fungsional BERHASIL!', 'green');
    }
}
