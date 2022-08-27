<?php namespace App\Repos;

use App\Models\Torneo;

class TorneoRepo {

    public function getAll(){
        return Torneo::get();
    }

}