<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsuario2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuario', function (Blueprint $table){
            $table->string('ref_code', 10)->nullable();
            $table->string('dni', 16)->nullable();
            $table->string('dni_url')->nullable();
            $table->integer('dni_status')->nullable();
            $table->integer('balance_prueba')->nullable();
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
