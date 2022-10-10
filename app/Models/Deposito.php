<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    protected $table = "deposito";
    protected $fillable = [ 'usuario_id', 'concepto', 'monto', 'ref_code', 'tipo', 'estado', 'proveedor', 'orden_id', 'tarjeta_marca', 'tarjeta_numero'];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }


}
