<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\Pregunta_king;
use App\Models\Pregunta_traduc;

use App\Models\Pregunta_agrupada;
use App\Models\Grupo_pregunta;
use App\Models\Contenido_traduc;

use App\Models\Evento;
use App\Models\User;
use App\Models\Opcion;


use App\Services\PreguntasService;


use Illuminate\Http\Request;

use DB;



class Preguntas_kingController extends Controller {


	public function getIndex(Request $request)
	{	
		$user 			= User::fromToken();
		$evento_id 		= $user->evento_selected_id;
		$categoria_id 	= $request->input('categoria_id');
		$idioma_id 		= $request->input('idioma_id');


		$consulta = 'SELECT * FROM (	
						SELECT pk.id as pg_id, TRUE as is_preg, pk.descripcion, pk.tipo_pregunta, pk.duracion, pk.categoria_id, pk.puntos, pk.aleatorias, pk.added_by, pk.created_at as gp_created_at, pk.updated_at as gp_updated_at, 
							pt.id as pg_traduc_id, pt.enunciado, NULL as definicion, pt.ayuda, pt.idioma_id, pt.texto_arriba, pt.texto_abajo, pt.traducido, pt.updated_at as pgt_updated_at
						FROM ws_preguntas_king pk
						INNER JOIN ws_pregunta_traduc pt on pt.pregunta_id=pk.id and pt.idioma_id=:idioma_id and pt.deleted_at is null
						WHERE pk.categoria_id=:categoria_id AND pk.deleted_at is null
					union
						SELECT gp.id as pg_id, FALSE as is_preg, gp.descripcion, NULL as tipo_pregunta, NULL as duracion, gp.categoria_id, NULL as puntos, NULL as aleatorias, gp.added_by, gp.created_at as gp_created_at, gp.updated_at as gp_updated_at,
							ct.id as pg_traduc_id, NULL as enunciado, ct.definicion, NULL as ayuda, ct.idioma_id, NULL as texto_arriba, NULL as texto_abajo, ct.traducido, ct.updated_at as pgt_updated_at
						FROM ws_grupos_preguntas gp
						INNER JOIN ws_contenido_traduc ct on ct.grupo_pregs_id=gp.id and ct.idioma_id=:idioma_id2 and ct.deleted_at is null
						WHERE gp.categoria_id=:categoria_id2 and gp.deleted_at is null
					)p order by gp_created_at';


