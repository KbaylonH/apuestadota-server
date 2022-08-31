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

            if($user->balance < $monto)
                throw new \Exception("No cuenta con saldo disponible para realizar la apuesta");

            $partida = (new PartidaRepo($user))->create($monto);

            // Descontamos del saldo
            $balanceRepo =  (new BalanceRepo);
            $balanceRepo->setUsuario($user);
            $balanceRepo->decrease($monto);

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
            $repo = (new PartidaRepo($user));
            $partida = $repo->find($partida_id);

            if($partida == null)
                throw new \Exception("La apuesta no existe en el sistema");
            
            if($partida->match_id !== null)
                throw new \Exception("La apuesta ya fue puesta en partida de Dota");
            
            $dotaRepo = new DotaRepo($user->steamid);
            $matches = $dotaRepo->getRecentMatches();

            $filtered_matches = array_filter($matches, function($item) use ($partida){
                return $item->game_mode == 22;
            });

            // Si no encuentra partida, el fronted realizara una nueva busqueda
            if(count($filtered_matches) < 1){
                return response()->json(['match_id'=>null]);
            }

            $partida->match_id = $filtered_matches[0]->match_id;
            $partida->save();

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