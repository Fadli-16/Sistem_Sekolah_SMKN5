<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DataUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'nama' => 'AdminPPDB',
            'email' => 'adminPpdb@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'menu' => 'ppdb',
        ]);
        User::factory()->create([
            'nama' => 'AdminSistemAkademik',
            'email' => 'adminSa@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'menu' => 'sistem_akademik',
        ]);
        User::factory()->create([
            'nama' => 'AdminPerpus',
            'email' => 'adminPerpus@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'menu' => 'perpus',
        ]);
        User::factory()->create([
            'nama' => 'AdminLabor',
            'email' => 'adminLab@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'menu' => 'labor',
        ]);
        User::factory()->create([
            'nama' => 'AdminMagang',
            'email' => 'adminMagang@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'menu' => 'magang',
        ]);

        User::factory()->create([
            'nama' => 'Guru',
            'email' => 'guru@gmail.com',
            'password' => Hash::make('guru'),
            'role' => 'guru',
        ]);
    }
}
