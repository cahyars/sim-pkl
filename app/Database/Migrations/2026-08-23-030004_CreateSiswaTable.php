<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nis'             => ['type' => 'VARCHAR', 'constraint' => 20],
            'nisn'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'kelas'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'jurusan'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'no_hp'           => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'alamat'          => ['type' => 'TEXT', 'null' => true],
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status_pkl'      => [
                'type'       => 'ENUM',
                'constraint' => ['belum_ditempatkan', 'aktif', 'selesai'],
                'default'    => 'belum_ditempatkan',
            ],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nis');
        $this->forge->addKey('kelas');
        $this->forge->addKey('status_pkl');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa');
    }
}
