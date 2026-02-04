<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinjamInventarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinjam_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Kolom nama
            $table->string('kelas'); // Kolom kelas
            $table->string('inventaris'); // Kolom inventaris
            $table->date('tanggal_peminjaman'); // Kolom tanggal peminjaman
            $table->text('tujuan'); // Kolom tujuan
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu'); // Kolom inventaris
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
        Schema::dropIfExists('pinjam_inventaris');
    }
}
