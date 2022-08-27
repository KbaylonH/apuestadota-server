<?php namespace App\Repos;

use Curl\Curl;

class DotaRepo {

    private $steamID64;
    private $curl;

    public function __construct($steamID64 = null)
    {
        if($steamID64 !== null)
            $this->steamID64 = $steamID64;

        $this->curl = new Curl("https://api.opendota.com/api/");
    }

    public function setSteamId64($steamID64){
        $this->steamID64 = $steamID64;
    }

    public function getRecentMatches(){
        $this->curl->get("players/".$this->steamID64."/recentMatches");
        return $this->curl->response;
    }

}