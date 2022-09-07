<?php namespace App\Actions;

use App\Models\Usuario;
use App\Repos\BalanceRepo;
use App\Repos\PartidaRepo;

class ApostarAction {

    public function execute(Usuario $usuario, $monto){

        $repo = new PartidaRepo($usuario);

        $emptyApuesta = $repo->getEmptyApuesta();

        if($emptyApuesta !== null)
            throw new \Exception("Solo se puede colocar como máximo 1 apuesta a la vez");

        if($usuario->balance < $monto)
            throw new \Exception("No cuenta con saldo disponible para realizar la apuesta");

        $partida = $repo->create($monto);

        // Descontamos del saldo
        $balanceRepo =  (new BalanceRepo);
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->decrease($monto);

        return $partida;
    }

}