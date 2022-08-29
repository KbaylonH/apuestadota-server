<?php namespace App\Repos;

use Curl\Curl;

class DotaRepo {

    private $steamID;
    private $curl;

    public function __construct($steamID = null)
    {
        if($steamID !== null)
            $this->steamID = $steamID;

        $this->curl = new Curl("https://api.opendota.com/api/");
    }

    public function setSteamID($steamID){
        $this->steamID = $steamID;
    }

    public function getRecentMatches(){
        $this->curl->get("players/".$this->steamID."/recentMatches");
        return $this->curl->response;
    }

}