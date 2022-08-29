<?php namespace App\Repos;

use App\Models\Usuario;
use App\Models\Partida;

class PartidaRepo {

    private $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function create($monto){
        $partida = new Partida();
        $partida->usuarioid = $this->usuario->usuarioid;
        $partida->estado = '0'; // pendiente
        $partida->monto = $monto;
        $partida->save();

        return $partida;
    }

}