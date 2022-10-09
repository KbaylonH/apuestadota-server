<?php namespace App\Actions;

use App\Models\Deposito;
use App\Models\Usuario;
use App\Repos\BalanceRepo;
use Illuminate\Support\Facades\Log;

class EntregarBonoDepositoAction {

    public function execute(Deposito $deposito){
        $usuario = Usuario::find($deposito->usuarioid);
        
        $balanceRepo = new BalanceRepo();
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->increaseDisponible($deposito->monto);
        $deposito->update(['estado'=>1]);
    }

}