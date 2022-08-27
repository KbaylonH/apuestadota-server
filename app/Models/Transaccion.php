<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    //
    protected $table = "transaccion";
    protected $primaryKey = "transaccionid";
    protected $fillable = ['estado','tipo','usaurioid'];

    public function usuario(){
        return $this->belongsTo(Usuario::class);
    }
}
