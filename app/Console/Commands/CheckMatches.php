<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Usuario;
use App\Models\Partida;
use App\Repos\DotaRepo;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matches:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca las partidas (apuestas) en estado 0 (pendientes) y realiza los pagos a los ganadores';

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

        $config = DB::table('0_config')->where('id', 2)->first();

        if($config->valor == 1){
            Log::info("Ya se esta completando las apuestas...");
            return;
        }

        // Se obtiene los match_ids en estado pendiente
        $partidas = Partida::where('estado', 0)->groupBy('match_id')->select('match_id')->get();

        if(count($partidas) < 1)
            return;

        DB::table('0_config')->where('id', 2)->update(['valor'=>1]);

        foreach($partidas as $partida){

            try {
                // Buscamos la partida con el api de dota (OJO: aun desconozco cual es la variable que indica si la partida ha terminado)
                $match = (new DotaRepo)->findMatch($partida->match_id);

                if(isset($match->error))
                    throw new \Exception("Hubo un error al obtener la informacion de la partida de dota #" . $partida->match_id. ":" . $match->error);

                if($match !== null){

                    if(isset($match->error)){
                        continue;
                    }

                    // Obtenemos los jugadores de la partida
                    $players = $match->players;

                    // Filtramos a los que ganaron
                    $winners = array_filter($players, function($item){
                        return $item->win == 1 && $item->account_id !== null;
                    });

                    // Recorremos a los ganadores
                    foreach($winners as $winner){
                        // Obtenemos el usuario mediante el steamid
                        $user = Usuario::where('steamid', $winner->account_id)->first();

                        // Validamos si el ganador esta en nuestra BD
                        if($user !== null){

                            // buscamos su partida (apuesta)
                            $user_partida = Partida::where('match_id', $partida->match_id)->where('usuarioid', $user->usuarioid)->first();

                            // Se aumenta su saldo con su respectivo 40%
                            $user->balance = $user->balance + ($user_partida->monto * 1.4);
                            $user->save();
                            
                            // marcado como ganado
                            $user_partida->fecha_finalizado = time(); 
                            $user_partida->estado = '1'; 
                            $user_partida->save();
                        }
                    }

                    // Marcar las apuestas de los otros participantes como perdidas (estado = 2)
                    Partida::where('match_id', $partida->match_id)->where('estado', '0')->update(['estado'=>'2','fecha_finalizado'=>time()]);
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        DB::table('0_config')->where('id', 2)->update(['valor'=>0]);
    }
}
