<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHariToLaboratoriumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('laboratorium', function (Blueprint $table) {
        $table->string('hari', 10)->after('labor');
    });
}

public function down()
{
    Schema::table('laboratorium', function (Blueprint $table) {
        $table->dropColumn('hari');
    });
}
}
