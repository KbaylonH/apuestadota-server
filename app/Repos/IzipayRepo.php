<?php namespace App\Repos;

use App\Models\Deposito;
use App\Models\Test\DepositoTest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Lyra\Client;

class IzipayRepo {

    private $client = null;
    public function __construct()
    {
        $config = file_get_contents(base_path('izipay_config.json'));
        $izipay_config = json_decode($config, true);
        Log::info($izipay_config);
        Client::setDefaultUsername($izipay_config['username']);
        Client::setDefaultPassword($izipay_config['password']);
        Client::setDefaultEndpoint($izipay_config['endpoint']);
        Client::setDefaultPublicKey($izipay_config['publickey']);
        Client::setDefaultSHA256Key($izipay_config['sha256key']);

        $this->client = new Client();
    }

    public function getIzipayClient(){
      return $this->client;
    }

    public function getToken(Deposito $deposito, Usuario $user){
        $finalAmount = $deposito->monto * 100;  
        $store = array("amount" => $finalAmount, 
        "currency" => "PEN", 
        "orderId" => $deposito->orden_id,
        "customer" => array(
          "email" => $user->email,
          "billingDetails" => array(
            "lastName" => $user->apellido,
            "address" => "1 rue de la paix",
            "zipCode" => '2',
            "city" => "Lima",
            "country" => "PE",
            "phone" => "0123456789"
          )
        ));
        $response = $this->client->post("V4/Charge/CreatePayment", $store);
        
        /* I check if there are some errors */
        if ($response['status'] != 'SUCCESS') {
            $error = $response['answer'];
            throw new \Exception("Error " . $error['errorCode'] . ": " . $error['errorMessage'] );
        }
        
        return $response["answer"]["formToken"];
    }
}