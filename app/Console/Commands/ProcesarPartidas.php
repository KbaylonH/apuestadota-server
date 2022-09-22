<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Usuario;
use App\Models\Partida;
use App\Repos\DotaRepo;

class ProcesarPartidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca las partidas (apuestas) y los procesa';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = DB::table('0_config')->where('id', 1)->first();

        if($config->valor == 1){
            Log::info("Ya esta en proceso las apuestas...");
            return;
        } 

        // Se obtiene los match_ids en estado pendiente
        $partidas = Partida::where('estado', 0)->whereNull('match_id')->get();

        Log::info("Nro Partidas a procesar: " . count($partidas));

        if(count($partidas) < 1){ 
            Log::info("No hay partidas que procesar...");
            return;
        }

        DB::table('0_config')->where('id', 1)->update(['valor'=>1]);

        $maxima_espera = DB::table('0_config')->where('id', 3)->first()->valor;

        foreach($partidas as $partida){

            try {

                $usuario = Usuario::find($partida->usuarioid);

                Log::info("Usuarioid: " . $usuario->usuarioid);
                Log::info("timestamp apuesta: " . $partida->created_at);
    
                if(time() - strtotime($partida->created_at) > $maxima_espera){
                    $partida->estado = '2';
                    $partida->save();
                    continue;
                }
                
                $dotaRepo = new DotaRepo($usuario->steamid);
                $matches = $dotaRepo->getRecentMatches();
        
                if(isset($matches->error)){
                    Log::error("Hubo un error al obtener la info del API de Dota: " . $matches->error);
                    continue;
                }

                $filtered_matches = array_filter($matches, function($item) use ($partida, $maxima_espera){
                    $diff = $item->start_time - strtotime($partida->created_at);
                    return $diff > 0 && $diff < $maxima_espera && $item->game_mode == 22 && $item->lobby_type == 7;
                });
        
                // Si no encuentra partida, el fronted realizara una nueva busqueda
                if(count($filtered_matches) < 1){
                    Log::info("No se ha encontrado una partida para colocarlo en la apuesta");
                    continue;
                } else {
                    Log::info(json_encode($filtered_matches));
                    $exists = $this->findMatch($partida->usuarioid, $filtered_matches[0]->match_id);
                    if($exists != null){
                        Log::info("La partida nro " . $filtered_matches[0]->match_id . " ya fue colocado en otra apuesta");
                        continue;
                    }
                }
        
                Log::info("Se encontro una partida para la apuesta #" . $partida->partidaid);
                $partida->match_id = $filtered_matches[0]->match_id;
                $partida->match_start_time = $filtered_matches[0]->start_time;
                $partida->match_hero_id = $filtered_matches[0]->hero_id;
                $partida->fecha_proceso = time();
                $partida->save();
            } catch (\Exception $e){
                Log::error($e);
            }
        }

        DB::table('0_config')->where('id', 1)->update(['valor'=>0]);
    }

    private function findMatch($usuarioid, $match_id){
        return Partida::where('usuarioid', $usuarioid)->where('match_id', $match_id)->first();
    }
}
