<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use DateInterval;
use DatePeriod;
use DateTime;

class LogbookSeeder extends Seeder
{
    /**
     * Profil pengisian per NIS: [peluang isi (%), jumlah hari kerja terakhir yang dilewati].
     * "Dilewati" dipakai untuk menghasilkan siswa yang layak menerima reminder (Fitur §4.5).
     */
    private array $profil = [
        '2324001' => [95, 0],
        '2324002' => [75, 0],
        '2324003' => [70, 1],
        '2324004' => [85, 0],
        '2324005' => [45, 6],   // menunggak — kandidat reminder
        '2324006' => [95, 0],
        '2324007' => [70, 0],
        '2324008' => [80, 2],
        '2324009' => [40, 8],   // menunggak — kandidat reminder
        '2324010' => [75, 0],
        '2324011' => [85, 1],
        '2324012' => [45, 5],   // menunggak — kandidat reminder
        '2324013' => [95, 0],
        '2324014' => [70, 0],
        '2324015' => [0, 0],    // belum pernah mengisi sama sekali
    ];

    private array $kegiatan = [
        'Rekayasa Perangkat Lunak' => [
            'Mempelajari struktur project Laravel milik perusahaan dan menelusuri alur routing hingga controller.',
            'Membuat halaman CRUD data pelanggan menggunakan Blade template dan Bootstrap.',
            'Melakukan perbaikan bug pada modul autentikasi yang gagal memvalidasi email.',
            'Mengikuti daily standup meeting tim developer dan mencatat progres task sprint berjalan.',
            'Membuat query builder untuk laporan rekap transaksi bulanan.',
            'Menguji endpoint API menggunakan Postman dan mendokumentasikan hasilnya.',
            'Melakukan slicing desain Figma menjadi halaman HTML responsif.',
            'Belajar penggunaan Git flow: branching, commit, dan pull request ke repository tim.',
            'Melakukan refactor kode controller agar sesuai standar penulisan tim.',
            'Membantu migrasi data lama ke struktur tabel baru menggunakan script seeder.',
        ],
        'Teknik Komputer & Jaringan' => [
            'Melakukan crimping kabel UTP straight dan cross serta pengujian dengan LAN tester.',
            'Membantu instalasi access point di lantai 2 kantor klien dan konfigurasi SSID.',
            'Mempelajari konfigurasi dasar MikroTik: IP address, DHCP server, dan NAT.',
            'Melakukan troubleshooting koneksi internet lambat pada jaringan kantor.',
            'Membantu pemasangan kabel fiber optic dari ODP ke rumah pelanggan.',
            'Mendokumentasikan topologi jaringan klien menggunakan aplikasi diagram.',
            'Melakukan maintenance rutin perangkat switch dan pembersihan rack server.',
            'Konfigurasi bandwidth management menggunakan simple queue di MikroTik.',
            'Membantu instalasi CCTV dan konfigurasi akses monitoring via smartphone.',
            'Melakukan pendataan inventaris perangkat jaringan milik perusahaan.',
        ],
        'Teknik Kendaraan Ringan' => [
            'Melakukan servis berkala kendaraan: ganti oli mesin dan filter oli.',
            'Membantu pembongkaran dan pemeriksaan sistem rem cakram depan.',
            'Mempelajari prosedur pemeriksaan sistem kelistrikan bodi kendaraan.',
            'Melakukan spooring dan balancing roda pada kendaraan pelanggan.',
            'Membantu penggantian kampas kopling pada kendaraan transmisi manual.',
            'Melakukan pemeriksaan dan pengisian ulang refrigerant sistem AC mobil.',
            'Mempelajari penggunaan scanner OBD-II untuk membaca kode kerusakan mesin.',
            'Membantu perbaikan sistem suspensi: penggantian shock absorber belakang.',
            'Melakukan pembersihan throttle body dan injektor bahan bakar.',
            'Mencatat riwayat servis kendaraan pelanggan ke dalam buku administrasi bengkel.',
        ],
        'Akuntansi Keuangan' => [
            'Melakukan input data transaksi harian ke dalam buku kas koperasi.',
            'Membantu rekapitulasi angsuran pinjaman anggota bulan berjalan.',
            'Mempelajari alur pencatatan jurnal umum hingga buku besar.',
            'Melakukan pengarsipan bukti transaksi dan penomoran dokumen keuangan.',
            'Membantu penyusunan laporan neraca sederhana periode mingguan.',
            'Melakukan verifikasi kelengkapan berkas pengajuan pinjaman anggota baru.',
            'Membantu pelayanan anggota untuk transaksi simpanan wajib dan sukarela.',
            'Mempelajari penggunaan aplikasi akuntansi koperasi untuk posting transaksi.',
            'Melakukan pencocokan saldo kas fisik dengan catatan pembukuan.',
            'Membantu penyusunan laporan sisa hasil usaha periode berjalan.',
        ],
    ];

