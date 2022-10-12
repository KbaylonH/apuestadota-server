<?php namespace App\Repos;

class SteamRepo {

    private $api_key = "1EDC0D204A7716E809F0B2DABE207BE7";

    public function getSteamId64($postParams){
        $params = [
            'openid.assoc_handle' => $postParams['openid_assoc_handle'],
            'openid.signed'       => $postParams['openid_signed'],
            'openid.sig'          => $postParams['openid_sig'],
            'openid.ns'           => 'http://specs.openid.net/auth/2.0',
            'openid.mode'         => 'check_authentication',
        ];
    
        $signed = explode(',', $postParams['openid_signed']);
            
        foreach ($signed as $item) {
            $val = $postParams['openid_'.str_replace('.', '_', $item)];
            $params['openid.'.$item] = stripslashes($val);
        }
    
        $data = http_build_query($params);
    
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Accept-language: en\r\n".
                "Content-type: application/x-www-form-urlencoded\r\n".
                'Content-Length: '.strlen($data)."\r\n",
                'content' => $data,
            ],
        ]);
    
        // En otros lenguajes, recurrir a funciones/librerias que hagan HTTP REQUEST
        $result = file_get_contents('https://steamcommunity.com/openid/login', false, $context);
    
        // Aqui es donde guardaremos el steamId del usuario
        $steamID64 = null;
    
        // Con esto validamos si la respuesta al request contiene is_valid:true
        if(preg_match("#is_valid\s*:\s*true#i", $result)){
            preg_match('#^https://steamcommunity.com/openid/id/([0-9]{17,25})#', $postParams['openid_claimed_id'], $matches);
            $steamID64 = is_numeric($matches[1]) ? $matches[1] : 0;
        } else {
            throw new \Exception("No fue posible validar la solicitud");
        }

        return $steamID64;
    }

    public function getUser($steamID64){
        $response = file_get_contents('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$this->api_key.'&steamids='.$steamID64);
        $response = json_decode($response,true);
        return $response['response']['players'][0];
    }

    public function hasPublicAccess($steamID){
        $response = file_get_contents('https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/v1/?key='.$this->api_key.'&account_id='.$steamID);
        $response = json_decode($response,true);
        return $response['result']['status'] == 1;
    }

}
