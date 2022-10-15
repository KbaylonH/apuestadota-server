<?php namespace App\Actions;

/**
 * @author Kevin Baylón <kbaylonh@outlook.com>
 */

use App\Models\Usuario;
use App\Models\Deposito;
use App\Models\Apuesta;
use App\Models\Test\ApuestaTest;
use App\Repos\BalanceRepo;
use App\Repos\ApuestaRepo;
use App\Repos\SteamRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApostarAction {

    public function execute(Usuario $usuario, $params, Request $req){

        if(!$this->hasPublicAccess($usuario))
            throw new \Exception("No se pudo realizar la apuesta porque debes compartir tus estadísticas en Dota2. En el siguiente video te enseñamos a hacerlo.");

        $multiplicador = 1.4;
        $monto = $params['monto'];

        $repo = new ApuestaRepo($usuario);
        /*
        $emptyApuesta = $repo->getEmptyApuesta();

        if($emptyApuesta !== null)
            throw new \Exception("Solo se puede colocar como máximo 1 apuesta a la vez");*/
        
        if( $this->hasApuestasPendientes($usuario) )
            throw new \Exception("Solo se puede colocar como máximo 1 apuesta a la vez");

        if($usuario->{$usuario->balance_switch} < $monto)
            throw new \Exception("No cuenta con saldo disponible para realizar la apuesta");

        // Si el usuario realizó 19 apuestas durante el mes, para esta apuesta el multiplicador de apuesta es 2
        if( $usuario->test_mode == 0 && $this->validoBono20Partidas($usuario) )
            $multiplicador = 2;

        $apuesta = $repo->create($monto, $multiplicador, [
            'ip_address' => $req->ip(),
            'isp' => gethostbyaddr($req->ip()),
            'pc_name' => gethostname()
        ]);

        // Descontamos del saldo        
        $balanceRepo =  (new BalanceRepo);
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->decrease($monto, $usuario->balance_switch);
        if($usuario->test_mode == 0){
            $this->habilitarRetiro($usuario);
        }
        return $apuesta;
    }

    private function habilitarRetiro($usuario){
        $partidas = $usuario->apuestas()->count();
        if($partidas >= 10 && $usuario->allow_withdraw == 0)
            $usuario->enableWithdraw();
    }

    /**
     * Esta funcion valida si el usuario hizo un deposito y despues de ello jugó 19 apuestas, la apuesta nro 20 del mes se duplica
     */
    private function validoBono20Partidas($usuario){
        $anio_actual = date('Y');
        $mes_actual = date('m');
        
        $ultimo_deposito = $usuario->depositos()->whereYear('created_at', $anio_actual)->whereMonth('created_at', $mes_actual)->whereIn('estado', [1])->orderBy('created_at', 'DESC')->first(); // Obtiene el ultimo deposito del mes

        if($ultimo_deposito === null){
            return false;
        } else {
            // Obtenemos la cantidad de apuestas realizadas despues del ultimo deposito
            $partidas = $usuario->apuestas()->whereYear('created_at', $anio_actual)->whereMonth('created_at', $mes_actual)->where('created_at', '>', date('Y-m-d', strtotime($ultimo_deposito->created_at)))->count();

            if( $partidas == 19 ){
                $ultimo_deposito->update(['estado'=>3]);
                return true;   
            } else {
                return false;
            }
        }
    }

    private function hasPublicAccess($usuario){
        $steamRepo = new SteamRepo();
        return $steamRepo->hasPublicAccess($usuario->steamid);
    }

    private function hasApuestasPendientes($usuario){
        $has_apuesta = $usuario->apuestas()->where('estado', 0)->first();
        $has_apuesta_test = $usuario->apuestas_test()->where('estado', 0)->first();

        return $has_apuesta !== null || $has_apuesta_test !== null;
    }
}