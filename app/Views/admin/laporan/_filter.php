<?php
/**
 * Partial filter bersama untuk laporan monitoring/nilai/kehadiran.
 * Variabel yang diharapkan: $filter, $daftarKelas, $daftarTempat, $daftarTahun,
 * $aksiExcel, $aksiPdf, $resetUrl
 */

$opsiKelas = [];
foreach ($daftarKelas as $k) {
    $opsiKelas[$k] = $k;
}

$opsiTempat = [];
foreach ($daftarTempat as $t) {
    $opsiTempat[$t['id']] = $t['nama_perusahaan'];
}

$opsiTahun = [];
foreach ($daftarTahun as $t) {
    $opsiTahun[$t['id']] = $t['nama_tahun_ajaran'] . ' ' . ucfirst($t['semester']);
}
?>
<?= view('partials/table_toolbar', [
    'searchName'        => 'q',
    'searchValue'       => $filter['q'],
    'searchPlaceholder' => 'Cari siswa/NIS/kelas/perusahaan',
    'searchWidth'       => '250px',
    'filters'           => [
        [
            'name'    => 'tahun_ajaran_id',
            'value'   => $filter['tahun_ajaran_id'],
            'label'   => 'Periode',
            'empty'   => 'Semua Periode',
            'width'   => '185px',
            'options' => $opsiTahun,
        ],
        [
            'name'    => 'kelas',
            'value'   => $filter['kelas'],
            'label'   => 'Kelas',
            'empty'   => 'Semua Kelas',
            'width'   => '150px',
            'options' => $opsiKelas,
        ],
        [
            'name'    => 'tempat_pkl_id',
            'value'   => $filter['tempat_pkl_id'],
            'label'   => 'Tempat PKL',
            'empty'   => 'Semua Tempat PKL',
            'width'   => '210px',
            'options' => $opsiTempat,
        ],
        [
            'name'    => 'status',
            'value'   => $filter['status'],
            'label'   => 'Status Penempatan',
            'width'   => '150px',
            'options' => [
                'aktif'   => 'Aktif',
                'selesai' => 'Selesai',
                'semua'   => 'Semua',
            ],
        ],
    ],
    'resetUrl'          => $resetUrl,
    'actions'           => '<a href="' . $aksiExcel . '" class="btn btn-sm btn-outline-success">Export Excel</a>'
        . '<a href="' . $aksiPdf . '" class="btn btn-sm btn-outline-danger" target="_blank">Export PDF</a>',
]) ?>
