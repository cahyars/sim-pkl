<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAspekPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_aspek' => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi'  => ['type' => 'TEXT', 'null' => true],
            // Bobot dalam persen; total seluruh aspek aktif diharapkan 100.
            'bobot'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_active');
        $this->forge->createTable('aspek_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('aspek_penilaian');
    }
}
