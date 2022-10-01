<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Model;

class RetiroTest extends Model
{
    protected $table = "retiro_prueba";
    protected $fillable = ['usuarioid','nombre','metodo','monto','estado','nro_cuenta','nro_cuenta_inter'];
}
