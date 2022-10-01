<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/", function(){
    return response(app()->version());
});

Route::view('payment_success', 'payment_success')->name('payment.success');
Route::view('payment_error', 'payment_error')->name('payment.error');

Route::post('izipay_finished', 'BalanceController@checkIzipayPayment');

Route::post('login_steam', 'LoginController@loginWithSteam');

Route::group(['middleware'=>'auth:api'], function(){

    Route::get('saldo', 'UsuarioController@getSaldo');
    Route::put('saldo/switch', 'UsuarioController@switchSaldo');

    Route::get('recent_matches', 'UsuarioController@getRecentMatches');

    Route::get('torneos', 'TorneoController@getAll');

    Route::post('depositar', 'BalanceController@depositar');

    Route::post('retiros', 'BalanceController@retirar');
    Route::get('retiros', 'BalanceController@getRetiros');

    Route::post('bet', 'PartidaController@apostar');

    Route::get('apuestas', 'PartidaController@search');
    Route::post('partidadota/{partida_id}', 'PartidaController@procesarPartida');
    Route::get('apuesta/review', 'PartidaController@revisarPartidas');

    Route::get('profile', 'UsuarioController@getProfile');
    Route::put('profile', 'UsuarioController@update');

    Route::get('referidos', 'ReferidoController');
});

