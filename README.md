# SIM-PKL — Sistem Informasi Monitoring PKL SMKN 1 Subang

Aplikasi web untuk mendigitalisasi pemantauan Praktik Kerja Lapangan (PKL) siswa SMK:
penempatan siswa, logbook harian, validasi berjenjang oleh guru pembimbing, penilaian akhir
oleh pembimbing lapangan (DU/DI), notifikasi otomatis, sampai rekap & ekspor laporan.

Dokumentasi kebutuhan lengkap ada di folder [docs/](docs/).

---

## Tech Stack

| Komponen          | Versi / Paket                              |
| ----------------- | ------------------------------------------ |
| PHP               | 8.2                                        |
| Framework         | CodeIgniter 4.7                            |
| Database          | MySQL / MariaDB                            |
| UI                | Bootstrap 5.3 (self-hosted, tanpa CDN)     |
| Font              | Inter (self-hosted, variable font)         |
| Auth              | Session-based + RBAC via CI4 Filter        |
| Export Excel      | PhpSpreadsheet                             |
| Export PDF        | Dompdf                                     |
| Notifikasi Email  | CI4 Email Service + Spark Command/Cron     |
| Notifikasi In-App | Polling AJAX (~20 detik) via endpoint JSON |
| Grafik            | Chart.js 4.4 (self-hosted)                 |

Bootstrap dan font di-hosting lokal (bukan lewat CDN) supaya aplikasi tetap jalan normal
walau server sekolah tidak punya akses internet keluar.

---

## Fitur

### Siswa (mobile-first)

- Isi logbook harian (draft/kirim), edit ulang saat status "Revisi"
- Upload dokumentasi kegiatan (multi-file, validasi tipe & ukuran)
- Lihat riwayat logbook dengan filter status & tanggal
- Lihat nilai akhir PKL (setelah difinalisasi pembimbing lapangan)
- Update profil (foto, no. HP, alamat) & ganti password

### Guru Pembimbing

- Dashboard progres siswa bimbingan: **grafik batang** persentase logbook disetujui per siswa
  + **grafik donat** komposisi status logbook, plus indikator "belum aktif"
- Validasi logbook: setujui / minta revisi / tolak, dengan catatan
- Lihat detail & riwayat logbook per siswa bimbingan
- Export laporan monitoring (Excel/PDF), otomatis ter-scope ke siswa bimbingan sendiri

### Pembimbing Lapangan (DU/DI)

- Lihat siswa bimbingan & rekap logbook (read-only)
- Input penilaian akhir per aspek (bobot dinamis, dikonfigurasi Admin)
- Simpan draft atau finalisasi (nilai final terkunci, tidak bisa diedit lagi)
- Feedback kualitatif ke siswa

### Admin / Tata Usaha PKL

- CRUD data siswa (+ import Excel), guru pembimbing, tempat PKL, pembimbing lapangan
- Assign & kelola penempatan PKL (riwayat, selesai, batalkan)
- Kelola tahun ajaran/periode PKL
- Kelola aspek penilaian (nama, deskripsi, bobot, aktif/nonaktif) — form penilaian
  pembimbing lapangan otomatis mengikuti aspek yang aktif
- Kelola akun pengguna semua role: reset password & aktivasi/nonaktifkan akun
- Pengaturan sistem (jangka waktu reminder, batas upload, identitas sekolah)
- 4 jenis laporan (Monitoring, Sebaran Siswa, Rekap Nilai, Rekap Kehadiran) × Excel & PDF,
  dengan filter periode/tahun ajaran, kelas, tempat PKL, dan status penempatan

### Sistem

- Reminder email otomatis ke siswa yang belum isi logbook > X hari (+ tembusan guru), via
  `php spark send:reminder`, dengan guard anti-kirim-ganda per hari
- Notifikasi in-app (lonceng) untuk semua role, update otomatis tanpa reload halaman

---

## Instalasi

