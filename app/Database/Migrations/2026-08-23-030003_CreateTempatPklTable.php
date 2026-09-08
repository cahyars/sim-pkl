<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTempatPklTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_perusahaan'  => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat'           => ['type' => 'TEXT'],
            'no_telp'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'bidang_usaha'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'penanggung_jawab' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kuota'            => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('nama_perusahaan');
        $this->forge->createTable('tempat_pkl');
    }

    public function down()
    {
        $this->forge->dropTable('tempat_pkl');
    }
}
