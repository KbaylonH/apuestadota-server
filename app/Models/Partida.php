<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = "partida";
    protected $primaryKey = "partidaid";
    protected $fillable = ['usuarioid','estado','match_id','monto','match_start_time','match_hero_id'];

    public function usuario(){
        return $this->belongsTo(Usuario::class);
    }
}
