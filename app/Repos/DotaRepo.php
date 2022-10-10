<?php namespace App\Repos;

use Curl\Curl;

class DotaRepo {

    private $steamID;
    private $curl;
    private $api_key = "84b24242-4fe0-46ac-87a7-efca3ff28117";

    public function __construct($steamID = null)
    {
        if($steamID !== null)
            $this->steamID = $steamID;

        $this->curl = new Curl("https://api.opendota.com/api/");
    }

    public function findMatch($matchID){
        $this->curl->get("matches/".$matchID."?api_key=".$this->api_key);
        return $this->curl->response;
    }

    public function setSteamID($steamID){
        $this->steamID = $steamID;
    }

    public function getRecentMatches(){
        $this->curl->get("players/".$this->steamID."/recentMatches?api_key=".$this->api_key);
        return $this->curl->response;
    }

}