<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/", function(){
    return response(app()->version());
});

Route::post('login_steam', 'LoginController@loginWithSteam');

Route::group(['middleware'=>'auth:api'], function(){

    Route::get('saldo', 'UsuarioController@getSaldo');

    Route::get('recent_matches', 'UsuarioController@getRecentMatches');

    Route::get('torneos', 'TorneoController@getAll');

    Route::post('depositar', 'BalanceController@depositar');
    Route::post('retirar', 'BalanceController@retirar');
    Route::get('transacciones', 'BalanceController@getAll');

    Route::post('bet', 'PartidaController@apostar');

    Route::get('apuestas', 'PartidaController@search');
    Route::post('partidadota/{partida_id}', 'PartidaController@procesarPartida');
    Route::get('apuesta/review', 'PartidaController@revisarPartidas');

    Route::get('profile', 'UsuarioController@getProfile');
    Route::put('profile', 'UsuarioController@update');
});

