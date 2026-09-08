<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// --- Autentikasi ---------------------------------------------------------
$routes->get('login', 'Auth\AuthController::login', ['filter' => 'guest']);
$routes->post('login', 'Auth\AuthController::attemptLogin', ['filter' => 'guest']);
$routes->get('logout', 'Auth\AuthController::logout');

// --- Notifikasi (semua role yang sudah login) -----------------------------
$routes->get('notifikasi/unread', 'NotifikasiController::unread', ['filter' => 'role']);
$routes->get('notifikasi/baca-semua', 'NotifikasiController::bacaSemua', ['filter' => 'role']);
$routes->get('notifikasi/(:num)/baca', 'NotifikasiController::baca/$1', ['filter' => 'role']);

// --- Siswa (mobile-first) -------------------------------------------------
$routes->group('siswa', ['filter' => 'role:siswa', 'namespace' => 'App\Controllers\Siswa'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('logbook', 'LogbookController::index');
    $routes->get('logbook/tambah', 'LogbookController::create');
    $routes->post('logbook', 'LogbookController::store');
    $routes->get('logbook/(:num)', 'LogbookController::show/$1');
    $routes->get('logbook/(:num)/edit', 'LogbookController::edit/$1');
    $routes->post('logbook/(:num)', 'LogbookController::update/$1');
    $routes->post('logbook/(:num)/dokumentasi/(:num)/hapus', 'LogbookController::hapusDokumentasi/$1/$2');
    $routes->get('logbook/dokumentasi/(:num)', 'LogbookController::file/$1');

    $routes->get('nilai', 'NilaiController::index');
    $routes->get('nilai/pdf', 'NilaiController::pdf');

    $routes->get('presensi', 'PresensiController::index');

    $routes->get('profil', 'ProfilController::index');
    $routes->post('profil', 'ProfilController::update');
    $routes->post('profil/password', 'ProfilController::updatePassword');
    $routes->get('profil/foto/(:any)', 'ProfilController::foto/$1');
});

// --- Guru Pembimbing --------------------------------------------------------
$routes->group('guru', ['filter' => 'role:guru_pembimbing', 'namespace' => 'App\Controllers\Guru'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('siswa', 'SiswaController::index');
    $routes->get('siswa/(:num)', 'SiswaController::show/$1');
    $routes->get('siswa/(:num)/kehadiran', 'PresensiController::index/$1');
    $routes->get('siswa/(:num)/kehadiran/excel', 'PresensiController::excel/$1');
    $routes->get('siswa/(:num)/kehadiran/pdf', 'PresensiController::pdf/$1');

    $routes->get('validasi', 'ValidasiLogbookController::index');
    $routes->get('validasi/(:num)', 'ValidasiLogbookController::show/$1');
    $routes->post('validasi/(:num)/setujui', 'ValidasiLogbookController::setujui/$1');
    $routes->post('validasi/(:num)/revisi', 'ValidasiLogbookController::revisi/$1');
    $routes->post('validasi/(:num)/tolak', 'ValidasiLogbookController::tolak/$1');
    $routes->get('validasi/dokumentasi/(:num)', 'ValidasiLogbookController::file/$1');

    $routes->get('laporan', 'LaporanController::index');
    $routes->get('laporan/excel', 'LaporanController::excel');
    $routes->get('laporan/pdf', 'LaporanController::pdf');
});

// --- Pembimbing Lapangan (DU/DI) --------------------------------------------
$routes->group('pembimbing-lapangan', ['filter' => 'role:pembimbing_lapangan', 'namespace' => 'App\Controllers\PembimbingLapangan'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('siswa', 'SiswaController::index');
    $routes->get('siswa/(:num)', 'SiswaController::show/$1');

    $routes->get('penilaian/(:num)', 'PenilaianController::form/$1');
    $routes->post('penilaian/(:num)', 'PenilaianController::store/$1');
    $routes->get('penilaian/(:num)/pdf', 'PenilaianController::pdf/$1');
});

