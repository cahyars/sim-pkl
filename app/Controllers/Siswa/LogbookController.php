<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\DokumentasiLogbookModel;
use App\Models\LogbookModel;
use App\Models\PenempatanPklModel;
use App\Models\PengaturanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class LogbookController extends BaseController
{
    public function index()
    {
        $siswaId = (int) $this->session->get('profile_id');
        $model   = (new LogbookModel())->where('siswa_id', $siswaId);

        $status  = $this->request->getGet('status');
        $dari    = $this->request->getGet('dari');
        $sampai  = $this->request->getGet('sampai');
        $keyword = trim((string) $this->request->getGet('q'));

        if (! empty($status)) {
            $model->where('status', $status);
        }
        if (! empty($dari)) {
            $model->where('tanggal_kegiatan >=', $dari);
        }
        if (! empty($sampai)) {
            $model->where('tanggal_kegiatan <=', $sampai);
        }
        if ($keyword !== '') {
            $model->like('uraian_kegiatan', $keyword);
        }

        $logbook = $model->orderBy('tanggal_kegiatan', 'DESC')->paginate(10, 'logbook');

        return view('siswa/logbook/index', [
            'title'      => 'Logbook Saya',
            'logbook'    => $logbook,
            'pager'      => $model->pager,
            'status'     => $status,
            'dari'       => $dari,
            'sampai'     => $sampai,
            'keyword'    => $keyword,
            'penempatan' => (new PenempatanPklModel())->getAktifBySiswa($siswaId),
        ]);
    }

    public function create()
    {
        $siswaId    = (int) $this->session->get('profile_id');
        $penempatan = (new PenempatanPklModel())->getAktifBySiswa($siswaId);

        if ($penempatan === null) {
            return redirect()->to('/siswa/logbook')
                ->with('error', 'Kamu belum memiliki penempatan PKL aktif. Logbook belum bisa diisi.');
        }

        return view('siswa/logbook/create', [
            'title'      => 'Isi Logbook',
            'penempatan' => $penempatan,
        ]);
    }

    public function store()
    {
        $siswaId    = (int) $this->session->get('profile_id');
        $penempatan = (new PenempatanPklModel())->getAktifBySiswa($siswaId);

        if ($penempatan === null) {
            return redirect()->to('/siswa/logbook')
                ->with('error', 'Kamu belum memiliki penempatan PKL aktif. Logbook belum bisa diisi.');
        }

        $statusKehadiran = $this->request->getPost('status_kehadiran') ?: 'masuk';

        if (! in_array($statusKehadiran, ['masuk', 'izin', 'sakit'], true)) {
            $statusKehadiran = 'masuk';
        }

        $hadir = $statusKehadiran === 'masuk';

        $rules = [
            'tanggal_kegiatan' => 'required|valid_date[Y-m-d]',
            'uraian_kegiatan'  => 'required|min_length[10]',
        ];

        if ($hadir) {
            $rules['jam_mulai']   = 'required';
            $rules['jam_selesai'] = 'required';
        }

        if (! $this->validate($rules, $this->pesanValidasi($hadir))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggal = $this->request->getPost('tanggal_kegiatan');

        if ($tanggal > date('Y-m-d')) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kegiatan tidak boleh lebih dari hari ini.');
        }
        if ($tanggal < $penempatan['tanggal_mulai'] || $tanggal > $penempatan['tanggal_selesai']) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kegiatan harus berada dalam periode PKL kamu (' . tanggal_indo($penempatan['tanggal_mulai']) . ' - ' . tanggal_indo($penempatan['tanggal_selesai']) . ').');
        }
        if ($hadir && $this->request->getPost('jam_selesai') <= $this->request->getPost('jam_mulai')) {
            return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih besar dari jam mulai.');
        }

        $logbookModel = new LogbookModel();

        if ($logbookModel->sudahAdaDiTanggal($siswaId, $tanggal)) {
            return redirect()->back()->withInput()->with('error', 'Kamu sudah mengisi logbook untuk tanggal tersebut. Silakan edit logbook yang sudah ada.');
        }

        $files = $this->request->getFileMultiple('dokumentasi') ?? [];
        [$validFiles, $fileErrors] = $this->validasiFile($files);

        if ($fileErrors !== []) {
            return redirect()->back()->withInput()->with('errors', $fileErrors);
        }

        $aksi   = $this->request->getPost('aksi') === 'kirim' ? 'menunggu_validasi' : 'draft';
        $now    = date('Y-m-d H:i:s');

        $db = db_connect();
        $db->transStart();

        $logbookId = $logbookModel->insert([
            'siswa_id'          => $siswaId,
            'penempatan_pkl_id' => $penempatan['id'],
            'tanggal_kegiatan'  => $tanggal,
            'status_kehadiran'  => $statusKehadiran,
            'jam_mulai'         => $hadir ? $this->request->getPost('jam_mulai') : null,
            'jam_selesai'       => $hadir ? $this->request->getPost('jam_selesai') : null,
            'uraian_kegiatan'   => $this->request->getPost('uraian_kegiatan'),
            'kendala'           => $hadir ? $this->request->getPost('kendala') : null,
            'status'            => $aksi,
            'submitted_at'      => $aksi === 'menunggu_validasi' ? $now : null,
        ], true);

        $this->simpanFile($validFiles, (int) $logbookId);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan logbook. Silakan coba lagi.');
        }

        $pesan = $aksi === 'menunggu_validasi'
            ? 'Logbook berhasil dikirim untuk validasi guru pembimbing.'
            : 'Logbook berhasil disimpan sebagai draft.';

        return redirect()->to('/siswa/logbook')->with('success', $pesan);
    }

    public function show(int $id)
    {
        $logbook = $this->ambilMilikSendiri($id);

        if ($logbook === null) {
            return redirect()->to('/siswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        return view('siswa/logbook/show', [
            'title'        => 'Detail Logbook',
            'logbook'      => $logbook,
            'dokumentasi'  => (new DokumentasiLogbookModel())->byLogbook($id),
        ]);
    }

    public function edit(int $id)
    {
        $logbook = $this->ambilMilikSendiri($id);

        if ($logbook === null) {
            return redirect()->to('/siswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        if (! in_array($logbook['status'], LogbookModel::EDITABLE, true)) {
            return redirect()->to('/siswa/logbook/' . $id)
                ->with('error', 'Logbook berstatus "' . logbook_status_label($logbook['status']) . '" tidak dapat diedit.');
        }

        return view('siswa/logbook/edit', [
            'title'       => 'Edit Logbook',
            'logbook'     => $logbook,
            'dokumentasi' => (new DokumentasiLogbookModel())->byLogbook($id),
        ]);
    }

    public function update(int $id)
    {
        $logbookModel = new LogbookModel();
        $logbook      = $this->ambilMilikSendiri($id);

        if ($logbook === null) {
            return redirect()->to('/siswa/logbook')->with('error', 'Logbook tidak ditemukan.');
        }

        if (! in_array($logbook['status'], LogbookModel::EDITABLE, true)) {
            return redirect()->to('/siswa/logbook/' . $id)
                ->with('error', 'Logbook berstatus "' . logbook_status_label($logbook['status']) . '" tidak dapat diedit.');
        }

        $statusKehadiran = $this->request->getPost('status_kehadiran') ?: 'masuk';

        if (! in_array($statusKehadiran, ['masuk', 'izin', 'sakit'], true)) {
            $statusKehadiran = 'masuk';
        }

        $hadir = $statusKehadiran === 'masuk';

        $rules = [
            'uraian_kegiatan' => 'required|min_length[10]',
        ];

        if ($hadir) {
            $rules['jam_mulai']   = 'required';
            $rules['jam_selesai'] = 'required';
        }

        if (! $this->validate($rules, $this->pesanValidasi($hadir))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($hadir && $this->request->getPost('jam_selesai') <= $this->request->getPost('jam_mulai')) {
            return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih besar dari jam mulai.');
        }

        $files = $this->request->getFileMultiple('dokumentasi') ?? [];
        [$validFiles, $fileErrors] = $this->validasiFile($files);

        if ($fileErrors !== []) {
            return redirect()->back()->withInput()->with('errors', $fileErrors);
        }

        $aksi = $this->request->getPost('aksi') === 'kirim' ? 'menunggu_validasi' : 'draft';
        $now  = date('Y-m-d H:i:s');

        $data = [
            'status_kehadiran' => $statusKehadiran,
            'jam_mulai'        => $hadir ? $this->request->getPost('jam_mulai') : null,
            'jam_selesai'      => $hadir ? $this->request->getPost('jam_selesai') : null,
            'uraian_kegiatan'  => $this->request->getPost('uraian_kegiatan'),
            'kendala'          => $hadir ? $this->request->getPost('kendala') : null,
            'status'           => $aksi,
        ];

        if ($aksi === 'menunggu_validasi') {
            $data['submitted_at'] = $now;
            $data['validated_by'] = null;
            $data['validated_at'] = null;
        }

        $db = db_connect();
        $db->transStart();

        $logbookModel->update($id, $data);
        $this->simpanFile($validFiles, $id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui logbook. Silakan coba lagi.');
        }

        $pesan = $aksi === 'menunggu_validasi'
            ? 'Logbook berhasil dikirim ulang untuk validasi guru pembimbing.'
            : 'Perubahan logbook berhasil disimpan sebagai draft.';

        return redirect()->to('/siswa/logbook')->with('success', $pesan);
    }

    public function hapusDokumentasi(int $logbookId, int $dokId)
    {
        $logbook = $this->ambilMilikSendiri($logbookId);

        if ($logbook === null || ! in_array($logbook['status'], LogbookModel::EDITABLE, true)) {
            return redirect()->to('/siswa/logbook')->with('error', 'Dokumentasi tidak dapat dihapus.');
        }

        $dokModel = new DokumentasiLogbookModel();
        $dok      = $dokModel->find($dokId);

        if ($dok === null || (int) $dok['logbook_id'] !== $logbookId) {
            return redirect()->to('/siswa/logbook/' . $logbookId . '/edit')->with('error', 'Dokumentasi tidak ditemukan.');
        }

        $path = WRITEPATH . 'uploads/dokumentasi/' . $dok['file_path'];
        if (is_file($path)) {
            unlink($path);
        }
        $dokModel->delete($dokId);

        return redirect()->to('/siswa/logbook/' . $logbookId . '/edit')->with('success', 'Dokumentasi berhasil dihapus.');
    }

    public function file(int $dokId)
    {
        $dok = (new DokumentasiLogbookModel())->find($dokId);

        if ($dok === null || $this->ambilMilikSendiri((int) $dok['logbook_id']) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/dokumentasi/' . $dok['file_path'];

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->setContentType($dok['file_type'])->setBody(file_get_contents($path));
    }

    private function pesanValidasi(bool $hadir = true): array
    {
        $labelUraian = $hadir ? 'Uraian kegiatan' : 'Alasan ketidakhadiran';

        return [
            'tanggal_kegiatan' => [
                'required'   => 'Tanggal kegiatan wajib diisi.',
                'valid_date' => 'Format tanggal tidak valid.',
            ],
            'jam_mulai'   => ['required' => 'Jam mulai wajib diisi.'],
            'jam_selesai' => ['required' => 'Jam selesai wajib diisi.'],
            'uraian_kegiatan' => [
                'required'   => "{$labelUraian} wajib diisi.",
                'min_length' => "{$labelUraian} minimal 10 karakter.",
            ],
        ];
    }

    private function ambilMilikSendiri(int $id): ?array
    {
        $siswaId = (int) $this->session->get('profile_id');
        $logbook = (new LogbookModel())->find($id);

        if ($logbook === null || (int) $logbook['siswa_id'] !== $siswaId) {
            return null;
        }

        return $logbook;
    }

    /**
     * @return array{0: array, 1: array<int, string>} [file yang valid, pesan error]
     */
    private function validasiFile(array $files): array
    {
        $pengaturan  = new PengaturanModel();
        $allowedExt  = array_map('trim', explode(',', $pengaturan->ambil('upload_allowed_ext', 'jpg,jpeg,png,pdf')));
        $maxSizeKb   = (int) $pengaturan->ambil('upload_max_size', 2048);

        $valid  = [];
        $errors = [];

        foreach ($files as $file) {
            if ($file === null || ! $file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientExtension());

            if (! in_array($ext, $allowedExt, true)) {
                $errors[] = "File \"{$file->getClientName()}\": format .{$ext} tidak didukung. Format yang diizinkan: " . implode(', ', $allowedExt) . '.';
                continue;
            }

            if ($file->getSize() > $maxSizeKb * 1024) {
                $errors[] = "File \"{$file->getClientName()}\": ukuran melebihi {$maxSizeKb} KB.";
                continue;
            }

            $valid[] = $file;
        }

        return [$valid, $errors];
    }

    private function simpanFile(array $files, int $logbookId): void
    {
        if ($files === []) {
            return;
        }

        $dokModel = new DokumentasiLogbookModel();

        foreach ($files as $file) {
            $namaBaru = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/dokumentasi', $namaBaru);

            $dokModel->insert([
                'logbook_id' => $logbookId,
                'file_path'  => $namaBaru,
                'file_name'  => $file->getClientName(),
                'file_type'  => $file->getClientMimeType(),
                'file_size'  => $file->getSize(),
            ]);
        }
    }
}
