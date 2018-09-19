<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Disciplina_king;
use App\Models\Disciplina_traduc;
use App\Models\User;
use App\Models\Evento;


use Illuminate\Http\Request;

class DisciplinasController extends Controller {


	public function getDisciplinasEvento()
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$dis = Disciplina_king::where('evento_id', '=', $evento_id)->get();

		Disciplina_traduc::traducciones($dis); // Paso por referencia la disciplina_king
		
		return $dis;
	}


	public function getDisciplinasUsuario()
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		$dis = Disciplina_king::where('evento_id', '=', $evento_id)->get();

		
		Disciplina_traduc::traducciones($dis); // Paso por referencia la disciplina_king
		

		return $dis;
	}



	public function postStore(Request $request)
	{

		$user = User::fromToken();

		$evento_id = $user->evento_selected_id;
		$evento = Evento::find($evento_id);


		$event_idiomas = Evento::idiomas_all($evento->id);



		$dis = new Disciplina_king;
		$dis->nombre = "";
		$dis->evento_id = $evento_id;
		$dis->save();


		// $disc_traduc = []; 
		$cant_idioms = count($event_idiomas);


		for($i=0; $i < $cant_idioms; $i++){

			$dis_trad 					= new Disciplina_traduc;
			$dis_trad->nombre			= '';
			$dis_trad->disciplina_id	= $dis->id;
			$dis_trad->idioma_id		= $event_idiomas[$i]->id;
			$dis_trad->traducido 		= false;
			$dis_trad->save();

			//array_push($disc_traduc, $dis_trad->toArray()); // No guarda el nombre del idioma en cada traducción.

		}
		

		Disciplina_traduc::traducciones_single($dis); // Paso por referencia la disciplina_king

		return $dis;
	}


	public function putGuardar(Request $request)
	{
		$user = User::fromToken();

		$disc_traducidas = $request->input('disciplinas_traducidas');
		
		$disc_king = Disciplina_king::find($request->input('id'));

		if ($disc_king->nombre != $disc_traducidas[0]['nombre'] ) {
			$disc_king->nombre = $disc_traducidas[0]['nombre'];
			$disc_king->save();
		}


		foreach ($disc_traducidas as $key => $disc_traducida) {

			$disc_trad = Disciplina_traduc::find($disc_traducida['id']);

			$disc_trad->nombre 		= $disc_traducida['nombre'];
			$disc_trad->descripcion = $disc_traducida['descripcion'];
			$disc_trad->traducido 	= $disc_traducida['traducido'];
			$disc_trad->save();

		}
	
		return 'Disciplina y sus traducciones guardadas.';
	}



	public function deleteDestroy($id)
	{
		$disciplina = Disciplina_king::find($id);
		$disciplina->delete();

		return $disciplina;
	}
	
	public function deleteForcedelete($id)
	{
		$disciplina = Disciplina_king::onlyTrashed()->findOrFail($id);
		
		if ($disciplina) {
			$disciplina->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $disciplina;
	
	}

	public function putRestore($id)
	{
		$disciplina = Disciplina_king::onlyTrashed()->findOrFail($id);

		if ($disciplina) {
			$disciplina->restore();
		}else{
			return \App::abort(400, 'Disciplina_king no encontrado en la Papelera.');
		}
		return $disciplina;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_disciplinas_king
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
