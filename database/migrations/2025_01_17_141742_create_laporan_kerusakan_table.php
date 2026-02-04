<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanKerusakanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('nama_alat');
            $table->text('deskripsi_kerusakan');
            $table->date('tanggal_laporan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
}
