<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class Categorias_traducController extends Controller {

	
	public function deleteDestroy($id)
	{
		$evento = Evento::find($id);
		$evento->delete();

		return $evento;
	}
	
	public function deleteForcedelete($id)
	{
		$evento = Evento::onlyTrashed()->findOrFail($id);
		
		if ($evento) {
			$evento->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	
	}

	public function putRestore($id)
	{
		$evento = Evento::onlyTrashed()->findOrFail($id);

		if ($evento) {
			$evento->restore();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_eventos
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
