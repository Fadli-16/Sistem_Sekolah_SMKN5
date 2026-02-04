<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('id')->on('users')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable();
            $table->string('nis')->unique();
            $table->string('image')->nullable();
            $table->string('kelas');
            $table->string('jurusan');
            $table->string('jenis_kelamin')->default('Laki-laki');
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->default('0000000000');
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
        Schema::table('siswa', function (Blueprint $table) {
            // Kembalikan ke nullable jika diperlukan
            $table->string('kelas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
        });
    }
}
