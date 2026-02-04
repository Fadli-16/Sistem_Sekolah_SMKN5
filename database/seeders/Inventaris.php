<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Inventaris extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inventaris')->insert([
            [
                'nama_inventaris' => 'Microscope',
                'kategori' => 'Alat Laboratorium',
                'jumlah' => 5,
                'deskripsi' => 'Digunakan untuk melihat objek kecil.',
                'status' => 'Tersedia',
                'gambar' => 'microscope.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_inventaris' => 'Projector',
                'kategori' => 'Elektronik',
                'jumlah' => 2,
                'deskripsi' => 'Digunakan untuk presentasi.',
                'status' => 'Tersedia',
                'gambar' => 'proyektor.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_inventaris' => 'Whiteboard',
                'kategori' => 'Peralatan',
                'jumlah' => 10,
                'deskripsi' => 'Digunakan untuk menulis di ruang kelas.',
                'status' => 'Tidak Tersedia',
                'gambar' => 'whiteboard.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
