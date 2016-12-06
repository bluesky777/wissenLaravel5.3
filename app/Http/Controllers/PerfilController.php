<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\User;
use \Hash;



use Request;
use DB;


class PerfilController extends Controller {

	public function putCambiarpassword($id)
	{
		$user = User::fromToken();
		$perfil = User::findOrFail($id);



		if (Request::has('oldpassword') || Request::has('oldpassword') == '') {
			if (! Hash::check((string)Request::input('oldpassword'), $perfil->password))
			{
				abort(400, 'Contraseña antigua es incorrecta');
			}

		}

		$perfil->password = Hash::make((string)Request::input('password'));

		$perfil->save();
		return (string)Request::input('password');
		
	}	



	public function putCambiarRutaImagenes()
	{
		$user 			= User::fromToken();
		$ruta_anterior 	= Request::input('ruta_anterior');
		$ruta_nueva 	= Request::input('ruta_nueva');

		if ($ruta_anterior && $ruta_nueva) {
			
			$consulta = "UPDATE ws_pregunta_traduc
						SET enunciado = REPLACE(enunciado, :ruta_anterior, :ruta_nueva)
						WHERE enunciado LIKE ('%" . $ruta_anterior . "%');";

			$rutas = DB::select($consulta, [':ruta_anterior' => $ruta_anterior, ':ruta_nueva' => $ruta_nueva] );

			return 'Rutas cambiadas';
		}else{
			return "Debe poner algo";
		}

		
	}	


}
