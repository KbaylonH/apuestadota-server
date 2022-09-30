<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    protected $table = "deposito";
    protected $fillable = [ 'usuarioid', 'monto', 'ref_code', 'estado', 'proveedor', 'orden_id', 'tarjeta_marca', 'tarjeta_numero'];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }
}