```bash
# 1. Install dependency
composer install

# 2. Siapkan konfigurasi
cp env .env      # lalu sesuaikan bagian database & email

# 3. Buat database
mysql -u root -e "CREATE DATABASE simpkl_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# 4. Jalankan migrasi & data awal
php spark migrate
php spark db:seed DatabaseSeeder

# 5. Jalankan server
php spark serve --port 1945
```

Aplikasi berjalan di <http://localhost:1945>.

> **Penting:** port pada `php spark serve` harus sama dengan `app.baseURL` di `.env`
> (default project ini `http://localhost:1945/`). Kalau tidak sama, setiap redirect
> CodeIgniter akan mengarahkan browser ke port yang salah.

> **Catatan lingkungan VS Code Flatpak:** kalau `php` di terminal biasa (bukan lewat
> Claude Code) muncul error `flatpak-spawn: not found` atau `php: command not found`,
> pakai path lengkap ke binary PHP host: `/opt/lampp/bin/php spark ...` — lihat penjelasan
> lengkap di riwayat chat pengembangan.

### Reset database ke kondisi awal

```bash
php spark migrate:refresh --all
php spark db:seed DatabaseSeeder
```

⚠️ Perintah ini menghapus **semua data**, termasuk yang diisi manual lewat browser.

---

## Notifikasi Email

Default `.env` memakai `email.protocol = 'mail'` (butuh MTA/sendmail lokal — biasanya
tidak tersedia di mesin development). Untuk email beneran terkirim, isi kredensial SMTP
di `.env`, misalnya pakai Gmail:

