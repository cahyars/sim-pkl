<?php
/**
 * Toolbar pencarian + filter untuk halaman daftar/tabel.
 *
 * Semua pencarian & filter dikirim sebagai query string GET supaya diproses
 * server-side (paginate + WHERE), bukan memfilter data yang sudah dimuat di
 * browser. Variabel yang diharapkan (semuanya opsional):
 *
 * - $searchName        : nama input pencarian (default 'q'); beri '' untuk menyembunyikan
 * - $searchValue       : nilai keyword saat ini
 * - $searchPlaceholder : placeholder input pencarian
 * - $searchWidth       : lebar CSS input pencarian (default 240px)
 * - $filters           : array of [
 *                            'name'    => 'status',
 *                            'value'   => $status,
 *                            'options' => ['aktif' => 'Aktif', ...],
 *                            'empty'   => 'Semua Status',   // opsional, tanpa ini tidak ada opsi kosong
 *                            'width'   => '170px',          // opsional
 *                            'type'    => 'select'|'date',  // default select
 *                            'label'   => 'Dari',           // opsional, untuk type date
 *                        ]
 * - $hidden            : array pasangan nama => nilai yang ikut dikirim sebagai hidden input
 * - $actions           : HTML tombol aksi di sisi kanan (mis. tombol Tambah)
 * - $resetUrl          : URL untuk tombol "Reset"; tombol muncul jika ada filter/keyword aktif
 */
$searchName        = $searchName ?? 'q';
$searchValue       = $searchValue ?? '';
$searchPlaceholder = $searchPlaceholder ?? 'Cari...';
$searchWidth       = $searchWidth ?? '240px';
$filters           = $filters ?? [];
$hidden            = $hidden ?? [];
$actions           = $actions ?? '';
$resetUrl          = $resetUrl ?? '';

$adaFilterAktif = ($searchName !== '' && $searchValue !== '');

foreach ($filters as $f) {
    if (($f['value'] ?? '') !== '') {
        $adaFilterAktif = true;
    }
}
?>
<div class="table-toolbar">
    <form method="get" class="table-toolbar-form" data-auto-search>
        <?php foreach ($hidden as $nama => $nilai): ?>
            <input type="hidden" name="<?= esc($nama, 'attr') ?>" value="<?= esc($nilai, 'attr') ?>">
        <?php endforeach; ?>

        <?php if ($searchName !== ''): ?>
            <div class="table-search" style="width: <?= esc($searchWidth, 'attr') ?>;">
                <svg class="table-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>
                </svg>
                <input
                    type="search"
                    name="<?= esc($searchName, 'attr') ?>"
                    class="form-control form-control-sm table-search-input"
                    placeholder="<?= esc($searchPlaceholder, 'attr') ?>"
                    value="<?= esc($searchValue) ?>"
                    autocomplete="off"
                    aria-label="<?= esc($searchPlaceholder, 'attr') ?>">
            </div>
        <?php endif; ?>

        <?php foreach ($filters as $f): ?>
            <?php $tipe = $f['type'] ?? 'select'; ?>
            <?php if ($tipe === 'date'): ?>
                <div class="table-filter-date">
                    <?php if (! empty($f['label'])): ?>
                        <span class="table-filter-label"><?= esc($f['label']) ?></span>
                    <?php endif; ?>
                    <input
                        type="date"
                        name="<?= esc($f['name'], 'attr') ?>"
                        class="form-select form-select-sm"
                        style="width: <?= esc($f['width'] ?? '150px', 'attr') ?>;"
                        value="<?= esc($f['value'] ?? '') ?>"
                        aria-label="<?= esc($f['label'] ?? $f['name'], 'attr') ?>">
                </div>
            <?php else: ?>
                <select
                    name="<?= esc($f['name'], 'attr') ?>"
                    class="form-select form-select-sm"
                    style="width: <?= esc($f['width'] ?? '170px', 'attr') ?>;"
                    aria-label="<?= esc($f['label'] ?? $f['name'], 'attr') ?>">
                    <?php if (isset($f['empty'])): ?>
                        <option value=""><?= esc($f['empty']) ?></option>
                    <?php endif; ?>
                    <?php foreach (($f['options'] ?? []) as $val => $label): ?>
                        <option value="<?= esc($val, 'attr') ?>" <?= (string) ($f['value'] ?? '') === (string) $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>

        <?php if ($resetUrl !== '' && $adaFilterAktif): ?>
            <a href="<?= esc($resetUrl) ?>" class="btn btn-sm btn-link text-decoration-none px-1">Reset</a>
        <?php endif; ?>
    </form>

    <?php if ($actions !== ''): ?>
        <div class="table-toolbar-actions"><?= $actions ?></div>
    <?php endif; ?>
</div>
