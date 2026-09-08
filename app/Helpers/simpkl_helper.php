<?php
/**
 * Helper umum SIM-PKL: label tampilan untuk role & status enum.
 * Dimuat otomatis lewat $this->helpers pada BaseController.
 */

if (! function_exists('role_label')) {
    function role_label(?string $role): string
    {
        return match ($role) {
            'siswa'                => 'Siswa',
            'guru_pembimbing'      => 'Guru Pembimbing',
            'pembimbing_lapangan'  => 'Pembimbing Lapangan',
            'admin'                => 'Admin / Tata Usaha',
            default                => '-',
        };
    }
}

if (! function_exists('logbook_status_label')) {
    function logbook_status_label(?string $status): string
    {
        return match ($status) {
            'draft'              => 'Draft',
            'menunggu_validasi'  => 'Menunggu Validasi',
            'disetujui'          => 'Disetujui',
            'revisi'             => 'Revisi',
            'ditolak'            => 'Ditolak',
            default              => '-',
        };
    }
}

if (! function_exists('logbook_status_badge')) {
    function logbook_status_badge(?string $status): string
    {
        return match ($status) {
            'draft'              => 'text-bg-secondary',
            'menunggu_validasi'  => 'text-bg-warning',
            'disetujui'          => 'text-bg-success',
            'revisi'             => 'text-bg-info',
            'ditolak'            => 'text-bg-danger',
            default              => 'text-bg-secondary',
        };
    }
}

if (! function_exists('penempatan_status_label')) {
    function penempatan_status_label(?string $status): string
    {
        return match ($status) {
            'aktif'       => 'Aktif',
            'selesai'     => 'Selesai',
            'dibatalkan'  => 'Dibatalkan',
            default       => '-',
        };
    }
}

if (! function_exists('tanggal_indo')) {
    function tanggal_indo(?string $tanggal, bool $denganHari = false): string
    {
        if (empty($tanggal)) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $time = strtotime($tanggal);
        $out  = date('d', $time) . ' ' . $bulan[(int) date('n', $time)] . ' ' . date('Y', $time);

        if ($denganHari) {
            $out = $hari[(int) date('w', $time)] . ', ' . $out;
        }

        return $out;
    }
}

if (! function_exists('tanggal_bulan_tahun')) {
    /**
     * Format "2026-09" menjadi "September 2026" — dipakai judul halaman Presensi
     * & Daftar Hadir.
     */
    function tanggal_bulan_tahun(string $bulanYm): string
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        [$tahun, $bln] = array_map('intval', explode('-', $bulanYm . '-01'));

        return ($bulan[$bln] ?? $bulanYm) . ' ' . $tahun;
    }
}

if (! function_exists('jumlah_hari_kerja')) {
    /**
     * Hitung jumlah hari Senin-Jumat antara dua tanggal (inklusif) — dipakai
     * laporan rekap kehadiran/keaktifan untuk membandingkan hari kerja PKL
     * dengan jumlah hari logbook benar-benar terisi.
     */
    function jumlah_hari_kerja(string $mulai, string $sampai): int
    {
        if (strtotime($sampai) < strtotime($mulai)) {
            return 0;
        }

        $periode = new DatePeriod(
            new DateTime($mulai),
            new DateInterval('P1D'),
            (new DateTime($sampai))->modify('+1 day')
        );

        $jumlah = 0;

        foreach ($periode as $tanggal) {
            if ((int) $tanggal->format('N') <= 5) {
                $jumlah++;
            }
        }

        return $jumlah;
    }
}

