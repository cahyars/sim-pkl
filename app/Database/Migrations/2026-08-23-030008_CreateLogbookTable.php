<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogbookTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'penempatan_pkl_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_kegiatan'  => ['type' => 'DATE'],
            'jam_mulai'         => ['type' => 'TIME'],
            'jam_selesai'       => ['type' => 'TIME'],
            'uraian_kegiatan'   => ['type' => 'TEXT'],
            'kendala'           => ['type' => 'TEXT', 'null' => true],
            'status'            => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'menunggu_validasi', 'disetujui', 'revisi', 'ditolak'],
                'default'    => 'draft',
            ],
            'catatan_guru'      => ['type' => 'TEXT', 'null' => true],
            'validated_by'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'validated_at'      => ['type' => 'DATETIME', 'null' => true],
            'submitted_at'      => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('siswa_id');
        $this->forge->addKey('penempatan_pkl_id');
        $this->forge->addKey('status');
        $this->forge->addKey('tanggal_kegiatan');
        // Satu siswa hanya boleh punya satu logbook per tanggal kegiatan.
        $this->forge->addUniqueKey(['siswa_id', 'tanggal_kegiatan']);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('penempatan_pkl_id', 'penempatan_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('validated_by', 'guru_pembimbing', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('logbook');
    }

    public function down()
    {
        $this->forge->dropTable('logbook');
    }
}
