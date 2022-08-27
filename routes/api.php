<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login_steam', 'LoginController@loginWithSteam');

Route::group(['middleware'=>'auth:api'], function(){

    Route::get('saldo', 'UsuarioController@getSaldo');

    Route::get('recent_matches', 'UsuarioController@getRecentMatches');

    Route::get('torneos', 'TorneoController@getAll');
});

