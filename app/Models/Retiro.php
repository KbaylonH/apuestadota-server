<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    protected $table = "retiro";
    protected $fillable = ['usuarioid','nombre','metodo','monto','estado','nro_cuenta','nro_cuenta_inter'];
}
