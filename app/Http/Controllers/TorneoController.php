<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repos\TorneoRepo;

class TorneoController extends Controller
{
    //
    public function getAll(){
        return response()->json((new TorneoRepo)->getAll());
    }
}
