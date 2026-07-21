<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMataPelajaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mata_pelajaran');
            $table->integer('jp')->default(1);
            $table->string('kategori')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('kategori_penjadwalan')->nullable()->comment('Contoh: umum, jurusan');
            $table->string('tefa_group_id')->nullable()->comment('Untuk mengelompokkan mapel sejenis dalam project');
            $table->foreignId('guru_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
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
        Schema::dropIfExists('mata_pelajaran');
    }
}
