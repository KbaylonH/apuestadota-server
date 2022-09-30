<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repos\BalanceRepo;

class BalanceController extends Controller {

    private $repo;

    public function __construct(BalanceRepo $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(){
        $user = auth()->user();
        $this->repo->setUsuario($user);

        return response()->json( $this->repo->getAll() );
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

            $params = $request->only('metodo', 'monto');
            $this->repo->setUsuario(auth()->user());

            $rpta = $this->repo->retirar($params);

            return response()->json( $rpta );

        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json(['error'=>$e->getMessage()], 400);
        }

    }

}