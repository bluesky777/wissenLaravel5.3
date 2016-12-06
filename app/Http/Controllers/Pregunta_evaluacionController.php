<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;



use App\Models\User;
use App\Models\Evento;
use App\Models\Pregunta_evaluacion;
use App\Models\Pregunta_traduc;
use App\Models\Contenido_traduc;
use App\Models\Examen_respuesta;
use App\Http\Controllers\informes\CalculoExamen;
use DB;



class Pregunta_evaluacionController extends Controller {


	public function putAsignarPregunta(Request $request)
	{
		$user 			= User::fromToken();
		$evento_id 		= Evento::actual()->id;

		$evaluacion_id 	= $request->input('evaluacion_id');
		$pregunta_id 	= $request->input('pregunta_id');



		$preg_eval = Pregunta_evaluacion::where('pregunta_id', $pregunta_id)
										->where('evaluacion_id', $evaluacion_id)
										->first();

		if ($preg_eval) {
			return $preg_eval;
		}else{

			$cant = Pregunta_evaluacion::where('evaluacion_id', $evaluacion_id)
										->count();
			
			$preg_eval 					= new Pregunta_evaluacion;
			$preg_eval->pregunta_id 	= $pregunta_id;
			$preg_eval->evaluacion_id 	= $evaluacion_id;
			$preg_eval->added_by 		= $user->id;
			$preg_eval->orden 			= $cant+1;
			$preg_eval->save();

			return $preg_eval;

		}

		return;
	}


	public function putQuitarPregunta(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$evaluacion_id = $request->input('evaluacion_id');
		$pregunta_id = $request->input('pregunta_id');
		$pregunta_eval_id = $request->input('pregunta_eval_id');



		$preg_eval = Pregunta_evaluacion::find($pregunta_eval_id);

		$preg_eval->delete();

		return 'Quitada con éxito.';
	}


	public function putAsignarGrupo(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$evaluacion_id = $request->input('evaluacion_id');
		$grupo_pregs_id = $request->input('grupo_pregs_id');


		$preg_eval = Pregunta_evaluacion::where('grupo_pregs_id', $grupo_pregs_id)
										->where('evaluacion_id', $evaluacion_id)
										->first();

		if ($preg_eval) {
			return $preg_eval;
		}else{
			

			$cant = Pregunta_evaluacion::where('evaluacion_id', $evaluacion_id)
										->count();

			$preg_eval 					= new Pregunta_evaluacion;
			$preg_eval->grupo_pregs_id 	= $grupo_pregs_id;
			$preg_eval->evaluacion_id 	= $evaluacion_id;
			$preg_eval->orden 			= $cant+1;
			$preg_eval->added_by 		= $user->id;
			$preg_eval->save();

			return $preg_eval;

		}

		return;
	}



	

	public function getSoloSinAsignar(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$categoria_id = $request->input('categoria_id');

		$consulta = 'SELECT p.* FROM ws_preguntas_king p 
					left join ws_pregunta_evaluacion pe on pe.pregunta_id=p.id
					where p.categoria_id=:categoria_id and p.deleted_at is null and pe.pregunta_id is null;';

		$preguntas_king = \DB::select(\DB::raw($consulta), array(':categoria_id' => $categoria_id) );


		Pregunta_traduc::traducciones($preguntas_king); // Paso por referencia la nivel_king

		//$preguntas_king = $preguntas_king->toArray();


		$consulta = 'SELECT g.* FROM ws_grupos_preguntas g 
					left join ws_pregunta_evaluacion pe on pe.grupo_pregs_id=g.id
					where pe.grupo_pregs_id is null 
						and g.categoria_id=:categoria_id and g.deleted_at is null';

		$grupos_preg = DB::select($consulta, array(':categoria_id' => $categoria_id) );

		Contenido_traduc::traducciones_and_push($grupos_preg, $preguntas_king);

		return $preguntas_king;
	}

	

