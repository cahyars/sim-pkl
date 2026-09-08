<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambah dukungan presensi (Masuk/Izin/Sakit) pada logbook. Saat status
 * kehadiran bukan "masuk", jam_mulai/jam_selesai tidak relevan (ditampilkan
 * sebagai "-"), sehingga kolom tersebut perlu dibuat nullable.
 */
class AddStatusKehadiranToLogbookTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('logbook', [
            'status_kehadiran' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'izin', 'sakit'],
                'default'    => 'masuk',
                'null'       => false,
                'after'      => 'penempatan_pkl_id',
            ],
        ]);

        $this->forge->modifyColumn('logbook', [
            'jam_mulai' => [
                'name' => 'jam_mulai',
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_selesai' => [
                'name' => 'jam_selesai',
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('logbook', [
            'jam_mulai' => [
                'name' => 'jam_mulai',
                'type' => 'TIME',
                'null' => false,
            ],
            'jam_selesai' => [
                'name' => 'jam_selesai',
                'type' => 'TIME',
                'null' => false,
            ],
        ]);

        $this->forge->dropColumn('logbook', 'status_kehadiran');
    }
}
