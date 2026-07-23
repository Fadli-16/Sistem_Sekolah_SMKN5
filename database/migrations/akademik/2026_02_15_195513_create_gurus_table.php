<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGurusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('id')->on('users')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('nip')->nullable()->unique();
            $table->string('jurusan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->default('Laki-laki');
            $table->string('agama', 50)->nullable();
            $table->text('alamat');
            $table->string('no_hp');
            $table->enum('status', ['guru', 'guru tidak tetap', 'pegawai', 'pegawai tidak tetap', 'kepala sekolah', 'wakil kepala', 'bendahara', 'kepala jurusan', 'kepala bengkel', 'kepala bidang', 'koordinator'])->default('guru');
            $table->string('spesialisasi')->nullable();
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
        Schema::dropIfExists('guru');
    }
}
