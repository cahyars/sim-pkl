<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Libraries\ExcelExport;
use App\Libraries\PdfExport;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;

class PresensiController extends BaseController
{
    public function index(int $siswaId)
    {
        $bulan = $this->request->getGet('bulan') ?: date('Y-m');
        if (! preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $bulan = date('Y-m');
        }

        $data = $this->ambilData($siswaId, $bulan);

        if ($data === null) {
            return redirect()->to('/guru/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $bulanSebelumnya = date('Y-m', strtotime($bulan . '-01 -1 month'));
        $bulanBerikutnya = date('Y-m', strtotime($bulan . '-01 +1 month'));

        return view('guru/presensi/index', [
            'title'           => 'Daftar Hadir - ' . $data['penempatan']['nama_siswa'],
            'penempatan'      => $data['penempatan'],
            'hari'            => $data['kalender']['hari'],
            'rekap'           => $data['kalender']['rekap'],
            'bulan'           => $bulan,
            'namaBulan'       => tanggal_bulan_tahun($bulan),
            'bulanSebelumnya' => $bulanSebelumnya,
            'bulanBerikutnya' => $bulanBerikutnya,
            'batasBulanAwal'  => substr($data['penempatan']['tanggal_mulai'], 0, 7),
            'batasBulanAkhir' => min(date('Y-m'), substr($data['penempatan']['tanggal_selesai'], 0, 7)),
        ]);
    }

    public function excel(int $siswaId)
    {
        $bulan = $this->request->getGet('bulan') ?: date('Y-m');
        $data  = $this->ambilData($siswaId, $bulan);

        if ($data === null) {
            return redirect()->to('/guru/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $p = $data['penempatan'];

        $headers = ['No', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Tanda Tangan Siswa', 'Tanda Tangan Pembimbing', 'Keterangan'];
        $rows    = [];

        foreach ($data['kalender']['hari'] as $i => $h) {
            [$datang, $pulang, $ket] = $this->kolomHari($h);
            $rows[] = [$i + 1, tanggal_indo($h['tanggal'], true), $datang, $pulang, '', '', $ket];
        }

        return (new ExcelExport())->unduh(
            'Daftar Hadir Kegiatan Praktik Kerja Lapangan (PKL)',
            [
                'Nama Peserta Didik : ' . $p['nama_siswa'],
                'Nama Tempat PKL    : ' . $p['nama_perusahaan'],
                'Nama Pembimbing PKL: ' . ($p['nama_pembimbing_lapangan'] ?? '-'),
                'Bulan              : ' . tanggal_bulan_tahun($bulan),
            ],
            $headers,
            $rows,
            'daftar-hadir-' . $p['nis'] . '-' . $bulan . '.xlsx'
        );
    }

    public function pdf(int $siswaId)
    {
        $bulan = $this->request->getGet('bulan') ?: date('Y-m');
        $data  = $this->ambilData($siswaId, $bulan);

        if ($data === null) {
            return redirect()->to('/guru/siswa')->with('error', 'Siswa tersebut bukan bimbingan Anda.');
        }

        $baris = [];
        foreach ($data['kalender']['hari'] as $i => $h) {
            [$datang, $pulang, $ket] = $this->kolomHari($h);
            $baris[] = ['no' => $i + 1, 'tanggal' => tanggal_indo($h['tanggal']), 'datang' => $datang, 'pulang' => $pulang, 'keterangan' => $ket];
        }

        return (new PdfExport())->unduh('guru/presensi/pdf', [
            'penempatan' => $data['penempatan'],
            'namaBulan'  => tanggal_bulan_tahun($bulan),
            'baris'      => $baris,
        ], 'daftar-hadir-' . $data['penempatan']['nis'] . '-' . $bulan . '.pdf', 'portrait');
    }

    /**
     * @return array{0: string, 1: string, 2: string} [jam datang, jam pulang, keterangan]
     */
    private function kolomHari(array $h): array
    {
        $entry = $h['entry'];

        if ($entry !== null && $entry['status_kehadiran'] === 'masuk') {
            return [substr($entry['jam_mulai'] ?? '-', 0, 5), substr($entry['jam_selesai'] ?? '-', 0, 5), logbook_status_label($entry['status'])];
        }

        if ($entry !== null) {
            return ['-', '-', ucfirst($entry['status_kehadiran'])];
        }

        if ($h['akhirPekan']) {
            return ['-', '-', 'Libur'];
        }

        if ($h['belumTiba']) {
            return ['-', '-', '-'];
        }

        return ['-', '-', 'Belum Mengisi'];
    }

    /**
     * Pastikan siswa ini benar bimbingan guru yang login, sekaligus siapkan data
     * penempatan (untuk kop Daftar Hadir) dan kalender presensi bulan terpilih.
     */
    private function ambilData(int $siswaId, string $bulan): ?array
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $bulan = date('Y-m');
        }

        $guruId = (int) $this->session->get('profile_id');

        $penempatan = (new PenempatanPklModel())->withRelasi()
            ->where('penempatan_pkl.siswa_id', $siswaId)
            ->where('penempatan_pkl.guru_pembimbing_id', $guruId)
            ->orderBy('penempatan_pkl.tanggal_mulai', 'DESC')
            ->first();

        if ($penempatan === null) {
            return null;
        }

        $kalender = (new LogbookModel())->kalenderPresensi($siswaId, $penempatan, $bulan);

        return ['penempatan' => $penempatan, 'kalender' => $kalender];
    }
}
