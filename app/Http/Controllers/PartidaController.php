<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repos\PartidaRepo;
use App\Repos\BalanceRepo;
use App\Repos\DotaRepo;

class PartidaController extends Controller {
    
    public function apostar(Request $request){
        try {

            $user = auth()->user();
            $monto = $request->input('monto');

            if($user->balance < $monto)
                throw new \Exception("No cuenta con saldo disponible para realizar la apuesta");

            $partida = (new PartidaRepo($user))->create($monto);

            $dotaRepo = new DotaRepo($user->steamid);
            $matches = $dotaRepo->getRecentMatches();

            $filtered_matches = array_filter($matches, function($item) use ($partida){
                return ($item->start_time + 2) > strtotime($partida->created_at) && $item->game_mode == 22;
            });

            if(count($filtered_matches) < 1){
                $partida->delete();
                throw new \Exception("No se encontraron partidas recientes");
            }

            // vinculamos la apuesta con el match_id
            $partida->match_id = $filtered_matches[0]['match_id'];
            $partida->save();

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
}