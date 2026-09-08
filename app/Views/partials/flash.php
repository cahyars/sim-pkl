<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="toast align-items-center text-bg-success border-0" role="status" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">&#10003;&nbsp; <?= esc(session()->getFlashdata('success')) ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="toast align-items-center text-bg-danger border-0" role="alert" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">&#9888;&nbsp; <?= esc(session()->getFlashdata('error')) ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    <?php endif; ?>
</div>
