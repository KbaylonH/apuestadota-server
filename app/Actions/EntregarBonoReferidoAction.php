<?php namespace App\Actions;

use App\Models\Deposito;
use App\Models\Usuario;
use App\Repos\BalanceRepo;

class EntregarBonoReferidoAction {

    public function execute(Deposito $deposito){
        $usuario = Usuario::where('ref_code', $deposito->ref_code)->first();
        $balanceRepo = new BalanceRepo();
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->increase(1);
    }

}