// --- Admin / Tata Usaha PKL ---------------------------------------------
$routes->group('admin', ['filter' => 'role:admin', 'namespace' => 'App\Controllers\Admin'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->post('switch-tahun-ajaran', '\App\Controllers\Auth\AuthController::switchTahunAjaran');

    // Kelola Jurusan
    $routes->get('jurusan', 'JurusanController::index');
    $routes->post('jurusan', 'JurusanController::store');
    $routes->post('jurusan/(:num)/update', 'JurusanController::update/$1');
    $routes->post('jurusan/(:num)/hapus', 'JurusanController::delete/$1');
    $routes->post('jurusan/(:num)/toggle', 'JurusanController::toggleAktif/$1');

    // Data Siswa
    $routes->get('siswa', 'SiswaController::index');
    $routes->get('siswa/tambah', 'SiswaController::create');
    $routes->post('siswa', 'SiswaController::store');
    $routes->get('siswa/import', 'SiswaController::importForm');
    $routes->post('siswa/import', 'SiswaController::import');
    $routes->get('siswa/template', 'SiswaController::downloadTemplate');
    $routes->get('siswa/(:num)/edit', 'SiswaController::edit/$1');
    $routes->post('siswa/(:num)', 'SiswaController::update/$1');
    $routes->post('siswa/(:num)/hapus', 'SiswaController::delete/$1');
    $routes->post('siswa/(:num)/toggle-aktif', 'SiswaController::toggleAktif/$1');
    $routes->post('siswa/(:num)/reset-password', 'SiswaController::resetPassword/$1');

    // Data Tempat PKL + Pembimbing Lapangan (nested)
    $routes->get('tempat-pkl', 'TempatPklController::index');
    $routes->get('tempat-pkl/tambah', 'TempatPklController::create');
    $routes->post('tempat-pkl', 'TempatPklController::store');
    $routes->get('tempat-pkl/(:num)', 'TempatPklController::show/$1');
    $routes->get('tempat-pkl/(:num)/edit', 'TempatPklController::edit/$1');
    $routes->post('tempat-pkl/(:num)', 'TempatPklController::update/$1');
    $routes->post('tempat-pkl/(:num)/hapus', 'TempatPklController::delete/$1');

    $routes->post('tempat-pkl/(:num)/pembimbing', 'PembimbingLapanganController::store/$1');
    $routes->post('pembimbing-lapangan/(:num)', 'PembimbingLapanganController::update/$1');
    $routes->post('pembimbing-lapangan/(:num)/hapus', 'PembimbingLapanganController::delete/$1');
    $routes->post('pembimbing-lapangan/(:num)/toggle-aktif', 'PembimbingLapanganController::toggleAktif/$1');
    $routes->post('pembimbing-lapangan/(:num)/reset-password', 'PembimbingLapanganController::resetPassword/$1');

    // Data Guru Pembimbing
    $routes->get('guru', 'GuruController::index');
    $routes->post('guru', 'GuruController::store');
    $routes->post('guru/(:num)', 'GuruController::update/$1');
    $routes->post('guru/(:num)/hapus', 'GuruController::delete/$1');
    $routes->post('guru/(:num)/toggle-aktif', 'GuruController::toggleAktif/$1');
    $routes->post('guru/(:num)/reset-password', 'GuruController::resetPassword/$1');

    // Penempatan PKL
    $routes->get('penempatan', 'PenempatanController::index');
    $routes->get('penempatan/tambah', 'PenempatanController::create');
    $routes->post('penempatan', 'PenempatanController::store');
    $routes->get('penempatan/riwayat/(:num)', 'PenempatanController::riwayat/$1');
    $routes->post('penempatan/(:num)/selesai', 'PenempatanController::selesaikan/$1');
    $routes->post('penempatan/(:num)/batalkan', 'PenempatanController::batalkan/$1');

    // Tahun Ajaran
    $routes->get('tahun-ajaran', 'TahunAjaranController::index');
    $routes->post('tahun-ajaran', 'TahunAjaranController::store');
    $routes->post('tahun-ajaran/(:num)', 'TahunAjaranController::update/$1');
    $routes->post('tahun-ajaran/(:num)/aktifkan', 'TahunAjaranController::jadikanAktif/$1');

    // Aspek Penilaian
    $routes->get('aspek-penilaian', 'AspekPenilaianController::index');
    $routes->post('aspek-penilaian', 'AspekPenilaianController::store');
    $routes->post('aspek-penilaian/(:num)', 'AspekPenilaianController::update/$1');
    $routes->post('aspek-penilaian/(:num)/toggle-aktif', 'AspekPenilaianController::toggleAktif/$1');
    $routes->post('aspek-penilaian/(:num)/hapus', 'AspekPenilaianController::delete/$1');

    // Pengaturan Sistem
    $routes->get('pengaturan', 'PengaturanController::index');
    $routes->post('pengaturan', 'PengaturanController::update');

    // Laporan
    $routes->get('laporan', 'LaporanController::index');

    $routes->get('laporan/monitoring', 'LaporanController::monitoring');
    $routes->get('laporan/monitoring/excel', 'LaporanController::monitoringExcel');
    $routes->get('laporan/monitoring/pdf', 'LaporanController::monitoringPdf');

    $routes->get('laporan/sebaran', 'LaporanController::sebaran');
    $routes->get('laporan/sebaran/excel', 'LaporanController::sebaranExcel');
    $routes->get('laporan/sebaran/pdf', 'LaporanController::sebaranPdf');

    $routes->get('laporan/nilai', 'LaporanController::nilai');
    $routes->get('laporan/nilai/excel', 'LaporanController::nilaiExcel');
    $routes->get('laporan/nilai/pdf', 'LaporanController::nilaiPdf');

    $routes->get('laporan/kehadiran', 'LaporanController::kehadiran');
    $routes->get('laporan/kehadiran/excel', 'LaporanController::kehadiranExcel');
    $routes->get('laporan/kehadiran/pdf', 'LaporanController::kehadiranPdf');

    $routes->get('laporan/plotting', 'LaporanController::plotting');
    $routes->get('laporan/plotting/excel', 'LaporanController::plottingExcel');
    $routes->get('laporan/plotting/pdf', 'LaporanController::plottingPdf');
});
