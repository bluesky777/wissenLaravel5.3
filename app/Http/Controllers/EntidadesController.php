<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\User;
use App\Models\Entidad;




use Illuminate\Http\Request;

class EntidadesController extends Controller {


	public function anyIndex()
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		$enti = Entidad::todas($evento_id);
		return $enti;
	}



	
	public function postStore(Request $request)
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;


		$enti = new Entidad;
		$enti->evento_id 	= $evento_id;
		$enti->nombre 		= $request->input('nombre');
		$enti->lider_id 	= $request->input('lider_id', null);
		$enti->lider_nombre = $request->input('lider_nombre', null);
		$enti->logo_id 		= $request->input('logo_id', null);
		$enti->telefono 	= $request->input('telefono', null);
		$enti->alias 		= $request->input('alias', null);

		$enti->save();

		return $enti;

	}

	
	
	public function putUpdate(Request $request)
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		$enti = Entidad::findOrFail($request->input('id'));

		$enti->evento_id 	= $evento_id;
		$enti->nombre 		= $request->input('nombre');
		$enti->lider_id 	= $request->input('lider_id', null);
		$enti->lider_nombre = $request->input('lider_nombre', null);
		$enti->logo_id 		= $request->input('logo_id', null);
		$enti->telefono 	= $request->input('telefono', null);
		$enti->alias 		= $request->input('alias', null);

		$enti->save();

		return $enti;
	}



	public function deleteDestroy($id)
	{
		$entidad = Entidad::find($id);
		$entidad->delete();

		return $entidad;
	}
	
	public function deleteForcedelete($id)
	{
		$entidad = Entidad::onlyTrashed()->findOrFail($id);
		
		if ($entidad) {
			$entidad->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $entidad;
	
	}

	public function putRestore($id)
	{
		$entidad = Entidad::onlyTrashed()->findOrFail($id);

		if ($entidad) {
			$entidad->restore();
		}else{
			return \App::abort(400, 'Entidad no encontrada en la Papelera.');
		}
		return $entidad;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_entidades
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
