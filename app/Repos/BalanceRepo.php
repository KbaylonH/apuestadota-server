<?php namespace App\Repos;

use App\Models\Usuario;
use App\Models\Transaccion;



class BalanceRepo {

    private $usuario;

    public function setUsuario(Usuario $user){
        $this->usuario = $user;
    }

    public function getAll(){
        return Transaccion::where('usuarioid', $this->usuario->usuarioid)->get();
    }

    public function insert($params, $tipo){
        $transaccion = new Transaccion([
            'usuarioid' => $this->usuario->usuarioid,
            'estado' => 1,
            'tipo' => $tipo,
            'metodo' => $params['metodo'],
            'monto' => $params['monto']
        ]);
        $transaccion->save();
        return $transaccion;
    }

    public function depositar($params){
        $transaccion = $this->insert($params, 'deposito');
        $this->increase($params['monto']);
        $this->usuario->save();

        return ['transaccion'=>$transaccion, 'saldo'=>$this->usuario->balance];
    }

    public function retirar($params){
        $transaccion = $this->insert($params, 'retiro');
        $this->decrease($params['monto']);
        return ['transaccion'=>$transaccion, 'saldo'=>$this->usuario->balance];
    }

    /*
    @param $monto decimal monto a descontar del saldo
    @param $field campo donde se hara el descuento, por defecto es 'balance'
    */
    public function decrease($monto, $field = 'balance'){
        $this->usuario->{$field} = $this->usuario->{$field} - $monto;
        $this->usuario->save();
    }

    /*
    @param $monto decimal monto a añadir al saldo
    @param $field campo donde se hara el incremento del saldo, por defecto es 'balance'
    */
    public function increase($monto, $field = 'balance'){
        $this->usuario->{$field} = $this->usuario->{$field} + $monto;
        $this->usuario->save();
    }

}