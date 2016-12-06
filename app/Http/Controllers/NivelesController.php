<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\Nivel_king;
use App\Models\Nivel_traduc;
use App\Models\User;
use App\Models\Evento;



use Illuminate\Http\Request;

class NivelesController extends Controller {


	public function getNivelesEvento()
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$nivel = Nivel_king::where('evento_id', '=', $evento_id)->get();

		Nivel_traduc::traducciones($nivel); // Paso por referencia la nivel_king
		
		return $nivel;
	}


	public function getNivelesUsuario()
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		$nivel = Nivel_king::where('evento_id', '=', $evento_id)->get();

		
		Nivel_traduc::traducciones($nivel); // Paso por referencia la nivel_king
		

		return $nivel;
	}



	public function postStore(Request $request)
	{

		$user = User::fromToken();

		$evento_id = $user->evento_selected_id;
		$evento = Evento::find($evento_id);


		$event_idiomas = Evento::idiomas_all($evento->id);



		$nivel = new Nivel_king;
		$nivel->nombre = "";
		$nivel->evento_id = $evento_id;
		$nivel->save();


		// $nivel_traduc = []; 
		$cant_idioms = count($event_idiomas);


		for($i=0; $i < $cant_idioms; $i++){

			$nivel_trad 				= new Nivel_traduc;
			$nivel_trad->nombre			= '';
			$nivel_trad->nivel_id		= $nivel->id;
			$nivel_trad->idioma_id		= $event_idiomas[$i]->id;
			$nivel_trad->traducido 		= false;
			$nivel_trad->save();

			//array_push($nivel_traduc, $nivel_trad->toArray()); // No guarda el nombre del idioma en cada traducción.

		}
		

		Nivel_traduc::traducciones_single($nivel); // Paso por referencia el nivel_king

		return $nivel;
	}


	public function putGuardar(Request $request)
	{
		$user = User::fromToken();

		$nivel_traducidos = $request->input('niveles_traducidos');
		
		$nivel_king = Nivel_king::find($request->input('id'));

		if ($nivel_king->nombre != $nivel_traducidos[0]['nombre'] ) {
			$nivel_king->nombre = $nivel_traducidos[0]['nombre'];
			$nivel_king->save();
		}


		foreach ($nivel_traducidos as $key => $nivel_traducido) {

			$nivel_trad = Nivel_traduc::find($nivel_traducido['id']);

			$nivel_trad->nombre 		= $nivel_traducido['nombre'];
			$nivel_trad->descripcion 	= $nivel_traducido['descripcion'];
			$nivel_trad->traducido 		= $nivel_traducido['traducido'];
			$nivel_trad->save();

		}
	
		return 'Nivel y sus traducciones guardadas.';
	}


	public function putUpdate($id)
	{
		//
	}


	
	public function deleteDestroy($id)
	{
		$nivel = Nivel_king::find($id);
		$nivel->delete();

		return $nivel;
	}
	
	public function deleteForcedelete($id)
	{
		$nivel = Nivel_king::onlyTrashed()->findOrFail($id);
		
		if ($nivel) {
			$nivel->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $nivel;
	
	}

	public function putRestore($id)
	{
		$nivel = Nivel_king::onlyTrashed()->findOrFail($id);

		if ($nivel) {
			$nivel->restore();
		}else{
			return \App::abort(400, 'Nivel_king no encontrado en la Papelera.');
		}
		return $nivel;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_niveles_king
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
