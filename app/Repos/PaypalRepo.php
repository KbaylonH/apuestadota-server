<?php namespace App\Repos;

use App\Models\Deposito;
use Curl\Curl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaypalRepo {

    private $curl;
    private $config;

    public function __construct()
    {
        $paypal = DB::table('0_config')->find(7);
        $config = json_decode($paypal->valor, true);
        $this->curl = new Curl($config['endpoint']);
        $this->config = $config;
        if(!isset($config['access_token'])){
            $token = $this->generateToken();
            Log::info("Paypal token: " . $token);
            $this->config['access_token'] = $token;
            //DB::table('0_config')->where('id', 7)->update(['valor'=>json_encode($config)]);
        }
    }

    public function generateToken(){
        $this->curl->setHeader('Authorization', 'Basic ' . base64_encode($this->config['client_id'].':'.$this->config['secret']));
        $this->curl->post('/v1/oauth2/token', [
            'grant_type' => 'client_credentials'
        ]);

        if(!$this->curl->error){
            return $this->curl->response->access_token;
        } else {
            Log::info( json_encode($this->curl->response) );
        }

        return false;
    }

    public function checkPayment(Deposito $deposito, $transaction_id){
        $this->curl->setHeader('Authorization', 'Bearer ' . $this->config['access_token']);
        $this->curl->get('/v2/checkout/orders/' . $transaction_id);
        if(!$this->curl->error){
            return $this->curl->response;
        } else {
            Log::info( json_encode($this->curl->response) );
        }
        return true;
    }

}