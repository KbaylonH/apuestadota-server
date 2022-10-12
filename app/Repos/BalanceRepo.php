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
        $orden_id = 'DEP_'.strtoupper(Str::random(16));
        $deposito = $this->usuario->depositos()->create([
            'monto' => $params['monto'],
            'concepto' => $params['concepto'],
            'ref_code' => $params['ref_code'],
            'tipo' => isset($params['tipo']) ? $params['tipo'] : 1,
            'estado' => isset($params['estado']) ? $params['estado'] : 0,
            'proveedor' => $params['proveedor'],
            'orden_id' => $orden_id,
        ]);
        return $deposito;
    }

    public function setUsuario(Usuario $user){
        $this->usuario = $user;
    }

    public function getResumen(){
        $sql = "
        SELECT * FROM (
            SELECT UNIX_TIMESTAMP(created_at) AS 'fecha', monto, concepto FROM deposito WHERE usuario_id = ? AND estado > 0
            UNION
            SELECT UNIX_TIMESTAMP(created_at) AS 'fecha', monto * -1, 'RETIRO' AS 'concepto' FROM retiro WHERE usuario_id = ?
            UNION
            SELECT UNIX_TIMESTAMP(created_at) AS 'fecha', IF(estado=1,monto*0.4,monto*-1) AS 'monto',  CASE estado WHEN 0 then 'EN PROCESO' WHEN 1 THEN 'APUESTA GANADA' ELSE 'APUESTA PERDIDA' END AS 'concepto' FROM apuesta WHERE usuario_id = ?
            ) a ORDER BY fecha DESC
        ";
        return DB::select($sql, [$this->usuario->id, $this->usuario->id, $this->usuario->id]);
    }

    public function getRetiros(){
        return $this->usuario->retiros()->orderBy('created_at', 'DESC')->get();
    }

    public function getDepositoOrden($orden_id){
        return Deposito::where('orden_id', $orden_id)->first();
    }

    public function retirar($params){
        if($this->usuario->allow_withdraw == 0)
            throw new \Exception("Por el momento no puedes hacer retiros ya que usaste un código de referidos y tienes que realizar por lo menos 10 apuestas antes de poder retirar");

        if($this->usuario->balance < $params['monto'])
            throw new \Exception("No cuentas con saldo suficiente para realizar el retiro");

        $retiro = $this->usuario->retiros()->create($params);
        $this->decrease($params['monto']);
        return ['retiro'=>$retiro, 'saldo'=>$this->usuario->balance];
    }

    /*
    @param $monto float monto a descontar del saldo
    @param $field string Campo donde se hara el descuento, por defecto es 'balance'
    */
    public function decrease($monto, $field = 'balance'){
        $this->usuario->{$field} = $this->usuario->{$field} - $monto;
        $this->usuario->save();
    }

    /*
    @param $monto float Monto a añadir al saldo
    @param $field string Campo donde se hara el incremento del saldo, por defecto es 'balance'
    */
    public function increase($monto, $field = 'balance'){
        $this->usuario->{$field} = $this->usuario->{$field} + $monto;
        $this->usuario->save();
    }
}