<?php namespace App\Http\Controllers;

use App\Models\Deposito;

class ReferidoController extends Controller {

    public function __invoke(){
        $user = auth()->user();
        $depositos = Deposito::with(['usuario'=>function($query){
            return $query->select('id', 'nickname', 'foto');
        }])->where('ref_code', $user->ref_code)->whereIn('estado', [1,3])->orderBy('created_at','desc')->select('usuario_id','ref_code','created_at')->get();
        return response()->json($depositos);
    }

}