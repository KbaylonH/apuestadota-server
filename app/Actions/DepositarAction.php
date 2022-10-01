<?php namespace App\Actions;

use App\Models\Usuario;
use App\Models\Deposito;
use App\Models\Test\DepositoTest;
use App\Repos\IzipayRepo;
use App\Repos\BalanceRepo;

class DepositarAction {

    public function execute($params, Usuario $usuario){
        try {
            $balanceRepo = new BalanceRepo();
    
            if(isset($params['ref_code']) && $params['ref_code'] !== ''){
                $this->checkDeposito($usuario, $params['ref_code']);
            }
            
            $balanceRepo->setUsuario($usuario);
            $deposito = $balanceRepo->crearDeposito($params);
            switch($deposito->proveedor){
                case 'izipay':
                    $izipayRepo = (new IzipayRepo);
                    $izipay_token = $izipayRepo->getToken($deposito, $usuario);
                    return view('izipay_checkout', ['token'=>$izipay_token, 'izipay_client'=>$izipayRepo->getIzipayClient()]);
                    break;
            }
        } catch (\Exception $e) {
            return redirect()->away(config('app.url_payment_error') . '?error=' . urlencode($e->getMessage()));
        }
    }

    private function checkDeposito($usuario, $ref_code){
        $exists = Deposito::where('usuarioid', $usuario->usuarioid)->whereIn('estado', [1,3])->first();
        if($exists !== null) {
            throw new \Exception("Lo sentimos, solo se admite el código de referido en la primera recarga");
        } else if( $usuario->ref_code == $ref_code ) {
            throw new \Exception("Lo sentimos, solo se admite el código de referido de otros usuarios");
        } else {
            $exists = Usuario::where('ref_code', $ref_code)->first();
            if($exists == null)
                throw new \Exception("El código de referido ingresado no es válido");
        }
    }

}