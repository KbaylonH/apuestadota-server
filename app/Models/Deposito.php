<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    protected $table = "deposito";
    protected $primaryKey = "depositoid";
    protected $fillable = ['transaccionid'];

    public function transaccion(){
        return $this->belongsTo(Transaccion::class);
    }
}
