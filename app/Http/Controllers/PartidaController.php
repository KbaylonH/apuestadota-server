<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repos\PartidaRepo;
use App\Repos\BalanceRepo;
use App\Repos\DotaRepo;
use Illuminate\Support\Facades\Artisan;

class PartidaController extends Controller {
    
    public function apostar(Request $request){
        try {
            $user = auth()->user();
            $monto = $request->input('monto');
            $partida = (new \App\Actions\ApostarAction)->execute($user, $monto);
            return response()->json(['match'=>$partida]);
        } catch (\Exception $e){
            \Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }
    }

    /* Vincula una partida (apuesta) a una partida reciente de Dota segun a ciertos criterios */
    public function procesarPartida($partida_id){
        try {
            $user = auth()->user();

            $partida = (new \App\Actions\ProcesarApuestaAction)->execute($user, $partida_id);

            // Al vincularse la partida de dota con la apuesta, devolvemos finished=true para que el fronted no realice una nueva busqueda
            return response()->json(['match_id'=>$partida->match_id]);

        } catch (\Exception $e){
            \Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }
    }

    public function search(){
        return (new PartidaRepo(auth()->user()))->search();
    }

    public function revisarPartidas(){
        Artisan::call('matches:check');
        return response()->json(['success'=>true]);
    }
}