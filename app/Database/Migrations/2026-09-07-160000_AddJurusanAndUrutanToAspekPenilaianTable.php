<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJurusanAndUrutanToAspekPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('aspek_penilaian', [
            'jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Umum',
                'null'       => false,
                'after'      => 'nama_aspek',
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'null'       => false,
                'after'      => 'jurusan',
            ],
        ]);

        $this->db->query('ALTER TABLE `aspek_penilaian` ADD INDEX `idx_jurusan` (`jurusan`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `aspek_penilaian` DROP INDEX `idx_jurusan`');
        $this->forge->dropColumn('aspek_penilaian', ['jurusan', 'urutan']);
    }
}
