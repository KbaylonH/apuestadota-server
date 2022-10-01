<?php namespace App\Actions;

use App\Models\Deposito;
use App\Models\Usuario;
use App\Repos\BalanceRepo;

class EntregarBonoDepositoAction {

    public function execute($deposito){
        $usuario = Usuario::find($deposito->usuarioid);
        $balanceRepo = new BalanceRepo();
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->increase($deposito->monto * 0.1, $usuario->balance_switch);
        $deposito->update(['estado'=>3]);
    }

}