<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanKerusakan extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('laporan_kerusakan')->insert([
            [
                'nama_pelapor' => 'John Doe',
                'nama_alat' => 'Microscope',
                'deskripsi_kerusakan' => 'Lensa pecah dan sulit digunakan.',
                'tanggal_laporan' => '2025-01-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelapor' => 'Jane Smith',
                'nama_alat' => 'Projector',
                'deskripsi_kerusakan' => 'Tidak bisa menyala meskipun sudah dihubungkan ke listrik.',
                'tanggal_laporan' => '2025-01-16',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pelapor' => 'Alice Johnson',
                'nama_alat' => 'Laptop',
                'deskripsi_kerusakan' => 'Keyboard beberapa tombol tidak berfungsi.',
                'tanggal_laporan' => '2025-01-17',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
