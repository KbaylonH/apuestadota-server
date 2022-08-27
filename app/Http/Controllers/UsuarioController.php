<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repos\DotaRepo;

class UsuarioController extends Controller
{
    //
    public function getSaldo(){
        $usuario = auth()->user();
        return response()->json(['saldo'=>number_format($usuario->balance, 2)]);
    }

    public function getRecentMatches(){
        $usuario = auth()->user();
        $matches = (new DotaRepo($usuario->steamid64))->getRecentMatches();
        return response()->json(['matches'=>$matches]);
    }
}
