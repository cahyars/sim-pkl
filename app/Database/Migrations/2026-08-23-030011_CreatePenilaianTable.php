<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pembimbing_lapangan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai_akhir'            => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'feedback'               => ['type' => 'TEXT', 'null' => true],
            'status'                 => ['type' => 'ENUM', 'constraint' => ['draft', 'final'], 'default' => 'draft'],
            'tanggal_penilaian'      => ['type' => 'DATE', 'null' => true],
            'finalized_at'           => ['type' => 'DATETIME', 'null' => true],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        // Satu siswa hanya dinilai sekali per periode PKL.
        $this->forge->addUniqueKey('siswa_id');
        $this->forge->addKey('pembimbing_lapangan_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pembimbing_lapangan_id', 'pembimbing_lapangan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('penilaian');
    }
}
