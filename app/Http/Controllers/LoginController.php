<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repos\SteamRepo;
use App\Repos\UsuarioRepo;

class LoginController extends Controller
{
    public function loginWithSteam(Request $req){
        try {

            $postParams = $req->all();
            $repo = new SteamRepo();
            $steamID64 = $repo->getSteamId64($postParams);
            $steamUser = $repo->getUser($steamID64);

            $usuarioRepo = new UsuarioRepo();
            $userDB = $usuarioRepo->findBySteamId($steamUser['steamid']);

            if($userDB == null){
                $userDB = $usuarioRepo->createFromSteam($steamUser, $steamID64);
            } else {
                $userDB = $usuarioRepo->updateFromSteam($userDB, $steamUser);
            }

            return response()->json($userDB);
            
        } catch (\Exception $e){
            \Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }
    }
}
