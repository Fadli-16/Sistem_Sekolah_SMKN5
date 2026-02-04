<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaboratoriumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('laboratorium', function (Blueprint $table) {
    $table->id();
    $table->string('labor'); // Nama laboratorium
    $table->enum('hari', ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']); // Hari dalam minggu
    $table->time('start');  // Jam mulai, tanpa tanggal
    $table->time('end');    // Jam selesai, tanpa tanggal
    $table->string('keterangan')->nullable(); // Keterangan tambahan
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
        Schema::dropIfExists('laboratorium');
    }
}