<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JurusanAspekPenilaianSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Pastikan data aspek yang ada sebelumnya diberi tanda jurusan Umum jika belum
        $this->db->table('aspek_penilaian')
            ->where('jurusan', 'Umum')
            ->orWhere('jurusan IS NULL')
            ->update(['jurusan' => 'Umum']);

        $aspekData = [
            // =========================================================================
            // 1. Akuntansi Keuangan / Akuntansi dan Keuangan Lembaga (6 Tujuan Pembelajaran)
            // =========================================================================
            [
                'nama_aspek' => 'Peserta didik mampu memahami etika profesi di bidang akuntansi',
                'deskripsi'  => 'Pemahaman terhadap kode etik dan integritas profesi akuntan dalam dunia kerja.',
                'jurusan'    => 'Akuntansi Keuangan',
                'urutan'     => 1,
                'bobot'      => 17,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Peserta didik mampu memahami ruang lingkup K3LH dalam penerapan di bidang kerja',
                'deskripsi'  => 'Pemahaman prinsip Kesehatan, Keselamatan Kerja dan Lingkungan Hidup di lingkungan kantor.',
                'jurusan'    => 'Akuntansi Keuangan',
                'urutan'     => 2,
                'bobot'      => 17,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Peserta didik mampu menerapkan etika profesi akuntansi dan K3LH di dalam dunia kerja',
                'deskripsi'  => 'Penerapan langsung etika profesi dan kepatuhan K3LH dalam aktivitas kerja harian.',
                'jurusan'    => 'Akuntansi Keuangan',
                'urutan'     => 3,
                'bobot'      => 17,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Peserta didik mampu memahami akuntansi perusahaan jasa, dagang, dan manufaktur',
                'deskripsi'  => 'Penguasaan siklus akuntansi transaksi pada perusahaan jasa, dagang, dan manufaktur.',
                'jurusan'    => 'Akuntansi Keuangan',
                'urutan'     => 4,
                'bobot'      => 17,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Peserta didik mampu memahami pengoperasian aplikasi komputer akuntansi',
                'deskripsi'  => 'Keterampilan menggunakan software akuntansi (spreadsheet/MYOB/Accurate/aplikasi kantor).',
                'jurusan'    => 'Akuntansi Keuangan',
                'urutan'     => 5,
                'bobot'      => 16,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Siswa mampu mengaplikasikan kompetensi keahlian akuntansi sesuai dengan bidang pekerjaannya',
                'deskripsi'  => 'Penyelesaian tugas-tugas pembukuan, pencatatan transaksi, dan rekonsiliasi keuangan nyata.',
                'jurusan'    => 'Akuntansi Keuangan',
                'urutan'     => 6,
                'bobot'      => 16,
                'is_active'  => 1,
            ],

            // =========================================================================
            // 2. Teknik Komputer & Jaringan / Teknik Jaringan Komputer dan Telekomunikasi (8 Tujuan Pembelajaran)
            // =========================================================================
            [
                'nama_aspek' => 'Memahami soft skills pada lingkungan kerja',
                'deskripsi'  => 'Pemahaman komunikasi, kedisiplinan, dan etika kerja pada lingkungan IT/jaringan.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 1,
                'bobot'      => 13,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Menerapkan soft skills pada lingkungan kerja',
                'deskripsi'  => 'Penerapan kerjasama tim, komunikasi efektif, dan inisiatif pemecahan masalah teknis.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 2,
                'bobot'      => 13,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Memahami norma, POS dan K3LH yang ada pada lingkungan kerja',
                'deskripsi'  => 'Pemahaman prosedur operasional standar dan keselamatan instalasi perangkat jaringan.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 3,
                'bobot'      => 13,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Menerapkan norma, POS dan K3LH yang ada pada lingkungan kerja',
                'deskripsi'  => 'Implementasi keselamatan kerja saat penanganan kabel, perangkat jaringan, dan instalasi listrik.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 4,
                'bobot'      => 13,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Memahami perkembangan teknologi pada perangkat teknik jaringan komputer dan telekomunikasi termasuk 5G, microwave Link, IPV6 teknologi serat optik terkini, IoT , Data Center, Cloud Computing dan information security',
                'deskripsi'  => 'Wawasan terhadap tren teknologi jaringan modern, cloud, dan keamanan data.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 5,
                'bobot'      => 12,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Menerapkan perangkat jaringan komputer dan telekomunikasi termasuk 5G, mivorwave Link, Ipv5 teknologi serat optik terkini, IoT , Data Center Cloud Computing dan information security',
                'deskripsi'  => 'Instalasi, konfigurasi router, switch, access point, dan pemeliharaan server.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 6,
                'bobot'      => 12,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Memahami fussion splicer pada jaringan fiber optik',
                'deskripsi'  => 'Penguasaan teori, jenis kabel optik, dan pengoperasian mesin fusion splicer & OTDR.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 7,
                'bobot'      => 12,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Menerapkan fussion splicer pada jaringan fiber optik',
                'deskripsi'  => 'Keterampilan praktik penarikan, pengupasan, penyambungan core, dan proteksi serat optik.',
                'jurusan'    => 'Teknik Komputer & Jaringan',
                'urutan'     => 8,
                'bobot'      => 12,
                'is_active'  => 1,
            ],

            // =========================================================================
            // 3. Rekayasa Perangkat Lunak (6 Tujuan Pembelajaran)
            // =========================================================================
            [
                'nama_aspek' => 'Memahami etika profesi, norma, POS dan K3LH pada industri perangkat lunak',
                'deskripsi'  => 'Integritas kode sumber, kerahasiaan data pengguna, dan kesehatan kerja komputer.',
                'jurusan'    => 'Rekayasa Perangkat Lunak',
                'urutan'     => 1,
                'bobot'      => 15,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Menerapkan metodologi pengembangan perangkat lunak (Agile/Scrum/Waterfall)',
                'deskripsi'  => 'Kepatuhan terhadap tahapan sprint, stand-up meeting, dan manajemen backlog.',
                'jurusan'    => 'Rekayasa Perangkat Lunak',
                'urutan'     => 2,
                'bobot'      => 15,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Memahami dan menerapkan perancangan basis data relasional',
                'deskripsi'  => 'Pemodelan ERD, normalisasi data, pembuatan tabel, dan optimasi query SQL.',
                'jurusan'    => 'Rekayasa Perangkat Lunak',
                'urutan'     => 3,
                'bobot'      => 20,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Mengimplementasikan pemrograman aplikasi sesuai spesifikasi kebutuhan',
                'deskripsi'  => 'Penulisan logika kode, integrasi antarmuka/API, dan kerapian struktur kode.',
                'jurusan'    => 'Rekayasa Perangkat Lunak',
                'urutan'     => 4,
                'bobot'      => 25,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Melakukan pengujian fungsionalitas (testing) dan debugging aplikasi',
                'deskripsi'  => 'Identifikasi bug, penanganan exception, dan pengujian alur bisnis aplikasi.',
                'jurusan'    => 'Rekayasa Perangkat Lunak',
                'urutan'     => 5,
                'bobot'      => 15,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Mampu menyusun dokumentasi teknis dan panduan penggunaan sistem',
                'deskripsi'  => 'Penyusunan API docs, commit log, dan manual petunjuk pengguna aplikasi.',
                'jurusan'    => 'Rekayasa Perangkat Lunak',
                'urutan'     => 6,
                'bobot'      => 10,
                'is_active'  => 1,
            ],

            // =========================================================================
            // 4. Teknik Kendaraan Ringan (5 Tujuan Pembelajaran)
            // =========================================================================
            [
                'nama_aspek' => 'Menerapkan budaya kerja 5R dan K3 pada bengkel otomotif',
                'deskripsi'  => 'Kebersihan area kerja, penggunaan APD, dan penanganan limbah B3 bengkel.',
                'jurusan'    => 'Teknik Kendaraan Ringan',
                'urutan'     => 1,
                'bobot'      => 20,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Melakukan perawatan berkala sistem mesin (engine) kendaraan ringan',
                'deskripsi'  => 'Pemeriksaan oli, filter, busi, timing belt, dan tune-up berkala mesin.',
                'jurusan'    => 'Teknik Kendaraan Ringan',
                'urutan'     => 2,
                'bobot'      => 25,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Melakukan pemeriksaan dan perbaikan sistem kelistrikan kendaraan',
                'deskripsi'  => 'Pemeriksaan baterai, alternator, sistem starter, dan sistem penerangan mobil.',
                'jurusan'    => 'Teknik Kendaraan Ringan',
                'urutan'     => 3,
                'bobot'      => 20,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Melakukan perawatan dan perbaikan sistem chasis dan pemindah tenaga',
                'deskripsi'  => 'Pemeriksaan sistem rem, kopling, transmisi, suspensi, dan spooring balancing.',
                'jurusan'    => 'Teknik Kendaraan Ringan',
                'urutan'     => 4,
                'bobot'      => 20,
                'is_active'  => 1,
            ],
            [
                'nama_aspek' => 'Mengoperasikan alat ukur mekanik, pneumatik, dan scanner diagnostik otomotif',
                'deskripsi'  => 'Penggunaan jangka sorong, micrometer, torque wrench, dan OBD scanner.',
                'jurusan'    => 'Teknik Kendaraan Ringan',
                'urutan'     => 5,
                'bobot'      => 15,
                'is_active'  => 1,
            ],
        ];

        foreach ($aspekData as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        $this->db->table('aspek_penilaian')->insertBatch($aspekData);
    }
}
