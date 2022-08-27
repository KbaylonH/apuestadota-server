<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    protected $table = "torneo";
    protected $primaryKey = "torneoid";
    protected $fillable = ['nombretorneo','inscritos','premio','estado'];

}
