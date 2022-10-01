<?php namespace App\Repos;

use App\Models\Usuario;
use App\Models\Transaccion;
use App\Models\Deposito;
use App\Models\Retiro;
use App\Models\Test\DepositoTest;
use App\Models\Test\RetiroTest;
use Illuminate\Support\Str;

class BalanceRepo {

    private $usuario;

    public function crearDeposito($params){
        $depositoModel = $this->usuario->test_mode == 1 ? new DepositoTest() : new Deposito();
        $orden_id = 'DEP_'.strtoupper(Str::random(16));
        $deposito = $depositoModel->fill([
            'usuarioid' => $this->usuario->usuarioid,
            'monto' => $params['monto'],
            'ref_code' => $params['ref_code'],
            'estado' => 0,
            'proveedor' => $params['proveedor'],
            'orden_id' => $orden_id,
        ]);
        $deposito->save();
        return $deposito;
    }

    public function setUsuario(Usuario $user){
        $this->usuario = $user;
    }

    public function getRetiros(){
        $retiroModel = $this->usuario->test_mode == 1 ? RetiroTest::query() : Retiro::query();
        return $retiroModel->where('usuarioid', $this->usuario->usuarioid)->get();
    }

    public function getDepositoOrden($orden_id){
        $deposito = Deposito::where('orden_id', $orden_id)->first();
        if($deposito == null)
            $deposito = DepositoTest::where('orden_id', $orden_id)->first();
        
        return $deposito;
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
        if($this->usuario->{$this->usuario->balance_switch} < $params['monto'])
            throw new \Exception("No cuentas con saldo suficiente para realizar el retiro");

        $retiroModel = $this->usuario->test_mode == 1 ? new RetiroTest() : new Retiro();
        $params['usuarioid'] = $this->usuario->usuarioid;
        $retiroModel->fill($params);
        $retiroModel->save();

        $this->decrease($params['monto'], $this->usuario->balance_switch);
        return ['retiro'=>$retiroModel, 'saldo'=>$this->usuario->{$this->usuario->balance_switch}];
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