<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeminatansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('peminatans')) {
            Schema::create('peminatans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // relasi ke tabel users
                $table->string('minat')->nullable();
                $table->text('alasan')->nullable();
                $table->string('pemilihan_jurusan')->nullable();
                $table->string('jenis_pekerjaan')->nullable();
                $table->string('ide_bisnis')->nullable();
                $table->integer('penghasilan_ortu')->nullable();
                $table->integer('tanggungan_keluarga')->nullable();
                $table->string('file_angket')->nullable()->comment('URL Google Drive angket');
                $table->string('file_raport')->nullable()->comment('URL Google Drive raport');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('peminatans');
    }
}
