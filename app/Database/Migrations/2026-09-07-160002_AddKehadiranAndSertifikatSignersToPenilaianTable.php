<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKehadiranAndSertifikatSignersToPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penilaian', [
            'sakit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'feedback',
            ],
            'izin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'sakit',
            ],
            'tanpa_keterangan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'izin',
            ],
            'pimpinan_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'tanpa_keterangan',
            ],
            'pimpinan_nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'pimpinan_nama',
            ],
            'instruktur_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'pimpinan_nip',
            ],
            'instruktur_nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'instruktur_nama',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penilaian', [
            'sakit', 'izin', 'tanpa_keterangan',
            'pimpinan_nama', 'pimpinan_nip',
            'instruktur_nama', 'instruktur_nip',
        ]);
    }
}
