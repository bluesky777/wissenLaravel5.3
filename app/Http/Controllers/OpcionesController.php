<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Models\Pregunta_king;
use App\Models\Pregunta_traduc;
use App\Models\Opcion;
use App\Models\Evento;
use App\Models\User;




class OpcionesController extends Controller {

	

	
	public function postStore(Request $request)
	{
		$user = User::fromToken();

		$opcion = new Opcion;
		$opcion->definicion 		= $request->input('definicion');
		$opcion->orden 				= $request->input('orden');
		$opcion->is_correct 		= $request->input('is_correct', false);
		$opcion->pregunta_traduc_id = $request->input('preg_traduc_id');
		$opcion->added_by 			= $user->id;

		$opcion->save();

		return $opcion;
	}

	
	
	public function putUpdateOrden(Request $request)
	{
		$user = User::fromToken();

		$sortHash = $request->input('sortHash');

		for($row = 0; $row < count($sortHash); $row++){
			foreach($sortHash[$row] as $key => $value){

				$opcion 		= Opcion::find((int)$key);
				$opcion->orden 	= (int)$value;
				$opcion->save();

			}
		}


		return 'Ordenado con éxito';
	}


	public function deleteDestroy($id)
	{
		$user = User::fromToken();
		
		$opcion = Opcion::findOrFail($id);
		$opcion->delete();

		return $opcion;
	}

}
