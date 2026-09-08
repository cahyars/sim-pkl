<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="info-card" style="max-width: 640px;">
    <p class="text-muted small">
        Unggah file Excel (.xlsx/.xls) berisi data siswa. Kolom yang dibaca sesuai urutan:
        <strong>NIS, Nama, NISN, Kelas, Jurusan, No HP, Alamat</strong> (baris pertama dianggap header dan dilewati).
        Username &amp; password awal setiap akun otomatis memakai NIS.
    </p>

    <a href="<?= site_url('admin/siswa/template') ?>" class="btn btn-sm btn-outline-secondary mb-3">Unduh Template Excel</a>

    <form action="<?= site_url('admin/siswa/import') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">File Excel</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Import</button>
            <a href="<?= site_url('admin/siswa') ?>" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
