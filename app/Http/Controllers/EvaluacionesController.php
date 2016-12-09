<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;



use App\Models\User;
use App\Models\Evento;
use App\Models\Evaluacion;
use App\Models\Pregunta_evaluacion;
use App\Models\Pregunta_traduc;
use App\Models\Categoria_king;
use App\Models\Categoria_traduc;
use App\Models\Opcion;
use DB;



class EvaluacionesController extends Controller {


	public function getIndex(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$categoria_id = $request->input('categoria_id');
		
		$evaluaciones = [];

		if ($categoria_id) {
			$evaluaciones = Evaluacion::where('categoria_id', $categoria_id)
						->where('evento_id', '=', $evento_id)
						->get();
		}else{
			$evaluaciones = Evaluacion::orderBy('categoria_id')
						->where('evento_id', '=', $evento_id)
						->get();
		}


		$cant = count($evaluaciones);

		for($i = 0; $i < $cant; $i++){

			$evaluacion = $evaluaciones[$i];

			$pregs_eval = Pregunta_evaluacion::where('evaluacion_id', $evaluacion->id)->get();
			
			$evaluacion->preguntas_evaluacion = $pregs_eval;

		}

		

		return $evaluaciones;
	}


	public function getCategoriasConPreguntas(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;


		$categorias = Categoria_king::where('evento_id', $evento_id)->get();
		Categoria_traduc::traducciones($categorias); // Paso por referencia la categoria_king
		
		$cantCat = count($categorias);
		for ($h=0; $h < $cantCat; $h++) { 
			
			$evaluaciones = Evaluacion::where('categoria_id', $categorias[$h]->id)
						->where('evento_id', $evento_id)
						->get();

			$cant = count($evaluaciones);

			for($i = 0; $i < $cant; $i++){

				$evaluacion = $evaluaciones[$i];

				$pregs_eval = Pregunta_evaluacion::preguntas($evaluacion->id);
				$cant_preg = count($pregs_eval);
				
				for($k=0; $k < $cant_preg; $k++){

					$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.pregunta_id, 
										t.idioma_id, t.traducido, i.nombre as idioma  
								FROM ws_pregunta_traduc t, ws_idiomas i
								where i.id=t.idioma_id and t.pregunta_id =:pregunta_id and t.deleted_at is null';

					$preg_trads = DB::select($consulta, [':pregunta_id' => $pregs_eval[$k]->pregunta_id] );

					// Traeremos las opciones de cada traducción.
					Opcion::opciones($preg_trads, $evaluacion->id);

					$pregs_eval[$k]->preguntas_traducidas = $preg_trads;

				}
				
				$evaluacion->preguntas_evaluacion = $pregs_eval;


			}

			$categorias[$h]->evaluaciones = $evaluaciones;

		}

		return $categorias;
		
	}


	public function postStore(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;


		$evaluacion 				= new Evaluacion;
		$evaluacion->categoria_id 	= $request->input('categoria_id');
		$evaluacion->evento_id 		= $evento_id;
		$evaluacion->descripcion 	= $request->input('descripcion');
		$evaluacion->duracion_preg 	= $request->input('duracion_preg', 0);
		$evaluacion->duracion_exam 	= $request->input('duracion_exam', 0);
		$evaluacion->one_by_one 	= $request->input('one_by_one', true);
		$evaluacion->created_by 	= $user->id;
		$evaluacion->actual 		= $request->input('actual', false);


		if ($evaluacion->actual) {
			Evaluacion::setElseNotActual($evaluacion);
		}


		$evaluacion->save();

		return $evaluacion;
	}

	

	public function putUpdate(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$eval_id = $request->input('id');

		$evaluacion 				= Evaluacion::findOrFail($eval_id);
		$evaluacion->categoria_id 	= $request->input('categoria_id');
		$evaluacion->evento_id 		= $evento_id;
		$evaluacion->descripcion 	= $request->input('descripcion');
		$evaluacion->duracion_preg 	= $request->input('duracion_preg', 0);
		$evaluacion->duracion_exam 	= $request->input('duracion_exam', 0);
		$evaluacion->one_by_one 	= $request->input('one_by_one', true);
		$evaluacion->created_by 	= $user->id;

		$evaluacion->actual 		= $request->input('actual', false);


		if ($evaluacion->actual) {
			Evaluacion::setElseNotActual($evaluacion);
		}

		$evaluacion->save();

		return $evaluacion;
	}


	public function putSetActual(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;

		$eval_id = $request->input('id');

		$evaluacion = Evaluacion::findOrFail($eval_id);
		$evaluacion->actual = $request->input('actual', false);

		if ($evaluacion->actual) {
			Evaluacion::setElseNotActual($evaluacion);
		}

		$evaluacion->save();

		return $evaluacion;
	}



	public function deleteDestroy($id)
	{
		$evaluacion = Evaluacion::find($id);
		$evaluacion->delete();

		return $evaluacion;
	}

}
