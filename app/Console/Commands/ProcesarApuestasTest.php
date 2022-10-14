<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Usuario;
use App\Models\Test\ApuestaTest;
use App\Repos\DotaRepo;

class ProcesarApuestasTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apuestas_test:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca las apuestas de prueba y los procesa';

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
        $config = DB::table('0_config')->where('id', 4)->first();

        if($config->valor == 1){
            Log::info("Ya esta en proceso las apuestas...");
            return;
        } 

        // Se obtiene los match_ids en estado pendiente
        $apuestas = ApuestaTest::where('estado', 0)->whereNull('match_id')->get();

        Log::info("Nro Partidas a procesar: " . count($apuestas));

        if(count($apuestas) < 1){ 
            Log::info("No hay partidas que procesar...");
            return;
        }

        DB::table('0_config')->where('id', 4)->update(['valor'=>1]);

        $maxima_espera = DB::table('0_config')->where('id', 3)->value('valor');
        $apuesta_intervalo = DB::table('0_config')->where('id', 8)->value('valor');

        foreach($apuestas as $apuesta){

            try {

                $usuario = $apuesta->usuario;

                Log::info("ID USUARIO: " . $usuario->id);
                Log::info("timestamp apuesta: " . $apuesta->created_at);
    
                if(time() - strtotime($apuesta->created_at) > $maxima_espera){
                    $apuesta->fecha_finalizado = time();
                    $apuesta->estado = '3';
                    $apuesta->save();
                    continue;
                }
                
                $dotaRepo = new DotaRepo($usuario->steamid);
                $matches = $dotaRepo->getRecentMatches();
        
                if(isset($matches->error)){
                    Log::info("Hubo un error al obtener la info del API de Dota: " . $matches->error);
                    continue;
                }

                $filtered_matches = array_filter($matches, function($item) use ($apuesta){
                    //$diff = $item->start_time - strtotime($apuesta->created_at);
                    //return $diff > 0 && $diff < $apuesta_intervalo && $item->game_mode == 22 && $item->lobby_type == 7 && $item->party_size == 1;
                    return $item->start_time >= strtotime($apuesta->created_at) && $item->game_mode == 22 && $item->lobby_type == 7 && $item->party_size == 1;
                });
        
                // Si no encuentra partida, el fronted realizara una nueva busqueda
                if(count($filtered_matches) < 1){
                    Log::info("No se ha encontrado una partida para colocarlo en la apuesta");
                    continue;
                } else {
                    Log::info(json_encode($filtered_matches));
                    $exists = $this->findMatch($apuesta->usuario_id, $filtered_matches[0]->match_id);
                    if($exists != null){
                        Log::info("La partida nro " . $filtered_matches[0]->match_id . " ya fue colocado en otra apuesta");
                        continue;
                    }
                }
        
                $diff = $filtered_matches[0]->start_time - strtotime($apuesta->created_at);
                if($diff > 0 && $diff < $apuesta_intervalo){
                    Log::info("Se encontro una partida para la apuesta #" . $apuesta->id);
                    $apuesta->match_id = $filtered_matches[0]->match_id;
                    $apuesta->match_start_time = $filtered_matches[0]->start_time;
                    $apuesta->match_hero_id = $filtered_matches[0]->hero_id;
                    $apuesta->fecha_proceso = time();
                    $apuesta->save();
                } else {
                    Log::info("Error: la diferencia de minutos es mayor a la tolerada");
                    $apuesta->match_id = $filtered_matches[0]->match_id;
                    $apuesta->match_start_time = $filtered_matches[0]->start_time;
                    $apuesta->match_hero_id = $filtered_matches[0]->hero_id;
                    $apuesta->estado = '3';
                    $apuesta->fecha_finalizado = time();
                    $apuesta->save();
                }
            } catch (\Exception $e){
                Log::error($e);
            }
        }

        DB::table('0_config')->where('id', 4)->update(['valor'=>0]);
    }

    private function findMatch($usuario_id, $match_id){
        return ApuestaTest::where('usuario_id', $usuario_id)->where('match_id', $match_id)->first();
    }
}