    private array $kendala = [
        null,
        null,
        null,
        'Belum terbiasa dengan tools yang dipakai perusahaan, masih perlu banyak bertanya ke pembimbing.',
        'Koneksi internet kantor sempat terputus sehingga pekerjaan tertunda sekitar satu jam.',
        'Kesulitan memahami dokumentasi teknis karena menggunakan istilah yang belum dipelajari di sekolah.',
        'Peralatan yang dibutuhkan sedang dipakai karyawan lain, harus menunggu giliran.',
    ];

    public function run()
    {
        $now      = date('Y-m-d H:i:s');
        $hariIni  = new DateTime(date('Y-m-d'));
        $mulai    = new DateTime(PenempatanSeeder::TANGGAL_MULAI);

        $penempatan = $this->db->table('penempatan_pkl pp')
            ->select('pp.id, pp.siswa_id, pp.guru_pembimbing_id, s.nis, s.jurusan')
            ->join('siswa s', 's.id = pp.siswa_id')
            ->where('pp.status', 'aktif')
            ->get()->getResultArray();

        $baris = [];

        foreach ($penempatan as $p) {
            [$peluang, $lewatiHariTerakhir] = $this->profil[$p['nis']] ?? [70, 0];

            if ($peluang === 0) {
                continue;
            }

            $hariKerja = $this->hariKerja($mulai, $hariIni);

            // Potong sejumlah hari kerja terakhir agar siswa tampak menunggak.
            if ($lewatiHariTerakhir > 0) {
                $hariKerja = array_slice($hariKerja, 0, max(0, count($hariKerja) - $lewatiHariTerakhir));
            }

            $daftarKegiatan = $this->kegiatan[$p['jurusan']] ?? $this->kegiatan['Rekayasa Perangkat Lunak'];
            $totalHari      = count($hariKerja);

            foreach ($hariKerja as $index => $tanggal) {
                if (random_int(1, 100) > $peluang) {
                    continue;
                }

                $status       = $this->tentukanStatus($index, $totalHari);
                $jamMulai     = random_int(0, 1) === 1 ? '08:00:00' : '07:30:00';
                $jamSelesai   = random_int(0, 1) === 1 ? '16:00:00' : '15:30:00';
                $sudahDivalidasi = in_array($status, ['disetujui', 'revisi', 'ditolak'], true);

                $baris[] = [
                    'siswa_id'          => $p['siswa_id'],
                    'penempatan_pkl_id' => $p['id'],
                    'tanggal_kegiatan'  => $tanggal,
                    'jam_mulai'         => $jamMulai,
                    'jam_selesai'       => $jamSelesai,
                    'uraian_kegiatan'   => $daftarKegiatan[array_rand($daftarKegiatan)],
                    'kendala'           => $this->kendala[array_rand($this->kendala)],
                    'status'            => $status,
                    'catatan_guru'      => $this->catatanGuru($status),
                    'validated_by'      => $sudahDivalidasi ? $p['guru_pembimbing_id'] : null,
                    'validated_at'      => $sudahDivalidasi ? $tanggal . ' 19:15:00' : null,
                    'submitted_at'      => $status === 'draft' ? null : $tanggal . ' 17:05:00',
                    'created_at'        => $tanggal . ' 16:40:00',
                    'updated_at'        => $now,
                ];
            }
        }

        if ($baris !== []) {
            $this->db->table('logbook')->insertBatch($baris);
        }
    }

    /**
     * Daftar tanggal Senin–Jumat dalam rentang, termasuk hari ini.
     *
     * @return list<string>
     */
    private function hariKerja(DateTime $mulai, DateTime $sampai): array
    {
        $periode = new DatePeriod($mulai, new DateInterval('P1D'), (clone $sampai)->modify('+1 day'));
        $hari    = [];

        foreach ($periode as $tanggal) {
            if ((int) $tanggal->format('N') <= 5) {
                $hari[] = $tanggal->format('Y-m-d');
            }
        }

        return $hari;
    }

    /**
     * Logbook lama umumnya sudah divalidasi; yang paling baru masih menunggu atau draft.
     */
    private function tentukanStatus(int $index, int $total): string
    {
        $sisaHariKeBelakang = $total - $index;

        if ($sisaHariKeBelakang <= 2) {
            return random_int(1, 10) <= 3 ? 'draft' : 'menunggu_validasi';
        }

        if ($sisaHariKeBelakang <= 5) {
            return random_int(1, 10) <= 6 ? 'menunggu_validasi' : 'disetujui';
        }

        $undian = random_int(1, 100);

        if ($undian <= 80) {
            return 'disetujui';
        }
        if ($undian <= 92) {
            return 'revisi';
        }
        if ($undian <= 96) {
            return 'ditolak';
        }

        return 'menunggu_validasi';
    }

    private function catatanGuru(string $status): ?string
    {
        return match ($status) {
            'disetujui' => random_int(1, 10) <= 4 ? 'Bagus, pertahankan ketelitian dalam mencatat kegiatan.' : null,
            'revisi'    => 'Uraian kegiatan masih terlalu singkat. Mohon jelaskan langkah pekerjaan lebih rinci dan lampirkan dokumentasi.',
            'ditolak'   => 'Kegiatan yang ditulis tidak sesuai dengan laporan pembimbing lapangan pada tanggal tersebut.',
            default     => null,
        };
    }
}
