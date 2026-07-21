<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generation_id')->constrained('schedule_generations')->cascadeOnDelete();
            
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->nullOnDelete();
            
            $table->string('hari')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('ruangan')->nullable();

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
        Schema::dropIfExists('schedule_drafts');
    }
}
