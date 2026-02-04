<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ConvertAdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Super Admin
        User::create([
            'nama' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'super_admin',
        ]);
        
        // Update existing admin accounts
        User::where('email', 'adminPpdb@gmail.com')
            ->update(['role' => 'admin_ppdb']);
            
        User::where('email', 'adminSa@gmail.com')
            ->update(['role' => 'admin_sa']);
            
        User::where('email', 'adminPerpus@gmail.com')
            ->update(['role' => 'admin_perpus']);
            
        User::where('email', 'adminLab@gmail.com')
            ->update(['role' => 'admin_lab']);
            
        User::where('email', 'adminMagang@gmail.com')
            ->update(['role' => 'admin_magang']);
    }
}