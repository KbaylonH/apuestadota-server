<?php namespace App\Actions;

use App\Models\Deposito;
use App\Models\Usuario;
use App\Repos\BalanceRepo;
use Illuminate\Support\Facades\Log;

class EntregarBonoDepositoAction {

    public function execute(Deposito $deposito){
        $usuario = $deposito->usuario;
        $balanceRepo = new BalanceRepo();
        $balanceRepo->setUsuario($usuario);
        $deposito->update(['estado'=>1]);
    }

}