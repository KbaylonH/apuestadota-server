<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUser;
use Illuminate\Http\Request;


class UsuarioController extends Controller
{
    //
    public function getSaldo(){
        $usuario = auth()->user();
        return response()->json(['saldo'=>number_format($usuario->balance, 2), 'saldo_prueba'=>number_format($usuario->balance_prueba, 2), 'saldo_switch'=>$usuario->balance_switch]);
    }

    public function switchSaldo(){
        $usuario = auth()->user();
        $usuario->update(['balance_switch' => ($usuario->balance_switch == 'balance' ? 'balance_prueba' : 'balance')]);
        return response()->json(['saldo_switch'=>$usuario->balance_switch]);
    }

    public function getProfile(){
        return response()->json(auth()->user());
    }

    public function update(UpdateUser $req){
        try {
            (new \App\Actions\UpdateProfile)->execute($req);
            return response()->json(['success'=>true]);
        } catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()]);
        }
    }

}
