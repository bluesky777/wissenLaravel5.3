<?php namespace App\Http\Controllers\Campamento;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Campamento\Usuario;



use Request;
use DB;


class LoginController extends Controller {

	
	public function postEntrar()
	{
		$usuario = Usuario::where('username', Request::input('username'))->where('password', Request::input('password'))->where('active', true)->first();

		if ($usuario) {
			# code...
		}else{
			return response()->json(['error' => 'invalid_credentials'], 400);
		}

		return ["usuario" => $usuario];
	}
	
	
	public function postRegistrar()
	{
		$nombre_completo 	= Request::input('nombre_completo');
		$username 			= Request::input('username');
		$password 			= Request::input('password');
		$grado 				= Request::input('grado');

		$usuario 					= new Usuario;
		$usuario->nombre_completo 	= $nombre_completo;
		$usuario->username 			= $username;
		$usuario->password 			= $password;
		$usuario->grado 			= $grado;
		$usuario->save();

		return ["usuario" => $usuario];
	}
	
}
