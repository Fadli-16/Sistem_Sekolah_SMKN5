<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMagangSiswaTable extends Migration
{
    public function up()
    {
        Schema::table('magang_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('opening_id')->nullable()->after('perusahaan_id');
            $table->string('email')->nullable()->after('nama');
            $table->string('no_hp')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('magang_siswa', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'opening_id', 'email', 'no_hp']);
        });
    }
}