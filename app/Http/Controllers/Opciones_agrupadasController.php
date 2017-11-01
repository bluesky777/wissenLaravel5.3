<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Models\Opcion_agrupada;
use App\Models\User;


class Opciones_agrupadasController extends Controller {



	public function putUpdateOrden(Request $request)
	{
		$user = User::fromToken();

		$sortHash = $request->input('sortHash');

		for($row = 0; $row < count($sortHash); $row++){
			foreach($sortHash[$row] as $key => $value){

				$opcion 		= Opcion_agrupada::find((int)$key);
				$opcion->orden 	= (int)$value;
				$opcion->save();

			}
		}


		return 'Ordenado con éxito';
	}




	public function postStore(Request $request)
	{
		$user = User::fromToken();

		$opcion = new Opcion_agrupada;
		$opcion->definicion 		= $request->input('definicion');
		$opcion->orden 				= $request->input('orden');
		$opcion->is_correct 		= $request->input('is_correct', false);
		$opcion->preg_agrupada_id 	= $request->input('preg_agrupada_id');
		$opcion->added_by 			= $user->id;
		
		$opcion->save();

		return $opcion;
	}




	public function deleteDestroy($id)
	{
		$user = User::fromToken();
		
		$opcion = Opcion_agrupada::findOrFail($id);
		$opcion->delete();

		return $opcion;
	}

}
