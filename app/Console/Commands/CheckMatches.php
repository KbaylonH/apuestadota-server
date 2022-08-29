<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Usuario;
use App\Models\Partida;
use App\Repos\DotaRepo;

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
        // Se obtiene los match_ids en estado pendiente
        $partidas = Partida::where('estado', 0)->groupBy('match_id')->select('match_id')->get();

        foreach($partidas as $partida){

            // Buscamos la partida con el api de dota (OJO: aun desconozco cual es la variable que indica si la partida ha terminado)
            $match = (new DotaRepo)->findMatch($partida->match_id);

            if($match !== null){
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
                        $user_partida->estado = '1'; 
                        $user_partida->save();
                    }
                }

                // Marcar las apuestas de los otros participantes como perdidas (estado = 2)
                Partida::where('match_id', $partida->match_id)->where('estado', '0')->update(['estado'=>'2']);
            }
            
        }

    }
}
