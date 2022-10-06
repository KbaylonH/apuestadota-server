<?php namespace App\Actions;

use App\Models\Deposito;
use App\Models\Usuario;
use App\Repos\BalanceRepo;

class EntregarBonoReferidoAction {

    public function execute(Deposito $deposito){
        $monto = 1;
        $usuario = Usuario::where('ref_code', $deposito->ref_code)->first();
        $balanceRepo = new BalanceRepo();
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->crearDeposito([
            'monto' => $monto,
            'concepto' => 'BONO POR REFERIDO',
            'ref_code' => '',
            'estado' => 1,
            'proveedor' => '',
            'orden_id' => '',
        ]);
        $balanceRepo->increase($monto);
    }

}