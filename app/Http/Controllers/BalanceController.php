<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\DepositarRequest;
use Illuminate\Http\Request;

use App\Repos\BalanceRepo;

class BalanceController extends Controller {

    private $repo;

    public function __construct(BalanceRepo $repo)
    {
        $this->repo = $repo;
    }

    public function depositarTest(){
        $user = auth()->user();
        if($user->balance_prueba + 100 > 10000)
            return response()->json(['error'=>'El tope de saldo es de $10,000'], 400);
        $this->repo->setUsuario($user);
        $this->repo->increase(100, 'balance_prueba');
        return response()->json(['success'=>true, 'saldo'=>$user->balance_prueba]);
    }

    public function getResumen(){
        $user = auth()->user();
        $this->repo->setUsuario($user);
        return response()->json($this->repo->getResumen());
    }

    public function getRetiros(){
        $user = auth()->user();
        $this->repo->setUsuario($user);

        return response()->json( $this->repo->getRetiros() );
    }

    public function depositar(DepositarRequest $request){
        $usuario = auth()->user();
        $params = $request->validated();
        return (new \App\Actions\DepositarAction)->execute($params, $usuario);
    }

    public function checkIzipayPayment(Request $request){
        $params = $request->all();
        return (new \App\Actions\CheckIzipayAction)->execute($params);
    }

    public function retirar(Request $request){
        try {
            $params = $request->only('metodo', 'nombre', 'monto', 'nro_cuenta', 'nro_cuenta_inter');
            if($params['monto'] < 1)
                throw new \Exception("El monto debe ser mayor a 0");

            $this->repo->setUsuario(auth()->user());
            $rpta = $this->repo->retirar($params);
            return response()->json( $rpta );
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }

    }

}