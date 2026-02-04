<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddWakilPerusahaanRoleToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Option 1: If using ENUM column type
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin', 'admin_ppdb', 'admin_sa', 'admin_perpus', 'admin_lab', 'admin_magang', 'guru', 'siswa', 'wakil_perusahaan') NOT NULL");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Option 1: If using ENUM column type, revert to original values
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin', 'admin_ppdb', 'admin_sa', 'admin_perpus', 'admin_lab', 'admin_magang', 'guru', 'siswa') NOT NULL");
        
    }
}