<?php namespace App\Actions;

/**
 * @author Kevin Baylón <kbaylonh@outlook.com>
 */

use App\Models\Usuario;
use App\Repos\BalanceRepo;
use App\Repos\PartidaRepo;
use Illuminate\Http\Request;

class ApostarAction {

    public function execute(Usuario $usuario, $params, Request $req){

        $monto = $params['monto'];

        $repo = new PartidaRepo($usuario);

        $emptyApuesta = $repo->getEmptyApuesta();

        if($emptyApuesta !== null)
            throw new \Exception("Solo se puede colocar como máximo 1 apuesta a la vez");

        if($usuario->balance < $monto)
            throw new \Exception("No cuenta con saldo disponible para realizar la apuesta");

        $partida = $repo->create($monto, [
            'ip_address' => $req->ip(),
            'isp' => gethostbyaddr($req->ip()),
            'pc_name' => gethostname()
        ]);

        // Descontamos del saldo
        $usuario->balance_prueba = $usuario->balance_prueba - $monto;
        $usuario->save();
        
        $balanceRepo =  (new BalanceRepo);
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->decrease($monto, 'balance_prueba');
        
        return $partida;
    }

}