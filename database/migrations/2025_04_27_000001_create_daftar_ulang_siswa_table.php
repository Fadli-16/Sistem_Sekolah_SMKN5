<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarUlangSiswaTable extends Migration
{
    public function up()
    {
        Schema::create('daftar_ulang_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('major');
            $table->string('password');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_ulang_siswa');
    }
}