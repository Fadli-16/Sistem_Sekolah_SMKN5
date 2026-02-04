<?php

namespace Database\Seeders;

use App\Models\Laboratorium;
use Illuminate\Database\Seeder;

class DataLaboratorium extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $laboratorium = [
            [
                'labor' => 'TKJ',
                'status' => 'terpakai',
                'start' => '2024-11-15 09:00:00',
                'end' => '2024-11-15 12:00:00',
            ],
            [
                'labor' => 'TKJ',
                'status' => 'kosong',
                'start' => '2024-11-15 13:00:00',
                'end' => '2024-11-15 16:00:00',
            ],
            [
                'labor' => 'MM',
                'status' => 'kosong',
                'start' => '2024-11-15 09:00:00',
                'end' => '2024-11-15 12:00:00',
            ],
            [
                'labor' => 'MM',
                'status' => 'terpakai',
                'start' => '2024-11-15 13:00:00',
                'end' => '2024-11-15 16:00:00',
            ],
            [
                'labor' => 'RPL',
                'status' => 'kosong',
                'start' => '2024-11-16 09:00:00',
                'end' => '2024-11-16 12:00:00',
            ],
            [
                'labor' => 'RPL',
                'status' => 'terpakai',
                'start' => '2024-11-16 13:00:00',
                'end' => '2024-11-16 16:00:00',
            ],
        ];

        foreach ($laboratorium as $lab) {
            Laboratorium::create($lab);
        }  
    }
}
