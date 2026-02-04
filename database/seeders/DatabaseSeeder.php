<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DataUsers::class);
        $this->call(DataLaboratorium::class);
        $this->call(Inventaris::class);
        $this->call(DataPinjamLabor::class);
        $this->call(LaporanKerusakan::class);
    }
}
