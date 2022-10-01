<?php namespace App\Http\Controllers;

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
        $user->balance_prueba += 100;
        $user->save();

        return response()->json(['success'=>true, 'saldo'=>$user->balance_prueba]);
    }

    public function getRetiros(){
        $user = auth()->user();
        $this->repo->setUsuario($user);

        return response()->json( $this->repo->getRetiros() );
    }

    public function depositar(Request $request){
        $usuario = auth()->user();
        $params = $request->only('proveedor', 'monto', 'ref_code');
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
            \Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }

    }

}