if (! function_exists('nav_icon')) {
    /**
     * Ikon SVG inline (stroke currentColor) untuk menu navigasi sidebar &
     * bottom nav. Inline supaya tidak perlu dependency icon font tambahan
     * dan warnanya otomatis mengikuti warna teks link.
     */
    function nav_icon(string $name, string $class = 'nav-icon'): string
    {
        $paths = [
            // Dashboard — grid panel
            'dashboard'  => '<rect x="3" y="3" width="7" height="8" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="3" y="15" width="7" height="6" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/>',
            // Data siswa — dua orang
            'siswa'      => '<path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20"/><circle cx="10" cy="8" r="3.2"/><path d="M16.5 11.2A3 3 0 0 0 16.5 5.4"/><path d="M20 20v-1.4a3.4 3.4 0 0 0-2.2-3.1"/>',
            // Tempat PKL — gedung
            'tempat'     => '<path d="M4 21V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v15"/><path d="M14 10h4a2 2 0 0 1 2 2v9"/><path d="M3 21h18"/><path d="M7.5 8h3"/><path d="M7.5 12h3"/><path d="M7.5 16h3"/><path d="M16.5 15h1.5"/>',
            // Guru pembimbing — toga
            'guru'       => '<path d="M21 9.5 12 5 3 9.5l9 4.5 9-4.5Z"/><path d="M7 11.6V16c0 1.7 2.2 3 5 3s5-1.3 5-3v-4.4"/><path d="M20 10.6v4.2"/>',
            // Penempatan PKL — pin lokasi
            'penempatan' => '<path d="M12 21s6.5-5.4 6.5-10.2A6.5 6.5 0 0 0 5.5 10.8C5.5 15.6 12 21 12 21Z"/><circle cx="12" cy="10.5" r="2.4"/>',
            // Tahun ajaran — kalender
            'kalender'   => '<rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M3.5 10h17"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M8 14h3"/><path d="M8 17.5h6"/>',
            // Laporan — dokumen bergrafik
            'laporan'    => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path d="M14 3v5h5"/><path d="M9 17v-3"/><path d="M12 17v-5"/><path d="M15 17v-2"/>',
            // Pengaturan — gear
            'pengaturan' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.03 1.56V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1.1-1.56 1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.56-1.03H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6h.07A1.7 1.7 0 0 0 10.1 3.04V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9v.07a1.7 1.7 0 0 0 1.56 1.03H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1.03Z"/>',
            // Validasi logbook — clipboard dengan centang
            'validasi'   => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1Z"/><path d="M16 6h1.5A1.5 1.5 0 0 1 19 7.5v11A2.5 2.5 0 0 1 16.5 21h-9A2.5 2.5 0 0 1 5 18.5v-11A1.5 1.5 0 0 1 6.5 6H8"/><path d="M9 13.5l2 2 4-4"/>',
            // Logbook — buku dengan pena
            'logbook'    => '<path d="M5 19.5V5.5A2.5 2.5 0 0 1 7.5 3H18a1 1 0 0 1 1 1v9"/><path d="M5 19.5A2.5 2.5 0 0 1 7.5 17H19"/><path d="M19 17v4H7.5A2.5 2.5 0 0 1 5 18.5"/><path d="M9 7.5h6"/><path d="M9 10.5h4"/>',
            // Nilai — medali
            'nilai'      => '<circle cx="12" cy="14.5" r="5"/><path d="M12 12.6l.95 1.9 2.1.3-1.52 1.48.36 2.08L12 17.4l-1.89.96.36-2.08L8.95 14.8l2.1-.3.95-1.9Z"/><path d="M8.5 8.6 6.5 3h11l-2 5.6"/>',
            // Profil — orang
            'profil'     => '<circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/>',
            // Beranda — rumah
            'beranda'    => '<path d="M3.5 10.5 12 3.5l8.5 7"/><path d="M5.5 9.8V19a1.5 1.5 0 0 0 1.5 1.5h10a1.5 1.5 0 0 0 1.5-1.5V9.8"/><path d="M10 20.5v-5h4v5"/>',
            // Jurusan — kurikulum / program keahlian
            'jurusan'    => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 7h8"/><path d="M8 11h8"/>',
            // Rekap Plotting — tabel spreadsheet
            'rekap'      => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/>',
        ];

        $body = $paths[$name] ?? $paths['dashboard'];

        return '<svg class="' . esc($class, 'attr') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
            . ' stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"'
            . ' focusable="false">' . $body . '</svg>';
    }
}
