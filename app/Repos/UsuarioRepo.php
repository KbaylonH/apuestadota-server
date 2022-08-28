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
        $usuario->steamid = (substr($steamID64, -16, 16) - 6561197960265728);
        $usuario->steamid64 = $steamID64;
        $usuario->foto = $steamUser['avatar'];
        $usuario->steam_time_created = $steamUser['timecreated'];
        $usuario->api_token = Hash::make(Str::random(16));
        $usuario->save();
        return $usuario;
    }

    public function updateFromSteam(Usuario $usuario, $steamUser){
        $usuario->foto = $steamUser['avatar'];
        $usuario->save();

        return $usuario;
    }


}