<?php namespace App\Actions;

use App\Models\Usuario;
use App\Repos\IzipayRepo;
use App\Repos\BalanceRepo;

class CheckIzipayAction {

    public function execute($params){
        $izipayRepo = (new IzipayRepo);
        $balanceRepo = (new BalanceRepo);

        $client = $izipayRepo->getIzipayClient();
        if(!$client->checkHash()){
            throw new \Exception("Invalid IziPay signature");
        }

        $rawAnswer = $client->getParsedFormAnswer();
        $formAnswer = $rawAnswer['kr-answer'];
        $ordenID = $formAnswer['orderDetails']['orderId'];
        $orderStatus = $formAnswer['orderStatus'];

        $deposito = $balanceRepo->getDepositoOrden($ordenID);
        $user = Usuario::find($deposito->usuarioid);

        if($orderStatus == 'PAID'){
            $deposito->estado = 1;
            $deposito->tarjeta_marca = $formAnswer['transactions'][0]['transactionDetails']['paymentMethodDetails']['effectiveBrand'];
            $deposito->tarjeta_numero = $formAnswer['transactions'][0]['transactionDetails']['paymentMethodDetails']['id'];
            $deposito->save();
            $balanceRepo->setUsuario($user);
            $balanceRepo->increase($deposito->monto);
            
            if($deposito->ref_code !== null && $deposito->ref_code !== '')
                (new EntregarBonoReferidoAction)->execute($deposito);

            return redirect()->route('payment.success');
        } else {
            return redirect()->route('payment.error')->withErrors(['error'=>'Hubo un error al momento de realizar el pago. No se debito de tu tarjeta']);
        }
    }
}