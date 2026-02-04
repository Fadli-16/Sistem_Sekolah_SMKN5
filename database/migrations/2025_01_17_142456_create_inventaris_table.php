<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_inventaris');
            $table->string('kategori');
            $table->integer('jumlah');
            $table->string('kondisi')->nullable();
            $table->string('lokasi');
            $table->date('tanggal_pengadaan');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Tersedia', 'Tidak Tersedia']);
            $table->string('gambar')->nullable();
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
        Schema::dropIfExists('inventaris');
    }
}