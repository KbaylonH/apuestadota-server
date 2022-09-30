<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Model;

class RetiroTest extends Model
{
    protected $table = "retiro_prueba";
    protected $primaryKey = "retiroid";
    protected $fillable = ['transaccionid'];

    public function transaccion(){
        return $this->belongsTo(Transaccion::class);
    }
}