```
email.protocol = 'smtp'
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'akun-anda@gmail.com'
email.SMTPPass = 'app-password-16-karakter'   # bukan password akun biasa
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

App Password Gmail dibuat lewat <https://myaccount.google.com/apppasswords> (perlu
2-Step Verification aktif).

### Jadwalkan reminder otomatis harian

```bash
php spark send:reminder
```

Tambahkan ke crontab server produksi agar berjalan otomatis tiap hari, misal jam 07:00:

```
0 7 * * * cd /path/ke/project && php spark send:reminder >> writable/logs/reminder.log 2>&1
```

Pengaturan jangka waktu reminder (`reminder_hari`), tembusan ke guru, dan aktif/nonaktif
reminder bisa diubah lewat menu **Pengaturan** di dashboard Admin — tidak perlu ubah kode.

---

## Akun Demo

Seluruh akun demo memakai password: **`password123`**

| Role                | Username   | Nama                   | Keterangan                             |
| ------------------- | ---------- | ---------------------- | -------------------------------------- |
| Admin / Tata Usaha  | `admin`    | Tata Usaha PKL         | Akses penuh data master                |
| Guru Pembimbing     | `dedi.k`   | Dedi Kurniawan, S.Kom. | Membimbing 5 siswa RPL                 |
| Guru Pembimbing     | `sri.w`    | Sri Wahyuni, S.Pd.     | Membimbing 4 siswa TKJ                 |
| Guru Pembimbing     | `agus.s`   | Agus Setiawan, S.T.    | Membimbing 3 siswa TKR                 |
| Guru Pembimbing     | `rina.m`   | Rina Marlina, S.E.     | Membimbing 3 siswa AKL                 |
| Pembimbing Lapangan | `hendra.g` | Hendra Gunawan         | PT Sinar Digital Nusantara             |
| Pembimbing Lapangan | `yusuf.r`  | Yusuf Ramdani          | CV Jaringan Prima Subang               |
| Pembimbing Lapangan | `tono.s`   | Tono Suratno           | Bengkel Resmi Auto Sejahtera           |
| Pembimbing Lapangan | `lilis.s`  | Lilis Suryani          | Koperasi Simpan Pinjam Mitra Warga     |
| Pembimbing Lapangan | `rizal.f`  | Rizal Fadillah         | Diskominfo Kabupaten Subang            |
| Siswa               | `2324001`  | Ahmad Fauzi Ramadhan   | Rajin mengisi, penilaian sudah final   |
| Siswa               | `2324009`  | Irfan Maulana          | Menunggak logbook (uji reminder)       |
| Siswa               | `2324015`  | Oki Setiawan           | Belum pernah mengisi logbook           |
| Siswa               | `2324016`  | Putri Rahmawati        | Belum ditempatkan (uji blokir logbook) |

Username siswa memakai NIS (`2324001` s.d. `2324018`).

---

## Struktur Data

14 tabel sesuai ERD pada [docs/4.Entity_relationship_diagram.md](docs/4.Entity_relationship_diagram.md):

`users`, `tahun_ajaran`, `tempat_pkl`, `siswa`, `guru_pembimbing`, `pembimbing_lapangan`,
`penempatan_pkl`, `logbook`, `dokumentasi_logbook`, `aspek_penilaian`, `penilaian`,
`detail_penilaian`, `notifikasi`, `pengaturan`.

Tabel `pengaturan` adalah tambahan di luar ERD untuk menampung konfigurasi sistem
(jangka waktu reminder, batas ukuran upload, identitas sekolah pada kop laporan)
sesuai hak akses Admin §3.4.

### Aturan bisnis yang dikunci di level database

- `logbook` unik per (`siswa_id`, `tanggal_kegiatan`) — satu siswa satu logbook per hari.
- `penilaian` unik per `siswa_id` — satu siswa satu penilaian per periode PKL.
- `tempat_pkl` dan `guru_pembimbing` memakai `ON DELETE RESTRICT` pada `penempatan_pkl`,
  sehingga tidak bisa dihapus selama masih dipakai penempatan.

### Proteksi akses (defense in depth)

Setiap query data siswa/logbook/penilaian di modul Guru dan Pembimbing Lapangan
selalu di-scope lewat `JOIN penempatan_pkl ... WHERE guru_pembimbing_id/pembimbing_lapangan_id = <profil yang login>`
di level query — bukan cuma disembunyikan di tampilan. File dokumentasi (logbook, foto
profil) disimpan di `writable/` (di luar document root) dan hanya bisa diakses lewat
controller yang memverifikasi kepemilikan.

---

## Perintah Spark Kustom

```bash
php spark simpkl:cek-model      # verifikasi seluruh Model & query relasinya
php spark send:reminder         # kirim reminder logbook (lihat bagian Notifikasi Email)
```

---

## Status Pengembangan

| Tahap | Lingkup                                                        | Status     |
| ----- | -------------------------------------------------------------- | ---------- |
| 1     | Fondasi: setup CI4, migrasi 14 tabel, model, seeder            | ✅ Selesai |
| 2     | Autentikasi & RBAC                                             | ✅ Selesai |
| 3     | Modul Admin — data master & penempatan                         | ✅ Selesai |
| 4     | Modul Siswa — logbook harian                                   | ✅ Selesai |
| 5     | Modul Guru Pembimbing — validasi & dashboard                   | ✅ Selesai |
| 6     | Modul Pembimbing Lapangan — penilaian akhir                    | ✅ Selesai |
| 7     | Notifikasi otomatis                                            | ✅ Selesai |
| 8     | Rekap & ekspor laporan                                         | ✅ Selesai |
| 9     | Finalisasi: profil siswa, tahun ajaran, dokumentasi, pengujian | ✅ Selesai |

Hasil pengujian black box lengkap (34 skenario) ada di
[docs/8.Blackbox_testing.md](docs/8.Blackbox_testing.md).

### Yang belum masuk scope (kandidat pengembangan lanjutan)

- Kredensial SMTP produksi (perlu diisi manual sebelum deploy — lihat bagian Notifikasi Email)
- Riwayat/log perubahan data (audit trail)
- Multi-bahasa (saat ini Bahasa Indonesia saja)
