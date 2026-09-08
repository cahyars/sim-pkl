<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CekModel extends BaseCommand
{
    protected $group       = 'SIM-PKL';
    protected $name        = 'simpkl:cek-model';
    protected $description = 'Uji cepat seluruh Model dapat dimuat dan query relasinya berjalan.';

    public function run(array $params)
    {
        $models = [
            'UserModel', 'SiswaModel', 'GuruPembimbingModel', 'TempatPklModel',
            'PembimbingLapanganModel', 'PenempatanPklModel', 'LogbookModel',
            'DokumentasiLogbookModel', 'AspekPenilaianModel', 'PenilaianModel',
            'DetailPenilaianModel', 'NotifikasiModel', 'TahunAjaranModel', 'PengaturanModel',
        ];

        foreach ($models as $nama) {
            $kelas = "App\\Models\\{$nama}";
            $model = new $kelas();
            CLI::write(sprintf('  %-28s %s baris', $nama, $model->countAllResults()), 'green');
        }

        CLI::newLine();
        CLI::write('Uji query relasi:', 'yellow');

        $siswa = new \App\Models\SiswaModel();
        $row   = $siswa->withUser()->where('siswa.nis', '2324001')->first();
        CLI::write('  SiswaModel::withUser  -> ' . ($row['nama'] ?? 'GAGAL') . ' / ' . ($row['kelas'] ?? '-'));

        $penempatan = new \App\Models\PenempatanPklModel();
        $p          = $penempatan->withRelasi()->where('penempatan_pkl.status', 'aktif')->first();
        CLI::write('  PenempatanPklModel::withRelasi -> ' . ($p['nama_siswa'] ?? 'GAGAL') . ' @ ' . ($p['nama_perusahaan'] ?? '-') . ' (guru: ' . ($p['nama_guru'] ?? '-') . ')');

        $logbook = new \App\Models\LogbookModel();
        $rekap   = $logbook->rekapStatus((int) $row['id']);
        CLI::write('  LogbookModel::rekapStatus -> ' . json_encode($rekap));

        $tempat = new \App\Models\TempatPklModel();
        foreach ($tempat->withKuotaTerpakai() as $t) {
            CLI::write(sprintf('  Kuota %-38s %d/%d', $t['nama_perusahaan'], $t['terisi'], $t['kuota']));
        }

        $detail = new \App\Models\DetailPenilaianModel();
        $pen    = (new \App\Models\PenilaianModel())->withSiswa()->where('penilaian.status', 'final')->first();
        CLI::write('  PenilaianModel::withSiswa -> ' . ($pen['nama_siswa'] ?? 'GAGAL') . ' nilai ' . ($pen['nilai_akhir'] ?? '-') . ' [' . \App\Models\PenilaianModel::predikat((float) ($pen['nilai_akhir'] ?? 0)) . ']');
        CLI::write('  DetailPenilaianModel::byPenilaian -> ' . count($detail->byPenilaian((int) $pen['id'])) . ' aspek');

        $pengaturan = new \App\Models\PengaturanModel();
        CLI::write('  PengaturanModel::ambil(reminder_hari) -> ' . $pengaturan->ambil('reminder_hari'));

        CLI::newLine();
        CLI::write('Semua model OK.', 'green');
    }
}
