<?php namespace App\Repos;

use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuarioRepo {

    public function findBySteamId($steam_id){
        return Usuario::where('steamid64', $steam_id)->first();
    }

    public function createFromSteam($steamUser, $steamID64){
        $usuario = new Usuario();
        $usuario->nickname = $steamUser['personaname'];
        $usuario->steamid = (substr($steamID64, -16, 16) - 6561197960265728);
        $usuario->steamid64 = $steamID64;
        $usuario->foto = $steamUser['avatarfull'];
        $usuario->steam_time_created = isset($steamUser['timecreated']) ? $steamUser['timecreated'] : time();
        $usuario->api_token = Hash::make(Str::random(16));
        $usuario->balance = 0;
        $usuario->balance_prueba = 100;
        $usuario->test_mode = 0;
        $usuario->allow_withdraw = 1;
        $usuario->ref_code = strtoupper(Str::random(10));
        $usuario->save();
        return $usuario;
    }

    public function updateFromSteam(Usuario $usuario, $steamUser){
        $usuario->foto = $steamUser['avatarfull'];
        if($usuario->ref_code == null || $usuario->ref_code == '')
            $usuario->ref_code = strtoupper(Str::random(10));
        //$usuario->login_at = date('Y-m-d H:i:s');
        $usuario->save();

        return $usuario;
    }

    public function update(Usuario $usuario, $params){
        $usuario->fill($params);
        $usuario->save();
        return $usuario;
    }
}