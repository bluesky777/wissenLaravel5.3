<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\Categoria_king;
use App\Models\Categoria_traduc;
use App\Models\User;
use App\Models\Evento;
use App\Models\Evaluacion;



use Illuminate\Http\Request;

class CategoriasController extends Controller {

	public function getCategoriasEvento()
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$categoria = Categoria_king::where('evento_id', $evento_id)->get();

		Categoria_traduc::traducciones($categoria); // Paso por referencia la categoria_king
		
		return $categoria;
	}


	public function getCategoriasUsuario()
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		$categoria = Categoria_king::where('evento_id', '=', $evento_id)->get();

		
		Categoria_traduc::traducciones($categoria); // Paso por referencia la categoria_king
		

		return $categoria;
	}



	public function postStore(Request $request)
	{

		$user = User::fromToken();

		$evento_id = $user->evento_selected_id;
		$evento = Evento::find($evento_id);


		$event_idiomas = Evento::idiomas_all($evento->id);



		$categoria = new Categoria_king;
		$categoria->nombre = ""; // lo dejo sin nada para la comprobación y cambiar el nombre a la evaluación
		$categoria->evento_id = $evento_id;
		$categoria->save();


		// $categoria_traduc = []; 
		$cant_idioms = count($event_idiomas);


		for($i=0; $i < $cant_idioms; $i++){

			$categoria_trad 				= new Categoria_traduc;
			$categoria_trad->nombre			= '';
			$categoria_trad->categoria_id	= $categoria->id;
			$categoria_trad->idioma_id		= $event_idiomas[$i]->id;
			$categoria_trad->traducido 		= false;
			$categoria_trad->save();

			//array_push($categoria_traduc, $categoria_trad->toArray()); // No guarda el nombre del idioma en cada traducción.

		}
		
		Evaluacion::crearPrimera($evento_id, $categoria->id, 'Evaluación'); // Aún no sé que nombre le asigne el usuario.

		Categoria_traduc::traducciones_single($categoria); // Paso por referencia la categoria_king

		return $categoria;
	}


	public function putGuardar(Request $request)
	{
		$user = User::fromToken();
		$categorias_traducidas = $request->input('categorias_traducidas');
		
		$categoria_king 			= Categoria_king::find($request->input('id'));
		$categoria_king->nivel_id 	= $request->input('nivel_id');
		$categoria_king->disciplina_id 	= $request->input('disciplina_id');


		if ($categoria_king->nombre != $categorias_traducidas[0]['nombre'] ) {
			$categoria_king->nombre = $categorias_traducidas[0]['nombre'];


			
			// Cambio el nombre a la Evaluación creada automáticamente
			$cat_trad = Categoria_traduc::find($categorias_traducidas[0]['id']);
			if ($cat_trad->nombre == "") {
				$eva = Evaluacion::where('evento_id', $categoria_king->evento_id)
							->where('categoria_id', $categoria_king->id)
							->first();

				if ($eva->descripcion == "Evaluación") {
					$eva->descripcion = "Evaluación de " . $categorias_traducidas[0]['nombre'];
					$eva->save();
				}
			}
		}
		$categoria_king->save();


		foreach ($categorias_traducidas as $key => $categoria_traducido) {

			$categoria_trad = Categoria_traduc::findOrFail($categoria_traducido['id']);

			$categoria_trad->nombre 		= $categoria_traducido['nombre'];
			$categoria_trad->abrev 			= $categoria_traducido['abrev'];
			$categoria_trad->descripcion 	= $categoria_traducido['descripcion'];
			$categoria_trad->traducido 		= $categoria_traducido['traducido'];
			$categoria_trad->save();

		}
	
		return 'Categoría y sus traducciones guardadas.';
	}



	
	public function deleteDestroy($id)
	{
		$categ = Categoria_king::find($id);
		$categ->delete();

		$categ = Evaluacion::where('categoria_id', $id)->delete();

		return $categ;
	}
	
	public function deleteForcedelete($id)
	{
		$evento = Categoria_king::onlyTrashed()->findOrFail($id);
		
		if ($evento) {
			$evento->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	
	}

	public function putRestore($id)
	{
		$evento = Categoria_king::onlyTrashed()->findOrFail($id);

		if ($evento) {
			$evento->restore();
		}else{
			return \App::abort(400, 'Categoria_king no encontrado en la Papelera.');
		}
		return $evento;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_categorias_king
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
