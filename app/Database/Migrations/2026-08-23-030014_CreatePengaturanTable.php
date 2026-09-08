<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel pengaturan sistem — mendukung hak akses Admin §3.4
 * ("Kelola pengaturan sistem: jangka waktu reminder notifikasi, dsb.").
 * Disimpan sebagai key-value agar pengaturan baru tidak butuh migrasi ulang.
 */
class CreatePengaturanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kunci'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'nilai'      => ['type' => 'TEXT', 'null' => true],
            'keterangan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kunci');
        $this->forge->createTable('pengaturan');
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan');
    }
}
