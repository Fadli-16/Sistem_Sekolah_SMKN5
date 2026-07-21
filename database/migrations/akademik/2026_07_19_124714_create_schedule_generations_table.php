<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleGenerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_generations', function (Blueprint $table) {
            $table->id();
            $table->string('jurusan')->nullable();
            $table->json('kelas_ids')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('skor_kualitas')->nullable();
            $table->integer('total_konflik')->nullable();
            $table->text('catatan_ai')->nullable();
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
        Schema::dropIfExists('schedule_generations');
    }
}
