<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\User;
use App\Models\Evento;
use App\Models\Inscripcion;



use Illuminate\Http\Request;

class InscripcionesController extends Controller {


	public function putInscribirVarios(Request $request)
	{	
		//User::fromToken();

		$usuarios = $request->input('usuarios');
		$categoria_id = $request->input('categoria_id');

		$cant = count($usuarios);

		$inscripciones_res = [];

		for($i=0; $i < $cant; $i++){

			$user_id = $usuarios[$i]['user_id'];
			$inscrip = Inscripcion::inscribir($user_id, $categoria_id);
			array_push($inscripciones_res, $inscrip);

		}

		return $inscripciones_res;
	}

	public function putDesinscribirVarios(Request $request)
	{	
		//User::fromToken();

		$usuarios = $request->input('usuarios');
		$categoria_id = $request->input('categoria_id');

		$cant = count($usuarios);

		for($i=0; $i < $cant; $i++){

			$user_id = $usuarios[$i]['user_id'];
			$inscrip = Inscripcion::desinscribir($user_id, $categoria_id);

		}

		return 'Desinscritos';
	}


	public function putInscribir(Request $request)
	{
		//User::fromToken();

		$usuario_id = $request->input('usuario_id');
		$categoria_id = $request->input('categoria_id');

		$inscrip = Inscripcion::inscribir($usuario_id, $categoria_id);

		return [$inscrip];

	}


	public function putDesinscribir(Request $request)
	{
		//User::fromToken();

		$usuario_id = $request->input('usuario_id');
		$categoria_id = $request->input('categoria_id');

		$inscrip = Inscripcion::desinscribir($usuario_id, $categoria_id);

		return array('usuario_id' => $usuario_id, 'categoria_id' => $categoria_id);

	}



}
