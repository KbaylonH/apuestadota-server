<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login_steam', 'LoginController@loginWithSteam');

Route::group(['middleware'=>'auth:api'], function(){

    Route::get('saldo', 'UsuarioController@getSaldo');

    Route::get('recent_matches', 'UsuarioController@getRecentMatches');

    Route::get('torneos', 'TorneoController@getAll');

    Route::post('depositar', 'BalanceController@depositar');
    Route::post('retirar', 'BalanceController@retirar');
    Route::get('transacciones', 'BalanceController@getAll');

    Route::post('bet', 'PartidaController@apostar');
});

