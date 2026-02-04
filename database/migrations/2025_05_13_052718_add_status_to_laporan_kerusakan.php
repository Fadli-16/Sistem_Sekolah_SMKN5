<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToLaporanKerusakan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->enum('status', ['pending', 'process', 'completed', 'rejected'])->default('pending')->after('tanggal_laporan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}