<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeskripsiToDetailPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_penilaian', [
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nilai',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_penilaian', 'deskripsi');
    }
}
