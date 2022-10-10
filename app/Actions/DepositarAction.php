<?php namespace App\Actions;

use App\Models\Usuario;
use App\Models\Deposito;
use App\Models\Test\DepositoTest;
use App\Repos\IzipayRepo;
use App\Repos\PaypalRepo;
use App\Repos\BalanceRepo;
use Illuminate\Support\Facades\Log;

class DepositarAction {

    public $balanceRepo;

    public function __construct()
    {
        $this->balanceRepo = new BalanceRepo();
    }

    public function execute($params, Usuario $usuario){
        try {
            if(isset($params['ref_code']) && $params['ref_code'] !== null &&$params['ref_code'] !== ''){
                $this->checkDeposito($usuario, $params['ref_code']);
            }
            $this->balanceRepo->setUsuario($usuario);
            $params['concepto'] = 'DEPÓSITO';
            $deposito = $this->balanceRepo->crearDeposito($params);
            switch($deposito->proveedor){
                case 'izipay':
                    return $this->depositarIzipay($deposito, $usuario);
                    break;
                case 'paypal':
                    return $this->depositarPaypal($deposito, $params);
                    break;
            }
        } catch (\Exception $e) {
            Log::error($e);
            if( !request()->expectsJson() )
                return redirect()->away(config('app.url_payment_error') . '?error=' . urlencode($e->getMessage()));
            else
                return response()->json(['error'=>$e->getMessage()], 400);
        }
    }

    private function checkDeposito($usuario, $ref_code){
        $exists = $usuario->depositos()->whereIn('estado', [1,3])->first();
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

    private function depositarIzipay(Deposito $deposito, Usuario $usuario){
        $izipayRepo = (new IzipayRepo);
        $izipay_token = $izipayRepo->getToken($deposito, $usuario);
        return view('izipay_checkout', ['token'=>$izipay_token, 'izipay_client'=>$izipayRepo->getIzipayClient()]);
    }

    private function depositarPaypal(Deposito $deposito, $params){
        $paypalRepo = (new PaypalRepo);
        $result = $paypalRepo->checkPayment($deposito, $params['transaction_id']);
        if(isset($result->id)){
            $deposito->estado = 1;
            $deposito->orden_id = 'PAYPAL_' . $params['transaction_id'];
            $deposito->save();

            $this->balanceRepo->increase($deposito->monto);

            if($deposito->ref_code !== '' && $deposito->ref_code !== null)
                (new EntregarBonoReferidoAction)->execute($deposito);
            
            return response()->json(['success'=>true]);
        } else {
            throw new \Exception("El ID de orden recibido no es válido");
        }
    }

}