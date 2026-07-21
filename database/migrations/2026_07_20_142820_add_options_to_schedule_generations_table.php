<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsToScheduleGenerationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_generations', function (Blueprint $table) {
            $table->json('options')->nullable()->after('mapel_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_generations', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
