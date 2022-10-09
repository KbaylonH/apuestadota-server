<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = "usuario";

    protected $primaryKey = "usuarioid";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'balance_disp', // Saldo disponible
        'test_mode',
        'partida',
        'foto',
        'login_at',
        'ref_code',
        'dni',
        'dni_url',
        'dni_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
