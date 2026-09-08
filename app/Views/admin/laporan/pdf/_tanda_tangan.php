<table width="100%" style="margin-top: 30px; font-size: 10px;" cellpadding="0" cellspacing="0">
    <tr>
        <td width="60%"></td>
        <td width="40%" style="text-align: center;">
            <div>Subang, <?= tanggal_indo(date('Y-m-d')) ?></div>
            <div>Kepala Sekolah,</div>
            <div style="height: 55px;"></div>
            <div style="font-weight: bold; text-decoration: underline;"><?= esc($sekolah['kepala'] ?: '(...........................)') ?></div>
        </td>
    </tr>
</table>
