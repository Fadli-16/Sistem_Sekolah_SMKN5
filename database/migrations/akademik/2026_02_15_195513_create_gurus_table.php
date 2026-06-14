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
            $table->string('kelas');
            $table->string('jurusan');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->default('Laki-laki');
            $table->string('agama', 50)->nullable();
            $table->text('alamat');
            $table->string('no_hp');
            $table->enum('status', ['guru', 'guru tidak tetap', 'pegawai', 'pegawai tidak tetap', 'kepala sekolah', 'wakil kepala kurikulum', 'wakil kepala humas', 'wakil kepala sarana prasarana', 'wakil kepala kesiswaan', 'bendahara gaji', 'bendahara BOS', 'bendahara pembimbing komite', 'kepala jurusan', 'kepala bengkel'])->default('guru');
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
