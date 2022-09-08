<?php namespace App\Actions;

use App\Models\Usuario;
use App\Repos\DotaRepo;
use App\Repos\PartidaRepo;

class ProcesarApuestaAction {

    public function execute(Usuario $usuario, $partida_id){

        $repo = (new PartidaRepo($usuario));
        $partida = $repo->find($partida_id);

        if($partida == null)
            throw new \Exception("La apuesta no existe en el sistema");
        
        if($partida->match_id !== null)
            throw new \Exception("La apuesta ya fue puesta en partida de Dota");

        if(time() - strtotime($partida->created_at) > 1200){
            $partida->estado = '3';
            $partida->save();
            return $partida;
        }
        
        $dotaRepo = new DotaRepo($usuario->steamid);
        $matches = $dotaRepo->getRecentMatches();

        $filtered_matches = array_filter($matches, function($item) use ($partida){
            return $item->start_time - strtotime($partida->created_at) < 1200 && $item->game_mode == 22 && $item->lobby_type == 7;
        });

        // Si no encuentra partida, el fronted realizara una nueva busqueda
        if(count($filtered_matches) < 1){
            return response()->json(['match_id'=>null]);
        }

        $partida->match_id = $filtered_matches[0]->match_id;
        $partida->match_start_time = $filtered_matches[0]->start_time;
        $partida->match_hero_id = $filtered_matches[0]->hero_id;
        $partida->fecha_proceso = time();
        $partida->save();

        return $partida;

    }

}