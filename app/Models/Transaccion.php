<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    //
    protected $table = "transaccion";
    protected $primaryKey = "transaccionid";
    protected $fillable = ['estado','tipo','metodo','usuario_id','monto'];

    public function usuario(){
        return $this->belongsTo(Usuario::class);
    }
}
