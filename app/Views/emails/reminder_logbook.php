<div style="font-family: Arial, Helvetica, sans-serif; max-width: 560px; margin: 0 auto; color: #1f2a2c;">
    <div style="background: #0d5c63; padding: 20px 24px; border-radius: 12px 12px 0 0;">
        <span style="color: #fff; font-size: 16px; font-weight: 700;">SIM-PKL SMK Negeri 1 Subang</span>
    </div>
    <div style="border: 1px solid #e6e9eb; border-top: none; border-radius: 0 0 12px 12px; padding: 24px;">
        <h2 style="margin-top: 0; font-size: 18px;">Reminder Pengisian Logbook PKL</h2>

        <p>Halo <strong><?= esc($nama) ?></strong>,</p>

        <?php if ($untukGuru): ?>
            <p>
                Siswa bimbingan Anda, <strong><?= esc($namaSiswa) ?></strong> (<?= esc($nisSiswa) ?>),
                <?php if ($hariTerakhir): ?>
                    terakhir mengisi logbook pada <strong><?= esc(tanggal_indo($hariTerakhir)) ?></strong>
                    (<?= (int) $selisih ?> hari yang lalu) dan belum mengisi logbook lagi hingga saat ini.
                <?php else: ?>
                    belum pernah mengisi logbook PKL sama sekali sejak ditempatkan.
                <?php endif; ?>
            </p>
            <p>Mohon pantau dan ingatkan siswa yang bersangkutan untuk segera mengisi logbook aktivitas hariannya.</p>
        <?php else: ?>
            <p>
                <?php if ($hariTerakhir): ?>
                    Sistem mencatat logbook terakhir kamu diisi pada <strong><?= esc(tanggal_indo($hariTerakhir)) ?></strong>
                    (<?= (int) $selisih ?> hari yang lalu).
                <?php else: ?>
                    Sistem mencatat kamu belum pernah mengisi logbook PKL sama sekali.
                <?php endif; ?>
            </p>
            <p>Segera isi logbook aktivitas harian kamu melalui SIM-PKL agar guru pembimbing dapat memantau progres PKL kamu dengan baik.</p>
        <?php endif; ?>

        <p style="text-align: center; margin: 28px 0;">
            <a href="<?= esc($link) ?>" style="background: #0d5c63; color: #fff; text-decoration: none; padding: 10px 22px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                Buka SIM-PKL
            </a>
        </p>

        <hr style="border: none; border-top: 1px solid #e6e9eb; margin: 24px 0;">
        <p style="font-size: 12px; color: #667479; margin: 0;">
            Email ini dikirim otomatis oleh sistem SIM-PKL SMK Negeri 1 Subang. Mohon tidak membalas email ini.
        </p>
    </div>
</div>
