<?php namespace App\Repos;

use App\Models\Usuario;
use App\Models\Transaccion;
use App\Models\Deposito;
use App\Models\Retiro;
use App\Models\Test\DepositoTest;
use App\Models\Test\RetiroTest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BalanceRepo {

    private $usuario;

    public function crearDeposito($params){
        $depositoModel = new Deposito();
        $orden_id = 'DEP_'.strtoupper(Str::random(16));
        $deposito = $depositoModel->fill([
            'usuarioid' => $this->usuario->usuarioid,
            'monto' => $params['monto'],
            'concepto' => $params['concepto'],
            'ref_code' => $params['ref_code'],
            'tipo' => isset($params['tipo']) ? $params['tipo'] : 1,
            'estado' => isset($params['estado']) ? $params['estado'] : 0,
            'proveedor' => $params['proveedor'],
            'orden_id' => $orden_id,
        ]);
        $deposito->save();
        return $deposito;
    }

    public function setUsuario(Usuario $user){
        $this->usuario = $user;
    }

    public function getResumen(){
        $sql = "
        SELECT * FROM (
            SELECT UNIX_TIMESTAMP(created_at) AS 'fecha', monto, concepto FROM deposito WHERE usuarioid = ? AND estado > 0
            UNION
            SELECT UNIX_TIMESTAMP(created_at) AS 'fecha', monto * -1, 'RETIRO' AS 'concepto' FROM retiro WHERE usuarioid = ?
            UNION
            SELECT UNIX_TIMESTAMP(created_at) AS 'fecha', IF(estado=1,monto,monto*-1) AS 'monto', IF(estado=1,'APUESTA GANADA', 'APUESTA PERDIDA') AS 'concepto' FROM partida WHERE usuarioid = ?
            ) a ORDER BY fecha DESC
        ";
        return DB::select($sql, [$this->usuario->usuarioid, $this->usuario->usuarioid, $this->usuario->usuarioid]);
    }

    public function getRetiros(){
        $retiroModel = Retiro::query();
        return $retiroModel->where('usuarioid', $this->usuario->usuarioid)->orderBy('created_at', 'DESC')->get();
    }

    public function getDepositoOrden($orden_id){
        return Deposito::where('orden_id', $orden_id)->first();
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
        if($this->usuario->balance_disp < $params['monto'])
            throw new \Exception("No cuentas con saldo suficiente para realizar el retiro");

        $retiroModel = new Retiro();
        $params['usuarioid'] = $this->usuario->usuarioid;
        $retiroModel->fill($params);
        $retiroModel->save();

        $this->decrease($params['monto']);
        $this->decreaseDisponible($params['monto']);
        return ['retiro'=>$retiroModel, 'saldo'=>$this->usuario->balance];
    }

    /*
    @param $monto float monto a descontar del saldo
    @param $field string Campo donde se hara el descuento, por defecto es 'balance'
    */
    public function decrease($monto, $field = 'balance'){
        $this->usuario->{$field} = $this->usuario->{$field} - $monto;
        $this->usuario->save();
    }

    /**
     * @param $monto float Monto a descontar del saldo disponible
     */
    public function decreaseDisponible($monto){
        $this->decrease($monto, 'balance_disp');
    }

    /*
    @param $monto float Monto a añadir al saldo
    @param $field string Campo donde se hara el incremento del saldo, por defecto es 'balance'
    */
    public function increase($monto, $field = 'balance'){
        $this->usuario->{$field} = $this->usuario->{$field} + $monto;
        $this->usuario->save();
    }

    /**
     * @param $monto float Monto a añadir al saldo disponible
     */
    public function increaseDisponible($monto){
        $this->increase($monto, 'balance_disp');
    }

}