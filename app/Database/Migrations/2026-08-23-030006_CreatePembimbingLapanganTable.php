<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembimbingLapanganTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            // Nullable: pembimbing lapangan boleh didata tanpa akun login (ERD §5).
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tempat_pkl_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'jabatan'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_hp'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addKey('tempat_pkl_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tempat_pkl_id', 'tempat_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembimbing_lapangan');
    }

    public function down()
    {
        $this->forge->dropTable('pembimbing_lapangan');
    }
}
