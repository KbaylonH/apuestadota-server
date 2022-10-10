<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    protected $table = "retiro";
    protected $fillable = ['usuario_id','nombre','metodo','monto','estado','nro_cuenta','nro_cuenta_inter'];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }
}
