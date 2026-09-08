<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'penilaian_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'aspek_penilaian_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai'               => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['penilaian_id', 'aspek_penilaian_id']);
        $this->forge->addForeignKey('penilaian_id', 'penilaian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('aspek_penilaian_id', 'aspek_penilaian', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('detail_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('detail_penilaian');
    }
}
