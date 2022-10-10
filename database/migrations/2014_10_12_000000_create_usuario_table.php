<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nickname');
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('steamid')->unique();
            $table->string('steamid64')->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->decimal('balance', 11, 2)->nullable();
            $table->decimal('balance_prueba', 11, 2)->nullable();
            $table->integer('test_mode')->default(0);
            $table->integer('allow_withdraw')->default(1);
            $table->string('foto')->nullable();
            $table->integer('steam_time_created')->nullable();
            $table->string('api_token');
            $table->dateTime('login_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('usuario');
    }
}
