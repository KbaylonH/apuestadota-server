<?php namespace App\Actions;

use App\Models\Usuario;
use App\Repos\DotaRepo;
use App\Repos\ApuestaRepo;

class ProcesarApuestaAction {

    public function execute(Usuario $usuario, $partida_id){

        $repo = (new ApuestaRepo($usuario));
        $apuesta = $repo->find($partida_id);

        if($apuesta == null)
            throw new \Exception("La apuesta no existe en el sistema");
        
        if($apuesta->match_id !== null)
            throw new \Exception("La apuesta ya fue puesta en partida de Dota");

        if(time() - strtotime($apuesta->created_at) > 1200){
            $apuesta->estado = '2';
            $apuesta->save();
            return $apuesta;
        }
        
        $dotaRepo = new DotaRepo($usuario->steamid);
        $matches = $dotaRepo->getRecentMatches();

        $filtered_matches = array_filter($matches, function($item) use ($apuesta){
            $diff = $item->start_time - strtotime($apuesta->created_at);
            return $diff > 0 && $diff < 1200 && $item->game_mode == 22 && $item->lobby_type == 7 && $item->party_size == 1;
        });

        // Si no encuentra partida, el fronted realizara una nueva busqueda
        if(count($filtered_matches) < 1){
            return response()->json(['match_id'=>null]);
        }

        $apuesta->match_id = $filtered_matches[0]->match_id;
        $apuesta->match_start_time = $filtered_matches[0]->start_time;
        $apuesta->match_hero_id = $filtered_matches[0]->hero_id;
        $apuesta->fecha_proceso = time();
        $apuesta->save();

        return $apuesta;

    }

}
