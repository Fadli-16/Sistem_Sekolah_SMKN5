<?php

namespace Database\Seeders;

use App\Models\PinjamLabor;
use Illuminate\Database\Seeder;

class DataPinjamLabor extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PinjamLabor::create([
            'nama' => 'John',
            'laboratorium_id' => 1,
            'keperluan' => 'Praktikum',
            'tanggal' => now()->toDateString(),
            'waktu' => now()->toTimeString(),
        ]);

        PinjamLabor::create([
            'nama' => 'Doe',
            'laboratorium_id' => 2,
            'keperluan' => 'Praktikum',
            'tanggal' => now()->toDateString(),
            'waktu' => now()->toTimeString(),
        ]);
    }
}
