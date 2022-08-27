<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    protected $table = "retiro";
    protected $primaryKey = "retiroid";
    protected $fillable = ['transaccionid'];

    public function transaccion(){
        return $this->belongsTo(Transaccion::class);
    }
}
