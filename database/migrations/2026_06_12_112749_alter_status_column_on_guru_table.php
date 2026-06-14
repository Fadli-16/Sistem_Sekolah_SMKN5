<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterStatusColumnOnGuruTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Alter enum column to include new roles
        DB::statement("ALTER TABLE guru MODIFY COLUMN status ENUM('guru', 'guru tidak tetap', 'pegawai', 'pegawai tidak tetap', 'kepala sekolah', 'wakil kepala kurikulum', 'wakil kepala humas', 'wakil kepala sarana prasarana', 'wakil kepala kesiswaan', 'bendahara gaji', 'bendahara BOS', 'bendahara pembimbing komite', 'kepala jurusan', 'kepala bengkel') DEFAULT 'guru'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original enum
        // Note: this will fail if there are records using the new enum values
        DB::statement("ALTER TABLE guru MODIFY COLUMN status ENUM('guru', 'guru tidak tetap', 'pegawai', 'pegawai tidak tetap') DEFAULT 'guru'");
    }
}
