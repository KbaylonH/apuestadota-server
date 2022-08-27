<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class UsuarioController extends Controller
{
    //
    public function getSaldo(){
        $usuario = auth()->user();
        return response()->json(['saldo'=>number_format($usuario->balance, 2)]);
    }
}
