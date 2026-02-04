<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpdbCalonSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppdb_calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('sekolah_asal');
            $table->string('no_hp');
            $table->string('email');
            $table->enum('status_pendaftaran', ['Menunggu', 'Diterima', 'Ditolak'])->default('Menunggu');
            $table->date('tanggal_pendaftaran');
            $table->string('nilai_rapor')->nullable();
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
        Schema::dropIfExists('ppdb_calon_siswa');
    }
}
