<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = "usuario";

    protected $fillable = [
        'nombre',
        'apellido',
        'steamid',
        'steamid64',
        'email',
        'telefono',
        'balance', // Saldo contable
        'balance_prueba',
        'balance_switch',
        'test_mode',
        'allow_withdraw',
        'foto',
        'login_at',
        'ref_code',
        'dni',
        'dni_url',
        'dni_status'
    ];

    protected $hidden = [ 'password', 'remember_token', ];

    protected $casts = [ 'email_verified_at' => 'datetime', ];

    public function enableWithdraw(){
        $this->allow_withdraw = 1;
        $this->save();
    }

    public function disableWithdraw(){
        $this->allow_withdraw = 0;
        $this->save();
    }

    public function apuestas_test(){
        return $this->hasMany(Test\ApuestaTest::class, 'usuario_id', 'id');
    }    

    public function apuestas(){
        return $this->hasMany(Apuesta::class, 'usuario_id', 'id');
    }

    public function depositos(){
        return $this->hasMany(Deposito::class, 'usuario_id', 'id');
    }

    public function retiros(){
        return $this->hasMany(Retiro::class, 'usuario_id', 'id');
    }
}