		$pg_traducidas = DB::select($consulta, [':idioma_id' => $idioma_id, ':categoria_id' => $categoria_id, ':idioma_id2' => $idioma_id, ':categoria_id2' => $categoria_id] );

			
		$cant_pg = count($pg_traducidas);
		for($i=0; $i < $cant_pg; $i++){

			if ($pg_traducidas[$i]->is_preg) {
				
				$consulta = 'SELECT o.id, o.definicion, o.orden, o.pregunta_traduc_id, o.is_correct, o.added_by, o.created_at, o.updated_at 
						FROM ws_opciones o
						where o.pregunta_traduc_id=:pregunta_traduc_id';

				$opciones = DB::select($consulta, [':pregunta_traduc_id' => $pg_traducidas[$i]->pg_traduc_id] );
				$pg_traducidas[$i]->opciones = $opciones;
			}else{

				$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.duracion, t.tipo_pregunta, t.puntos, t.aleatorias, t.orden, t.added_by, t.created_at, t.updated_at 
						FROM ws_preguntas_agrupadas t
						where t.contenido_id = :contenido_id and t.deleted_at is null';

				$pregs_agrupadas = DB::select($consulta, [':contenido_id' => $pg_traducidas[$i]->pg_traduc_id] );

				$pg_traducidas[$i]->pregs_agrupadas = $pregs_agrupadas;

				$cant_pregs = count($pg_traducidas[$i]->pregs_agrupadas);
				for($k=0; $k < $cant_pregs; $k++){

					$consulta = 'SELECT o.id, o.definicion, o.orden, o.preg_agrupada_id, o.is_correct 
							FROM ws_opciones_agrupadas o
							where o.preg_agrupada_id =:preg_agrupada_id';

					$preg_trads = DB::select($consulta, [':preg_agrupada_id' => $pg_traducidas[$i]->pregs_agrupadas[$k]->id] );

					$pg_traducidas[$i]->pregs_agrupadas[$k]->opciones = $preg_trads;

				}
			}
		}
		return $pg_traducidas;
	}




	public function getTraducidas(Request $request)
	{	
		$user 			= User::fromToken();
		$pregunta_id 	= $request->input('pregunta_id');


		$consulta = 'SELECT pk.id as pg_id, pk.descripcion, pk.tipo_pregunta, pk.duracion, pk.categoria_id, pk.puntos, pk.aleatorias, pk.added_by, pk.created_at as gp_created_at, pk.updated_at as gp_updated_at, 
						pt.id as pg_traduc_id, pt.enunciado, pt.ayuda, pt.idioma_id, pt.texto_arriba, pt.texto_abajo, pt.traducido, pt.updated_at as pgt_updated_at,
						idi.nombre as idioma, idi.original as idioma_original, idi.abrev as idioma_abrev
					FROM ws_preguntas_king pk
					INNER JOIN ws_pregunta_traduc pt on pt.pregunta_id=pk.id and pt.deleted_at is null
					INNER JOIN ws_idiomas idi on idi.id=pt.idioma_id and idi.deleted_at is null
					WHERE pk.id=:pregunta_id AND pk.deleted_at is null';


		$pg_traducidas = DB::select($consulta, [':pregunta_id' => $pregunta_id] );

			
		$cant_pg = count($pg_traducidas);
		for($i=0; $i < $cant_pg; $i++){

			$consulta = 'SELECT o.id, o.definicion, o.orden, o.pregunta_traduc_id, o.is_correct, o.added_by, o.created_at, o.updated_at 
					FROM ws_opciones o
					where o.pregunta_traduc_id=:pregunta_traduc_id';

			$opciones = DB::select($consulta, [':pregunta_traduc_id' => $pg_traducidas[$i]->pg_traduc_id] );
			$pg_traducidas[$i]->opciones = $opciones;
		
		}
		return $pg_traducidas;
	}



	public function postStore(Request $request)
	{
		$user = User::fromToken();

		$evento_id 		= $user->evento_selected_id;
		$categoria_id 	= $request->input('categoria_id');
		$idioma_id 		= $request->input('idioma_id');
		$evento 		= Evento::find($evento_id);


		// Creamos la pregunta
		$pre_king = new Pregunta_king;
		$pre_king->tipo_pregunta 	= 'Test';
		$pre_king->categoria_id 	= $categoria_id;
		$pre_king->aleatorias 		= false;
		$pre_king->puntos 			= 1;
		$pre_king->added_by 		= $user->id;
		$pre_king->save();

		$event_idiomas = Evento::idiomas_all($evento->id);
		$cant_idioms = count($event_idiomas);

		for($i=0; $i < $cant_idioms; $i++){

			$preg_trad 					= new Pregunta_traduc;
			$preg_trad->enunciado		= 'Pregunta ' . $pre_king->id;
			$preg_trad->ayuda			= '';
			$preg_trad->pregunta_id		= $pre_king->id;
			$preg_trad->idioma_id		= $event_idiomas[$i]->id;
			$preg_trad->traducido 		= false;
			$preg_trad->save();


			$opciones_nuevas = [];

			if ($user->idioma_main_id == $preg_trad->idioma_id) {
				
				$cont = 0;

				for ($i=0; $i < 4; $i++) { 
					$opcion = new Opcion;
					$opcion->definicion 		= 'Opción ' . ($cont+1);
					$opcion->orden 				= $cont;
					$opcion->is_correct 		= ($cont == 0 ? true : false);
					$opcion->pregunta_traduc_id = $preg_trad->id;
					$opcion->added_by 			= $user->id;

					$opcion->save();
					$cont++;

					array_push($opciones_nuevas, $opcion);
				}
				
			}

			$preg_trad->opciones 		= []; // Necesario para evitar errores con el length en javascript




		}
		
		$pg_pregunta = $this->unaPGPregunta($idioma_id, $pre_king->id);

		
		return (array)$pg_pregunta;
	}

	public function unaPGPregunta($idioma_id, $pg_id){


		// Traemos la pregunta creada con sus datos traducidos
		$consulta = 'SELECT pk.id as pg_id, TRUE as is_preg, pk.descripcion, pk.tipo_pregunta, pk.duracion, pk.categoria_id, pk.puntos, pk.aleatorias, pk.added_by, pk.created_at as gp_created_at, pk.updated_at as gp_updated_at, 
						pt.id as pg_traduc_id, pt.enunciado, NULL as definicion, pt.ayuda, pt.idioma_id, pt.texto_arriba, pt.texto_abajo, pt.traducido, pt.updated_at as pgt_updated_at
					FROM ws_preguntas_king pk
					INNER JOIN ws_pregunta_traduc pt on pt.pregunta_id=pk.id and pt.idioma_id=:idioma_id and pt.deleted_at is null
					WHERE pk.id=:pg_id';

		$pg_pregunta = DB::select($consulta, [':idioma_id' => $idioma_id, ':pg_id' => $pg_id] );
		$pg_pregunta = $pg_pregunta[0];

		// Le traemos las opciones
		$consulta = 'SELECT o.id, o.definicion, o.orden, o.pregunta_traduc_id, o.is_correct, o.added_by, o.created_at, o.updated_at 
						FROM ws_opciones o
						where o.pregunta_traduc_id=:pregunta_traduc_id';

		$opciones = DB::select($consulta, [':pregunta_traduc_id' => $pg_pregunta->pg_traduc_id] );
		$pg_pregunta->opciones = $opciones;
			
		return $pg_pregunta;
		
	}


	public function putCambiarCategoria(Request $request)
	{
		$user = User::fromToken();

		$pregunta_id = $request->input('pregunta_id');
		$categoria_id = $request->input('categoria_id');

		$pregunta = Pregunta_king::find($pregunta_id);

		$pregunta->categoria_id = $categoria_id;
		$pregunta->save();
		

		return 'Cambiada';
	}


	public function putUpdate(Request $request)
	{
		$user = User::fromToken();

		$preg_king 					= Pregunta_king::findOrFail($request->input('pg_id', $request->input('id')));
		$preg_king->descripcion 	= $request->input('descripcion');
		$preg_king->tipo_pregunta 	= $request->input('tipo_pregunta');
		$preg_king->duracion 		= $request->input('duracion');
		$preg_king->categoria_id 	= $request->input('categoria_id');
		$preg_king->puntos 			= $request->input('puntos', 1);
		$preg_king->aleatorias 		= $request->input('aleatorias');
		
		$preg_king->save();

		if ($request->input('preguntas_traducidas')) {
			foreach ($request->input('preguntas_traducidas') as $key => $preg_trad) {

				$preg_trad_temp 					= Pregunta_traduc::findOrFail($preg_trad['pg_traduc_id']);
				$preg_trad_temp->enunciado 			= $preg_trad['enunciado'];
				$preg_trad_temp->ayuda 				= $preg_trad['ayuda'];
				$preg_trad_temp->save();

				foreach ($preg_trad['opciones'] as $keytr => $opcion) {

					if (array_key_exists('nueva', $opcion)) {

						if ($opcion['nueva'] == true) {
							// No es una opción a agregar, es el botón.
						}else{
							$opcion_t 				= Opcion::findOrFail($opcion['id']);
							$opcion_t->definicion 	= $opcion['definicion'];
							$opcion_t->is_correct 	= $opcion['is_correct'];
							$opcion_t->save();
						}
						
					}else{
						$opcion_t 				= Opcion::findOrFail($opcion['id']);
						$opcion_t->definicion 	= $opcion['definicion'];
						$opcion_t->is_correct 	= $opcion['is_correct'];
						$opcion_t->save();
					}

				
				}

			}

		}else{
			$preg_trad_temp 					= Pregunta_traduc::findOrFail($request->input('pg_traduc_id'));
			$preg_trad_temp->enunciado 			= $request->input('enunciado');
			$preg_trad_temp->ayuda 				= $request->input('ayuda');
			$preg_trad_temp->save();

			foreach ($request->input('opciones') as $keytr => $opcion) {

				if (array_key_exists('nueva', $opcion)) {

					if ($opcion['nueva'] == true) {
						// No es una opción a agregar, es el botón.
					}else{
						$opcion_t 				= Opcion::findOrFail($opcion['id']);
						$opcion_t->definicion 	= $opcion['definicion'];
						$opcion_t->is_correct 	= $opcion['is_correct'];
						$opcion_t->save();
					}
					
				}else{
					$opcion_t 				= Opcion::findOrFail($opcion['id']);
					$opcion_t->definicion 	= $opcion['definicion'];
					$opcion_t->is_correct 	= $opcion['is_correct'];
					$opcion_t->save();
				}

			
			}
		}

		$pg_pregunta = $this->unaPGPregunta($request->input('idioma_id'), $preg_king->id);
		return (array)$pg_pregunta;
		//return $preg_king;
	}

	public function getConImagenes()
	{
		$user = User::fromToken();
		
		

		$consulta = "SELECT * FROM ws_pregunta_traduc WHERE enunciado LIKE ('%<img %') AND deleted_at is null;";

		$preguntas = DB::select($consulta);


		return $preguntas;
	}

	public function deleteDestroy($id)
	{
		$user = User::fromToken();
		
		$preg_king = Pregunta_king::findOrFail($id);
		$preg_king->delete();

		return $preg_king;
	}

	public function putDestroyVarias(Request $request)
	{
		$user = User::fromToken();
		
		$pregs_solicitadas 	= $request->input('preguntas');
		$cant_eliminadas 	= 0;

		for ($i=0; $i < count($pregs_solicitadas); $i++) { 
			if ($pregs_solicitadas[$i]['is_preg']) {
				$preg_king = Pregunta_king::findOrFail($pregs_solicitadas[$i]['pg_id']);
				$preg_king->delete();
				$cant_eliminadas++;
			}
			
		}
		

		return $cant_eliminadas;
	}

}
