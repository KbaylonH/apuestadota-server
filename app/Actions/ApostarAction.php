<?php namespace App\Actions;

/**
 * @author Kevin Baylón <kbaylonh@outlook.com>
 */

use App\Models\Usuario;
use App\Models\Deposito;
use App\Models\Partida;
use App\Repos\BalanceRepo;
use App\Repos\PartidaRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApostarAction {

    public function execute(Usuario $usuario, $params, Request $req){
        $multiplicador = 1.4;
        $monto = $params['monto'];

        $repo = new PartidaRepo($usuario);

        $emptyApuesta = $repo->getEmptyApuesta();

        if($emptyApuesta !== null)
            throw new \Exception("Solo se puede colocar como máximo 1 apuesta a la vez");

        if($usuario->{$usuario->balance_switch} < $monto)
            throw new \Exception("No cuenta con saldo disponible para realizar la apuesta");

        // Si el usuario realizó 19 apuestas durante el mes, para esta apuesta el multiplicador de apuesta es 2
        if( $usuario->test_mode == 0 && $this->validoBono20Partidas($usuario) )
            $multiplicador = 2;

        $partida = $repo->create($monto, $multiplicador, [
            'ip_address' => $req->ip(),
            'isp' => gethostbyaddr($req->ip()),
            'pc_name' => gethostname()
        ]);

        // Descontamos del saldo        
        $balanceRepo =  (new BalanceRepo);
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->decrease($monto, $usuario->balance_switch);
        if($usuario->test_mode == 0){
            $balanceRepo->decreaseDisponible($monto);
            $this->entregarBonoAdicional($usuario);
        }
        return $partida;
    }

        /**
     * Esta funcion detecta si hay un deposito retenido por recarga con codigo de referido, de cumplirse la condicion de mas de 10 partidas realizadas, se libera el deposito
     */
    private function entregarBonoAdicional($usuario){
        $deposito = Deposito::where('usuarioid', $usuario->usuarioid)->where('estado', 2)->where('tipo', 3)->first();

        if($deposito !== null){
            $partidas = Partida::where('usuarioid', $usuario->usuarioid)->whereRaw('DATE(created_at) >= ?', [date('Y-m-d', strtotime($deposito->created_at))])->count();
            Log::info("nro partidas: " . $partidas);
            if($partidas >= 1){
                (new \App\Actions\EntregarBonoDepositoAction())->execute($deposito);
            }
        } else {
            Log::info("No se halló el deposito");
        }
    }

    /**
     * Esta funcion valida si el usuario hizo un deposito y despues de ello jugó 19 apuestas, la apuesta nro 20 del mes se duplica
     */
    private function validoBono20Partidas($usuario){
        $depositoModel = Deposito::query();
        $apuestaModel = Partida::query();
        $anio_actual = date('Y');
        $mes_actual = date('m');
        
        $ultimo_deposito = $depositoModel->where('usuarioid', $usuario->usuarioid)->whereYear('created_at', $anio_actual)->whereMonth('created_at', $mes_actual)->whereIn('estado', [1])->orderBy('created_at', 'DESC')->first(); // Obtiene el ultimo deposito del mes

        if($ultimo_deposito === null){
            return false;
        } else {
            // Obtenemos la cantidad de apuestas realizadas despues del ultimo deposito
            $partidas = $apuestaModel->where('usuarioid', $usuario->usuarioid)->whereYear('created_at', $anio_actual)->whereMonth('created_at', $mes_actual)->where('created_at', '>', date('Y-m-d', strtotime($ultimo_deposito->created_at)))->count();

            if( $partidas == 19 ){
                $ultimo_deposito->update(['estado'=>3]);
                return true;   
            } else {
                return false;
            }
        }
    }
}