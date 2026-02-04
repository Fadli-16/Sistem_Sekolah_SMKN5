<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToLaporanKerusakan extends Migration
{
    public function up()
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->enum('status', ['pending', 'process', 'completed', 'rejected'])->default('pending')->after('tanggal_laporan');
            $table->text('tanggapan')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->dropColumn(['status', 'tanggapan']);
        });
    }
}