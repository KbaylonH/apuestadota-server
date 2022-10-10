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
            'tipo' => 2,
            'estado' => 1,
            'proveedor' => 'sistema',
            'orden_id' => '',
        ]);
        $balanceRepo->increase($monto);
        $this->entregarAdicional($deposito);
    }

    private function entregarAdicional(Deposito $deposito){
        $usuario = $deposito->usuario;
        $monto = $deposito->monto * 0.1;
        $balanceRepo = new BalanceRepo();
        $balanceRepo->setUsuario($usuario);
        $balanceRepo->crearDeposito([
            'monto' => $monto,
            'concepto' => 'BONO 10%',
            'ref_code' => '',
            'tipo' => 3,
            'estado' => 1, // Retenido
            'proveedor' => 'sistema',
            'orden_id' => '',
        ]);
        $balanceRepo->increase($monto);
        $usuario->disableWithdraw();
    }

}