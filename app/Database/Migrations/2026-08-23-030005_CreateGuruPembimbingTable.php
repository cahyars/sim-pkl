<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGuruPembimbingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nip'          => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'no_hp'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'jurusan_ampu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('guru_pembimbing');
    }

    public function down()
    {
        $this->forge->dropTable('guru_pembimbing');
    }
}
