<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIM-PKL SMKN 1 Subang</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo-sekolah.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-brand">
            <img src="<?= base_url('assets/img/logo-sekolah.png') ?>" alt="Logo SMKN 1 Subang" class="brand-logo-lg">
            <div>
                <div class="auth-brand-title">SIM-PKL</div>
                <div class="auth-brand-subtitle">SMK Negeri 1 Subang</div>
            </div>
        </div>

        <!-- Alert Notifikasi Login Gagal / Peringatan -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2 mb-3 shadow-sm border-danger-subtle" role="alert">
                <svg class="flex-shrink-0 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="small fw-semibold text-danger-emphasis">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <!-- Alert Validasi Input -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm" role="alert">
                <ul class="mb-0 ps-3 small">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <!-- Alert Notifikasi Sukses / Logout -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm border-success-subtle" role="alert">
                <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div class="small text-success-emphasis">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username / Email</label>
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    value="<?= esc(old('username')) ?>"
                    placeholder="Contoh: 2324001 atau admin"
                    autofocus
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <!-- Pilihan Periode Tahun Ajaran (Khusus Admin / Akses Arsip) -->
            <?php
            $daftarTA = $daftarTahunAjaran ?? (new \App\Models\TahunAjaranModel())->orderBy('is_active', 'DESC')->orderBy('nama_tahun_ajaran', 'DESC')->findAll();
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="tahun_ajaran_id" class="form-label mb-0">Tahun Ajaran / Periode</label>
                    <span class="badge text-bg-light border text-muted" style="font-size: 0.68rem;">Khusus Arsip Admin</span>
                </div>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select form-select-sm">
                    <?php foreach ($daftarTA as $ta): ?>
                        <option value="<?= $ta['id'] ?>" <?= (int) $ta['is_active'] === 1 ? 'selected' : '' ?>>
                            <?= esc($ta['nama_tahun_ajaran']) ?> &ndash; <?= esc(ucfirst($ta['semester'])) ?> <?= (int) $ta['is_active'] === 1 ? '(Periode Aktif)' : '(Arsip)' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text text-muted" style="font-size: 0.72rem; line-height: 1.35;">
                    Admin dapat memilih tahun ajaran sebelumnya untuk meninjau data arsip penempatan, penilaian, dan siswa PKL.
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" style="background-color: var(--simpkl-primary); border-color: var(--simpkl-primary);">
                Masuk
            </button>
        </form>

        <div class="auth-demo-hint">
            Lupa akun/password? Hubungi Admin/Tata Usaha PKL sekolah.
        </div>
    </div>
</div>

<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
