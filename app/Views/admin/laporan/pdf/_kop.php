<?php
/**
 * Kop surat bersama untuk semua laporan PDF. Variabel yang diharapkan:
 * $sekolah (nama, alamat, kepala, logo), $judulLaporan, $subjudul (array, opsional)
 */
?>
<table width="100%" style="border-bottom: 3px solid #0d5c63; margin-bottom: 14px;" cellpadding="0" cellspacing="0">
    <tr>
        <td width="70" style="vertical-align: middle; padding-bottom: 8px;">
            <?php if (is_file($sekolah['logo'])): ?>
                <img src="<?= $sekolah['logo'] ?>" style="height: 55px;">
            <?php endif; ?>
        </td>
        <td style="vertical-align: middle; padding-bottom: 8px;">
            <div style="font-size: 15px; font-weight: bold; color: #093f44;"><?= esc($sekolah['nama']) ?></div>
            <div style="font-size: 9px; color: #555;"><?= esc($sekolah['alamat']) ?></div>
        </td>
    </tr>
</table>

<h3 style="text-align: center; margin: 4px 0 2px; font-size: 14px;"><?= esc($judulLaporan) ?></h3>

<?php if (! empty($subjudul)): ?>
    <div style="text-align: center; font-size: 9px; color: #666; margin-bottom: 14px;">
        <?= esc(implode('  |  ', $subjudul)) ?>
    </div>
<?php else: ?>
    <div style="margin-bottom: 10px;"></div>
<?php endif; ?>
