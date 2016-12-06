<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Models\Grupo_pregunta;
use App\Models\Contenido_traduc;
use App\Models\Pregunta_agrupada;
use App\Models\Evento;
use App\Models\User;
use DB;



class Grupo_preguntasController extends Controller {



	public function postStore(Request $request)
	{
		$user = User::fromToken();

		$evento_id = $user->evento_selected_id;
		$evento = Evento::find($evento_id);

		$event_idiomas = Evento::idiomas_all($evento->id);


		$pres_agrup = new Grupo_pregunta;

		$pres_agrup->descripcion = '';
		$pres_agrup->categoria_id = $request->input('categoria_id');
		$pres_agrup->is_cuadricula = false;
		$pres_agrup->added_by = $user->id;

		$pres_agrup->save();


		$cant_idioms = count($event_idiomas);

		for($i=0; $i < $cant_idioms; $i++){

			$conten_trad 					= new Contenido_traduc;
			$conten_trad->definicion		= 'Contenido ' . $pres_agrup->id;
			$conten_trad->grupo_pregs_id	= $pres_agrup->id;
			$conten_trad->idioma_id			= $event_idiomas[$i]->id;
			//$conten_trad->traducido 		= false; // No en DB
			$conten_trad->save();

			$conten_trad->preguntas_agrupadas = []; // Necesario para evitar errores con el length en javascript

		}

		Contenido_traduc::traducciones_single($pres_agrup); // Paso por referencia el grupo preg 
		

		
		
		return $pres_agrup;
	}

	
	public function getTraducidos(Request $request)
	{
		
		$user 		= User::fromToken();
		$grupo_id 	= $request->input('grupo_id');


		$consulta = 'SELECT gp.id as pg_id, gp.descripcion, gp.categoria_id, gp.added_by, gp.created_at as gp_created_at, gp.updated_at as gp_updated_at,
						ct.id as pg_traduc_id, ct.definicion, ct.idioma_id, ct.traducido, ct.updated_at as pgt_updated_at,
						idi.nombre as idioma, idi.original as idioma_original, idi.abrev as idioma_abrev
					FROM ws_grupos_preguntas gp
					INNER JOIN ws_contenido_traduc ct on ct.grupo_pregs_id=gp.id and ct.deleted_at is null
					INNER JOIN ws_idiomas idi on idi.id=ct.idioma_id and idi.deleted_at is null
					WHERE gp.id=:grupo_id AND gp.deleted_at is null';


		$pg_traducidas = DB::select($consulta, [':grupo_id' => $grupo_id] );

		return $pg_traducidas;
	}



	public function deleteDestroy($id)
	{
		$grup = Grupo_pregunta::findOrFail($id);
		$grup->delete();

		return $grup;
	}

}
