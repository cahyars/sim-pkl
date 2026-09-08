<?php
$userId      = session()->get('user_id');
$notifModel  = new App\Models\NotifikasiModel();
$belumDibaca = $userId ? $notifModel->belumDibaca($userId) : 0;
$daftar      = $userId ? $notifModel->terbaru($userId, 8) : [];
$variant     = $variant ?? 'dark';
$btnClass    = $variant === 'light' ? 'btn-outline-light' : 'btn-outline-secondary';
?>
<div class="dropdown" data-poll-url="<?= site_url('notifikasi/unread') ?>">
    <button class="btn btn-sm <?= $btnClass ?> notif-bell-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifikasi">
        &#128276;
        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge <?= $belumDibaca > 0 ? '' : 'd-none' ?>">
            <?= $belumDibaca > 9 ? '9+' : $belumDibaca ?>
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px; max-width: 90vw;">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <span class="fw-semibold small">Notifikasi</span>
            <a href="<?= site_url('notifikasi/baca-semua') ?>" id="notifBacaSemua" class="small text-decoration-none <?= $belumDibaca > 0 ? '' : 'd-none' ?>">Tandai semua dibaca</a>
        </div>
        <div id="notifList" style="max-height: 340px; overflow-y: auto;">
            <?php if (empty($daftar)): ?>
                <div class="notif-empty">Belum ada notifikasi.</div>
            <?php else: ?>
                <?php foreach ($daftar as $n): ?>
                    <a href="<?= site_url('notifikasi/' . $n['id'] . '/baca') ?>" class="dropdown-item notif-item <?= ! $n['is_read'] ? 'is-unread' : '' ?>">
                        <div class="fw-semibold small"><?= esc($n['judul']) ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= esc($n['pesan']) ?></div>
                        <div class="text-muted mt-1" style="font-size: 0.68rem;"><?= tanggal_indo(substr($n['created_at'], 0, 10)) ?></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
