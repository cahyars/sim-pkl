<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumentasiLogbookModel extends Model
{
    protected $table         = 'dokumentasi_logbook';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['logbook_id', 'file_path', 'file_name', 'file_type', 'file_size', 'uploaded_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'uploaded_at';
    protected $updatedField  = '';

    public function byLogbook(int $logbookId): array
    {
        return $this->where('logbook_id', $logbookId)->orderBy('id', 'ASC')->findAll();
    }
}
