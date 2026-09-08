Yang perlu jalan (2 servis)

1. MySQL/MariaDB — via XAMPP:

sudo /opt/lampp/lampp startmysql
Cek statusnya:

/opt/lampp/lampp status 2. Web server CI4 — dari folder project:

cd /home/cahya/Documents/JOKI/WEB
php spark serve --port 1945
Ini jalan di foreground (nge-block terminal, log request keliatan langsung). Tekan Ctrl+C buat matiin.

Kalau mau jalan di background (biar terminal bisa dipakai hal lain):

cd /home/cahya/Documents/JOKI/WEB
nohup php spark serve --port 1945 > /tmp/simpkl-serve.log 2>&1 &
Matiinnya:

pkill -f "spark serve"
Atau kalau mau spesifik per port (lsof belum terinstall di mesin ini, pakai fuser):

fuser -k 1945/tcp
Lalu buka http://localhost:1945 di browser.

⚠️ Port di `php spark serve` harus sama dengan `app.baseURL` di file `.env` (sekarang `http://localhost:1945/`). Kalau beda, semua redirect CI4 (misal waktu belum login) akan melempar browser ke port yang salah. Port 8080 sekarang dipakai Open WebUI, jadi jangan dipakai untuk project ini.

Kalau mau reset database ke kondisi awal (data dummy bersih)

cd /home/cahya/Documents/JOKI/WEB
php spark migrate:refresh --all
php spark db:seed DatabaseSeeder
⚠️ Ini menghapus semua data, termasuk logbook yang lo isi manual dari browser kemarin. Pakai kalau memang mau mulai bersih lagi.
