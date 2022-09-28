<?php namespace App\Actions;

use App\Http\Requests\UpdateUser;
use App\Repos\UsuarioRepo;
use Illuminate\Support\Facades\Storage;

class UpdateProfile {

    public $repo;
    
    public function __construct(){
        $this->repo = new UsuarioRepo();
    }

    public function execute(UpdateUser $req){
        $user = auth()->user();
        $params = $req->validated();

        $path = Storage::disk('public')->putFile('', $req->file('dni_file'));

        $this->repo->update($user, [
            'nombre' => $params['nombre'],
            'apellido' => $params['apellido'],
            'email' => $params['email'],
            'dni' => $params['dni'],
            'dni_url' => $path,
            'dni_status' => 1
        ]);
    }
}