<?php namespace App\Repos;

use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuarioRepo {

    public function findBySteamId($steam_id){
        return Usuario::where('steamid', $steam_id)->first();
    }

    public function createFromSteam($steamUser, $steamID64){
        $usuario = new Usuario();
        $usuario->nickname = $steamUser['accountname'];
        $usuario->steamid = $steamUser['steamid'];
        $usuario->steamid64 = $steamID64;
        $usuario->foto = $steamUser['avatar'];
        $usuario->api_token = Hash::make(Str::random(16));
        $usuario->save();
        return $usuario;
    }


}