	public function putAsignarAleatoriamente(Request $request)
	{
		$user 			= User::fromToken();
		$evento_id 		= $user->evento_selected_id;
		$idioma_id 		= $user->idioma_main_id;
		$categoria_id 	= $request->input('categoria_id');

		$evaluacion_id 	= $request->input('evaluacion_id');
		$cantPreg 		= $request->input('cantPregRandom');
		$pregNoAsignadas 	= $request->input('pregNoAsignadas');


		Pregunta_evaluacion::where('evaluacion_id', $evaluacion_id)->delete();


		if ($pregNoAsignadas) {
			
			

			$consulta = 'SELECT * FROM (	
							SELECT pk.id as pg_id, TRUE as is_preg, pk.descripcion, pk.tipo_pregunta, pk.categoria_id, pk.aleatorias, pk.added_by, pk.created_at as gp_created_at, pk.updated_at as gp_updated_at, 
								pt.id as pg_traduc_id, pt.enunciado, NULL as definicion, pt.ayuda, pt.idioma_id, pt.texto_arriba, pt.texto_abajo, pt.traducido, pt.updated_at as pgt_updated_at
							FROM ws_preguntas_king pk
							INNER JOIN ws_pregunta_traduc pt on pt.pregunta_id=pk.id and pt.idioma_id=:idioma_id and pt.deleted_at is null
							WHERE pk.categoria_id=:categoria_id AND pk.deleted_at is null
						union
							SELECT gp.id as pg_id, FALSE as is_preg, gp.descripcion, NULL as tipo_pregunta, gp.categoria_id, NULL as aleatorias, gp.added_by, gp.created_at as gp_created_at, gp.updated_at as gp_updated_at,
								ct.id as pg_traduc_id, NULL as enunciado, ct.definicion, NULL as ayuda, ct.idioma_id, NULL as texto_arriba, NULL as texto_abajo, ct.traducido, ct.updated_at as pgt_updated_at
							FROM ws_grupos_preguntas gp
							INNER JOIN ws_contenido_traduc ct on ct.grupo_pregs_id=gp.id and ct.idioma_id=:idioma_id2 and ct.deleted_at is null
							WHERE gp.categoria_id=:categoria_id2 and gp.deleted_at is null
						)p
						WHERE p.pg_id NOT IN (SELECT pregunta_id FROM ws_pregunta_evaluacion where evaluacion_id=:evaluacion_id AND pregunta_id IS NOT NULL) 
							AND p.pg_id NOT IN (SELECT grupo_pregs_id FROM ws_pregunta_evaluacion where evaluacion_id=:evaluacion_id2 and grupo_pregs_id IS NOT NULL)
                        order by gp_created_at';


			$pg_traducidas = DB::select($consulta, [':idioma_id' => $idioma_id, ':categoria_id' => $categoria_id, 
													':idioma_id2' => $idioma_id, ':categoria_id2' => $categoria_id,
													':evaluacion_id' => $evaluacion_id, ':evaluacion_id2' => $evaluacion_id] );

			
		}else{

			// Todas las preguntas aunque ya estén asignadas a otras evaluaciones

			$consulta = 'SELECT * FROM (	
							SELECT pk.id as pg_id, TRUE as is_preg, pk.descripcion, pk.tipo_pregunta, pk.categoria_id, pk.aleatorias, pk.added_by, pk.created_at as gp_created_at, pk.updated_at as gp_updated_at, 
								pt.id as pg_traduc_id, pt.enunciado, NULL as definicion, pt.ayuda, pt.idioma_id, pt.texto_arriba, pt.texto_abajo, pt.traducido, pt.updated_at as pgt_updated_at
							FROM ws_preguntas_king pk
							INNER JOIN ws_pregunta_traduc pt on pt.pregunta_id=pk.id and pt.idioma_id=:idioma_id and pt.deleted_at is null
							WHERE pk.categoria_id=:categoria_id AND pk.deleted_at is null
						union
							SELECT gp.id as pg_id, FALSE as is_preg, gp.descripcion, NULL as tipo_pregunta, gp.categoria_id, NULL as aleatorias, gp.added_by, gp.created_at as gp_created_at, gp.updated_at as gp_updated_at,
								ct.id as pg_traduc_id, NULL as enunciado, ct.definicion, NULL as ayuda, ct.idioma_id, NULL as texto_arriba, NULL as texto_abajo, ct.traducido, ct.updated_at as pgt_updated_at
							FROM ws_grupos_preguntas gp
							INNER JOIN ws_contenido_traduc ct on ct.grupo_pregs_id=gp.id and ct.idioma_id=:idioma_id2 and ct.deleted_at is null
							WHERE gp.categoria_id=:categoria_id2 and gp.deleted_at is null
						)p order by gp_created_at';


			$pg_traducidas = DB::select($consulta, [':idioma_id' => $idioma_id, ':categoria_id' => $categoria_id, ':idioma_id2' => $idioma_id, ':categoria_id2' => $categoria_id] );
			
		}

		$aleatorias = array_rand($pg_traducidas,$cantPreg);
		$asignadas = [];

		for ($i=0; $i < count($aleatorias); $i++) { 
			$toAsign = $pg_traducidas[$aleatorias[$i]];

			$cant = Pregunta_evaluacion::where('evaluacion_id', $evaluacion_id)
										->count();
			
			$preg_eval 					= new Pregunta_evaluacion;
			$preg_eval->evaluacion_id 	= $evaluacion_id;
			$preg_eval->added_by 		= $user->id;
			$preg_eval->orden 			= $cant+1;
			
			if ($toAsign->is_preg) {
				$preg_eval->pregunta_id 	= $toAsign->pg_id;
			}else{
				$preg_eval->grupo_pregs_id 	= $toAsign->pg_id;
			}
			$preg_eval->save();
			

		}
		return $asignadas;

	}


	public function getExamenesDeEvaluacion(Request $request)
	{
		$user 			= User::fromToken();
		$evaluacion_id 	= $request->input('evaluacion_id');

		$examenes 		= Examen_respuesta::where('evaluacion_id', $evaluacion_id)->get();

		$examenes_puntajes	= [];

		$cant_exams 	= count($examenes);
		for($i=0; $i < $cant_exams; $i++){
			$examenes[$i]->examen_id 			= $examenes[$i]->id;
			$examen 					= CalculoExamen::calcular($examenes[$i]);
			$examenes[$i]->puntajes 	= $examen;

			$consulta = 'SELECT u.*, i.* FROM users u inner join ws_inscripciones i on i.user_id=u.id
					where i.id=:inscripcion_id;';

			$usuario = DB::select($consulta, [':inscripcion_id' => $examenes[$i]->inscripcion_id ]);

			if (count($usuario) > 0) {
				$examenes[$i]->usuario 	= $usuario[0];
			}
			

			array_push($examenes_puntajes, $examenes[$i]);
		}


		return $examenes_puntajes;
	}



}



