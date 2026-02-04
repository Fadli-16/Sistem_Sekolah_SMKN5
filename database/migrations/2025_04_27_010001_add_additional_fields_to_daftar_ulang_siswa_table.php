<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToDaftarUlangSiswaTable extends Migration
{
    public function up()
    {
        Schema::table('daftar_ulang_siswa', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['Pria', 'Wanita'])->default('Pria')->after('major');
            $table->date('tanggal_lahir')->default(now()->format('Y-m-d'))->after('jenis_kelamin');
            $table->text('alamat')->default('Belum diisi')->after('tanggal_lahir');
            $table->string('no_hp', 15)->default('0000000000')->after('alamat');
        });
    }

    public function down()
    {
        Schema::table('daftar_ulang_siswa', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'tanggal_lahir', 'alamat', 'no_hp']);
        });
    }
}