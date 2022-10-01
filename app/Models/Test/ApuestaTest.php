<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Model;

class ApuestaTest extends Model
{
    protected $table = "apuesta_prueba";
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
