<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenempatanPklTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tempat_pkl_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_pembimbing_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pembimbing_lapangan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tanggal_mulai'          => ['type' => 'DATE'],
            'tanggal_selesai'        => ['type' => 'DATE'],
            'status'                 => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'selesai', 'dibatalkan'],
                'default'    => 'aktif',
            ],
            'keterangan'             => ['type' => 'TEXT', 'null' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('siswa_id');
        $this->forge->addKey('tempat_pkl_id');
        $this->forge->addKey('guru_pembimbing_id');
        $this->forge->addKey('pembimbing_lapangan_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tempat_pkl_id', 'tempat_pkl', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('guru_pembimbing_id', 'guru_pembimbing', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('pembimbing_lapangan_id', 'pembimbing_lapangan', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('penempatan_pkl');
    }

    public function down()
    {
        $this->forge->dropTable('penempatan_pkl');
    }
}
