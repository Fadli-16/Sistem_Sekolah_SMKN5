<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpeningIdToMagangSiswaTable extends Migration
{
    public function up()
    {
        Schema::table('magang_siswa', function (Blueprint $table) {
            // Add opening_id column after perusahaan_id
            $table->unsignedBigInteger('opening_id')->nullable()->after('perusahaan_id');
            
            // Add foreign key constraint
            $table->foreign('opening_id')
                  ->references('id')
                  ->on('magang_openings')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('magang_siswa', function (Blueprint $table) {
            $table->dropForeign(['opening_id']);
            $table->dropColumn('opening_id');
        });
    }
}