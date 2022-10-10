<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePartidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apuesta', function (Blueprint $table){
            $table->bigInteger('fecha_proceso')->nullable();
            $table->bigInteger('fecha_finalizado')->nullable();
            $table->bigInteger('match_start_time', false, false)->nullable();
            $table->integer('match_hero_id', false, false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
