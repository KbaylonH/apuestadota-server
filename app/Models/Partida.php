<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $table = "partida";
    protected $primaryKey = "partidaid";
    protected $fillable = [
        'usuarioid','estado','match_id',
        'monto', 'multiplicador', 'match_start_time','match_hero_id',
        'fecha_proceso','fecha_finalizado',
        'isp', 'ip_address', 'pc_name'];

    public function usuario(){
        return $this->belongsTo(Usuario::class);
    }
}
