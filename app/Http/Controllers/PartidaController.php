<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repos\ApuestaRepo;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class PartidaController extends Controller {
    
    public function apostar(Request $request){
        try {
            $user = auth()->user();
            $params = $request->only('monto');
            $apuesta = (new \App\Actions\ApostarAction)->execute($user, $params, $request);
            return response()->json(['match'=>$apuesta]);
        } catch (\Exception $e){
            Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }
    }

    /* Vincula una partida (apuesta) a una partida reciente de Dota segun a ciertos criterios */
    public function procesarPartida($partida_id){
        try {
            $user = auth()->user();

            $apuesta = (new \App\Actions\ProcesarApuestaAction)->execute($user, $partida_id);

            // Al vincularse la partida de dota con la apuesta, devolvemos finished=true para que el fronted no realice una nueva busqueda
            return response()->json(['match_id'=>$apuesta->match_id]);

        } catch (\Exception $e){
            Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }
    }

    public function search(){
        return (new ApuestaRepo(auth()->user()))->search();
    }

    public function revisarPartidas(){
        Artisan::call('matches:check');
        return response()->json(['success'=>true]);
    }
}