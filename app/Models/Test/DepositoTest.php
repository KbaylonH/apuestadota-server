<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Model;

class DepositoTest extends Model
{
    protected $table = "deposito_prueba";
    protected $fillable = [ 'usuarioid', 'monto', 'ref_code', 'estado', 'proveedor', 'orden_id', 'tarjeta_marca', 'tarjeta_numero'];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }
}
