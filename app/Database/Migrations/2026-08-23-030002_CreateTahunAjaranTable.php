<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAjaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_tahun_ajaran'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'semester'           => ['type' => 'ENUM', 'constraint' => ['ganjil', 'genap']],
            'tanggal_mulai'      => ['type' => 'DATE', 'null' => true],
            'tanggal_selesai'    => ['type' => 'DATE', 'null' => true],
            'is_active'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['nama_tahun_ajaran', 'semester']);
        $this->forge->createTable('tahun_ajaran');
    }

    public function down()
    {
        $this->forge->dropTable('tahun_ajaran');
    }
}
