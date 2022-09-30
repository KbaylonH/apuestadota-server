<?php namespace App\Actions;

use App\Models\Usuario;
use App\Models\Deposito;
use App\Repos\IzipayRepo;
use App\Repos\BalanceRepo;

class DepositarAction {

    public function execute($params, Usuario $usuario){
        try {
            $balanceRepo = new BalanceRepo();
            $params['usuarioid'] = $usuario->usuarioid;
    
            if(isset($params['ref_code']) && $params['ref_code'] !== ''){
                $this->checkDeposito($usuario);
            }
    
            $deposito = $balanceRepo->crearDeposito($params);
            switch($deposito->proveedor){
                case 'izipay':
                    $izipayRepo = (new IzipayRepo);
                    $izipay_token = $izipayRepo->getToken($deposito, $usuario);
                    return view('izipay_checkout', ['token'=>$izipay_token, 'izipay_client'=>$izipayRepo->getIzipayClient()]);
                    break;
            }
        } catch (\Exception $e) {
            return redirect()->route('payment.error')->withErrors(['error'=>$e->getMessage()]);
        }
    }

    private function checkDeposito($usuario){
        $exists = Deposito::where('usuarioid', $usuario->usuarioid)->whereIn('estado', [1,3])->first();
        if($exists !== null)
            throw new \Exception("Lo sentimos, solo se admite el código de referido en la primera recarga");
    }

}