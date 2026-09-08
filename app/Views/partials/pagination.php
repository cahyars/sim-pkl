<?php
/**
 * Partial pagination Bootstrap 5. Variabel yang diharapkan:
 * - $pager : CodeIgniter\Pager\Pager
 * - $pagerGroup : string, nama group paginate() yang dipakai controller
 * - $pagerKeep : array daftar nama query string yang harus dipertahankan (opsional)
 * - $pagerLabel : label entitas untuk teks ringkasan, mis. 'siswa' (opsional)
 *
 * Nomor halaman dirender dengan window + ellipsis supaya tidak meledak jadi
 * ribuan link ketika datanya banyak.
 */
$pagerGroup = $pagerGroup ?? 'default';
$pagerLabel = $pagerLabel ?? 'data';

if (! empty($pagerKeep)) {
    $pager->only($pagerKeep);
}

$totalPages = $pager->getPageCount($pagerGroup);
$current    = $pager->getCurrentPage($pagerGroup);
$total      = $pager->getTotal($pagerGroup);
$perPage    = $pager->getPerPage($pagerGroup);

$dari    = $total > 0 ? (($current - 1) * $perPage) + 1 : 0;
$hingga  = min($current * $perPage, $total);

// Window nomor halaman: selalu tampilkan halaman pertama, terakhir, dan 2
// halaman di sekitar halaman aktif. Sisanya diringkas jadi ellipsis.
$window = 2;
$pages  = [];

for ($i = 1; $i <= $totalPages; $i++) {
    if ($i === 1 || $i === $totalPages || abs($i - $current) <= $window) {
        $pages[] = $i;
    }
}
?>
<div class="table-pager">
    <div class="table-pager-info">
        <?php if ($total > 0): ?>
            Menampilkan <strong><?= number_format($dari, 0, ',', '.') ?>&ndash;<?= number_format($hingga, 0, ',', '.') ?></strong>
            dari <strong><?= number_format($total, 0, ',', '.') ?></strong> <?= esc($pagerLabel) ?>
        <?php else: ?>
            Tidak ada <?= esc($pagerLabel) ?> yang cocok.
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Navigasi halaman">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $current > 1 ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= $current > 1 ? esc($pager->getPreviousPageURI($pagerGroup)) : '#' ?>" aria-label="Halaman sebelumnya">&laquo;</a>
                </li>

                <?php $sebelumnya = 0; ?>
                <?php foreach ($pages as $i): ?>
                    <?php if ($sebelumnya && $i - $sebelumnya > 1): ?>
                        <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                    <?php endif; ?>
                    <li class="page-item <?= $i === $current ? 'active' : '' ?>">
                        <a class="page-link" href="<?= esc($pager->getPageURI($i, $pagerGroup)) ?>" <?= $i === $current ? 'aria-current="page"' : '' ?>><?= $i ?></a>
                    </li>
                    <?php $sebelumnya = $i; ?>
                <?php endforeach; ?>

                <li class="page-item <?= $pager->hasMore($pagerGroup) ? '' : 'disabled' ?>">
                    <a class="page-link" href="<?= $pager->hasMore($pagerGroup) ? esc($pager->getNextPageURI($pagerGroup)) : '#' ?>" aria-label="Halaman selanjutnya">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
