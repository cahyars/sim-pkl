<?php

namespace App\Commands;

use App\Models\LogbookModel;
use App\Models\NotifikasiModel;
use App\Models\PengaturanModel;
use App\Models\SiswaModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Reminder harian untuk siswa PKL yang belum mengisi logbook dalam X hari (Fitur §4.5).
 * Dijalankan manual: php spark send:reminder
 * Dijalankan via cron (contoh setiap hari jam 07:00):
 *   0 7 * * * cd /path/ke/project && php spark send:reminder >> writable/logs/reminder.log 2>&1
 */
class SendLogbookReminder extends BaseCommand
{
    protected $group       = 'SIM-PKL';
    protected $name        = 'send:reminder';
    protected $description = 'Kirim reminder email ke siswa PKL yang belum mengisi logbook dalam X hari.';

    public function run(array $params)
    {
        helper('simpkl');

        $pengaturan = new PengaturanModel();

        if (! (bool) $pengaturan->ambil('reminder_aktif', 1)) {
            CLI::write('Reminder otomatis sedang dinonaktifkan lewat menu Pengaturan. Tidak ada yang dikirim.', 'yellow');

            return;
        }

        $reminderHari = (int) $pengaturan->ambil('reminder_hari', 3);
        $tembusanGuru = (bool) $pengaturan->ambil('reminder_tembusan_guru', 1);
        $hariIni      = date('Y-m-d');

        $daftar = (new SiswaModel())->withUser()
            ->select('penempatan_pkl.id AS penempatan_id, guru_pembimbing.id AS guru_pembimbing_id, ug.nama AS nama_guru, ug.email AS email_guru, ug.id AS user_id_guru')
            ->join('penempatan_pkl', 'penempatan_pkl.siswa_id = siswa.id')
            ->join('guru_pembimbing', 'guru_pembimbing.id = penempatan_pkl.guru_pembimbing_id')
            ->join('users ug', 'ug.id = guru_pembimbing.user_id')
            ->where('penempatan_pkl.status', 'aktif')
            ->where('users.is_active', 1)
            ->findAll();

        CLI::write('Reminder logbook — batas: ' . $reminderHari . ' hari, tembusan guru: ' . ($tembusanGuru ? 'ya' : 'tidak'), 'cyan');
        CLI::write('Memeriksa ' . count($daftar) . ' siswa dengan penempatan aktif...', 'cyan');

        $logbookModel = new LogbookModel();
        $notifModel   = new NotifikasiModel();

        $terkirim = 0;
        $dilewatiSudahIsi = 0;
        $dilewatiSudahDikirim = 0;
        $gagalEmail = 0;

        foreach ($daftar as $siswa) {
            $terakhirIsi = $logbookModel->tanggalLogbookTerakhir((int) $siswa['id']);
            $selisih     = $terakhirIsi ? (int) ((strtotime($hariIni) - strtotime($terakhirIsi)) / 86400) : null;

            if ($selisih !== null && $selisih <= $reminderHari) {
                $dilewatiSudahIsi++;

                continue;
            }

            if ($notifModel->sudahDikirimHariIni((int) $siswa['user_id'], 'reminder_logbook', $hariIni)) {
                $dilewatiSudahDikirim++;

                continue;
            }

            $berhasilEmail = $this->kirimEmailReminder($siswa, $siswa['nama'], false, $terakhirIsi, $selisih, $tembusanGuru ? $siswa['email_guru'] : null);

            $pesanSiswa = $terakhirIsi
                ? "Kamu belum mengisi logbook sejak {$selisih} hari lalu (terakhir " . tanggal_indo($terakhirIsi) . '). Segera lengkapi logbook PKL kamu.'
                : 'Kamu belum pernah mengisi logbook PKL sama sekali. Segera lengkapi logbook PKL kamu.';

            $notifModel->kirim((int) $siswa['user_id'], 'Reminder Isi Logbook', $pesanSiswa, 'reminder_logbook', '/siswa/logbook/tambah', $berhasilEmail);

            if ($tembusanGuru && ! empty($siswa['user_id_guru'])) {
                $pesanGuru = 'Siswa bimbingan Anda, ' . $siswa['nama'] . ' (' . $siswa['nis'] . '), belum mengisi logbook selama '
                    . ($selisih ?? '-') . ' hari.';
                $notifModel->kirim((int) $siswa['user_id_guru'], 'Reminder: Siswa Bimbingan Belum Isi Logbook', $pesanGuru, 'reminder_logbook', '/guru/siswa/' . $siswa['id'], $berhasilEmail);
            }

            if ($berhasilEmail) {
                CLI::write("  [OK] {$siswa['nama']} ({$siswa['nis']}) — email terkirim.", 'green');
                $terkirim++;
            } else {
                CLI::write("  [GAGAL] {$siswa['nama']} ({$siswa['nis']}) — email gagal terkirim, notifikasi in-app tetap tersimpan.", 'red');
                $gagalEmail++;
            }
        }

        CLI::newLine();
        CLI::write('Selesai.', 'yellow');
        CLI::write("  Reminder terkirim   : {$terkirim}");
        CLI::write("  Gagal kirim email   : {$gagalEmail}");
        CLI::write("  Sudah isi logbook   : {$dilewatiSudahIsi}");
        CLI::write("  Sudah dikirim hari ini (dilewati) : {$dilewatiSudahDikirim}");
    }

    private function kirimEmailReminder(array $siswa, string $namaTujuan, bool $untukGuru, ?string $terakhirIsi, ?int $selisih, ?string $ccGuru): bool
    {
        $email = \Config\Services::email(null, false);

        $email->setTo($siswa['email']);

        if (! empty($ccGuru)) {
            $email->setCC($ccGuru);
        }

        $email->setSubject('Reminder: Belum Mengisi Logbook PKL');
        $email->setMessage(view('emails/reminder_logbook', [
            'nama'         => $namaTujuan,
            'namaSiswa'    => $siswa['nama'],
            'nisSiswa'     => $siswa['nis'],
            'untukGuru'    => $untukGuru,
            'hariTerakhir' => $terakhirIsi,
            'selisih'      => $selisih,
            'link'         => site_url('siswa/logbook/tambah'),
        ]));

        try {
            $berhasil = $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Gagal kirim email reminder: ' . $e->getMessage());
            $berhasil = false;
        }

        $email->clear(true);

        return $berhasil;
    }
}
