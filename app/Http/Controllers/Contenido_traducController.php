<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;



use App\Models\Contenido_traduc;
use DB;




class Contenido_traducController extends Controller {

	
	public function putUpdate(Request $request)
	{
		
		if ($request->input('contenidos_traducidos')) {
			$contents_update = $request->input('contenidos_traducidos');

			foreach ($contents_update as $key => $content) {

				$conte = Contenido_traduc::find( $content['pg_traduc_id'] );
				$conte->definicion = $content['definicion'];

				$conte->save();

			}
		}else{

			$conte = Contenido_traduc::findOrFail($request->input('pg_traduc_id'));
			$conte->definicion = $request->input('definicion');

			$conte->save();

		}
		

		return $request->input('pg_traduc_id');
	}


	
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
