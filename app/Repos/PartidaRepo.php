<?php namespace App\Repos;

use App\Models\Usuario;
use App\Models\Partida;

class PartidaRepo {

    private $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function create($monto, $params = null){
        $partida = new Partida();
        $partida->usuarioid = $this->usuario->usuarioid;
        $partida->estado = '0'; // pendiente
        $partida->monto = $monto;

        if(isset($params['isp']))
        $partida->isp = $params['isp'];

        if(isset($params['pc_name']))
        $partida->pc_name = $params['pc_name'];

        if(isset($params['ip_address']))
        $partida->ip_address = $params['ip_address'];

        if(isset($params['ganancia']))
        $partida->ganancia = $params['ganancia'];

        $partida->save();

        return $partida;
    }

    public function find($partida_id){
        return Partida::where('usuarioid', $this->usuario->usuarioid)->where('partidaid', $partida_id)->first();
    }

    public function search(){
        return Partida::where('usuarioid', $this->usuario->usuarioid)->orderBy('created_at', 'DESC')->get();
    }

    public function getEmptyApuesta(){
        return Partida::where('usuarioid', $this->usuario->usuarioid)->where('estado','0')->first();
    }
}