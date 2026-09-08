<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <a href="<?= site_url('admin/laporan/monitoring') ?>" class="text-decoration-none text-body">
            <div class="info-card h-100">
                <div class="mb-2" style="font-size: 1.6rem;">&#128203;</div>
                <div class="fw-semibold mb-1">Laporan Monitoring PKL</div>
                <div class="text-muted small">Progres logbook per siswa, kelas, tempat PKL, dan periode.</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= site_url('admin/laporan/sebaran') ?>" class="text-decoration-none text-body">
            <div class="info-card h-100">
                <div class="mb-2" style="font-size: 1.6rem;">&#127970;</div>
                <div class="fw-semibold mb-1">Laporan Sebaran Siswa</div>
                <div class="text-muted small">Sebaran siswa PKL aktif per tempat DU/DI beserta kuota.</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= site_url('admin/laporan/nilai') ?>" class="text-decoration-none text-body">
            <div class="info-card h-100">
                <div class="mb-2" style="font-size: 1.6rem;">&#127942;</div>
                <div class="fw-semibold mb-1">Rekap Nilai Akhir</div>
                <div class="text-muted small">Nilai per aspek, nilai akhir, dan predikat seluruh siswa.</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= site_url('admin/laporan/kehadiran') ?>" class="text-decoration-none text-body">
            <div class="info-card h-100">
                <div class="mb-2" style="font-size: 1.6rem;">&#128197;</div>
                <div class="fw-semibold mb-1">Rekap Kehadiran &amp; Keaktifan</div>
                <div class="text-muted small">Perbandingan hari kerja PKL dengan hari logbook terisi.</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?= site_url('admin/laporan/plotting') ?>" class="text-decoration-none text-body">
            <div class="info-card h-100 border-success border-2">
                <div class="mb-2" style="font-size: 1.6rem;">&#128221;</div>
                <div class="fw-semibold mb-1 text-success">Rekap Plotting PKL (Format P2)</div>
                <div class="text-muted small">Daftar siswa, pembimbing, dan tempat PKL berformat resmi Data PKL P2.</div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
