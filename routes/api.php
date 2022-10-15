<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


Route::get("/", function(){
    return response(app()->version());
});

Route::get('restore_tasks', function(){
    DB::table('0_config')->whereIn('id', [1,2,4,5])->update(['valor'=>0]);
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

    Route::post('deposito/test', 'BalanceController@depositarTest');
    Route::get('balance/resumen', 'BalanceController@getResumen');
});

