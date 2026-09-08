<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokumentasiLogbookTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'logbook_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_type'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'file_size'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'uploaded_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('logbook_id');
        $this->forge->addForeignKey('logbook_id', 'logbook', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumentasi_logbook');
    }

    public function down()
    {
        $this->forge->dropTable('dokumentasi_logbook');
    }
}
