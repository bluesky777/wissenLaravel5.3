<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;



use App\Models\Evento;
use App\Models\User;
use App\Models\Examen_respuesta;
use App\Models\Evaluacion;
use App\Models\Pregunta_evaluacion;
use App\Models\Pregunta_king;

use App\Models\Grupo_pregunta;
use App\Models\Pregunta_agrupada;
use App\Models\Opcion_agrupada;

use App\Models\Categoria_king;
use App\Models\Categoria_traduc;
use App\Models\Opcion;
use App\Models\Respuesta;



use Illuminate\Http\Request;

class Examenes_respuestaController extends Controller {


	public function getIndex(Request $request)
	{
		$examenes = Examen_respuesta::all();
		return $examenes;
	}

	
	public function postIniciar(Request $request)
	{
		$user 		= User::fromToken();
		$evento 	= Evento::actual();
		$evento_id 	= $evento->id;

		$evaluacion = Evaluacion::actual($evento_id, $request->categoria_id);

		$examen 					= new Examen_respuesta;
		$examen->inscripcion_id 	= $request->inscripcion_id;
		$examen->evaluacion_id 		= $evaluacion->id;
		$examen->idioma_id 			= $user->idioma_main_id;
		$examen->categoria_id 		= $request->categoria_id;
		$examen->terminado 			= false;
		$examen->gran_final 		= $evento->gran_final;
		$examen->res_by_promedio 	= $evaluacion->puntaje_por_promedio;
		$examen->save();


		$preguntas_king = Pregunta_king::deEvaluacion($evaluacion->id);
		$preguntas = Grupo_pregunta::deEvaluacion($preguntas_king, $evaluacion->id);


		$evaluacion->preguntas = $preguntas;

		$evaluacion->inscripcion_id 	= $request->inscripcion_id;
		$evaluacion->examen_id 			= $examen->id;


		//Datos de la categoría
		$categoria = Categoria_king::findOrFail($request->categoria_id);
		Categoria_traduc::traducciones_single($categoria); // Paso por referencia la categoria_king
		$evaluacion->categoria = $categoria;

		return $evaluacion;
	}


	
	public function putContinuar(Request $request)
	{
		$user = User::fromToken();
		$evento_id = Evento::actual()->id;


		$exa_resp_id = $request->input('exa_resp_id');

		$examen = Examen_respuesta::findOrFail($exa_resp_id);


		$preguntas_king = Pregunta_king::deEvaluacion($examen->evaluacion_id, $exa_resp_id);
		$preguntas = Grupo_pregunta::deEvaluacion($preguntas_king, $examen->evaluacion_id, $exa_resp_id);


		$evaluacion = Evaluacion::findOrFail($examen->evaluacion_id);
		$evaluacion->preguntas = $preguntas;

		$evaluacion->examen_id 			= $examen->id;
		$evaluacion->id 				= $evaluacion->examen_id;
		$evaluacion->evaluacion_id 		= $evaluacion->id;


		//Datos de la categoría
		$categoria = Categoria_king::findOrFail($evaluacion->categoria_id);
		Categoria_traduc::traducciones_single($categoria); // Paso por referencia la categoria_king
		$evaluacion->categoria = $categoria;

		return $evaluacion;
	}

	

	public function putResponderPregunta(Request $request)
	{
		$user = User::fromToken();

		$puntos = 0;
		$examen_actual_id 	= $request->input('examen_actual_id');
		$opcion_id 			= $request->input('opcion_id');
		$preg_king_id 		= $request->input('pregunta_top_id');
		$preg_traduc_id 	= $request->input('pregunta_sub_id');

		$pregunta_king 	= Pregunta_king::findOrFail($preg_king_id);
		$opcion 		= Opcion::find($opcion_id);


		$respondida = Respuesta::where('examen_respuesta_id', $examen_actual_id)
								->where('preg_traduc_id', $preg_traduc_id)
								->first();
		if ($respondida) {
			return 'Ya respondida'; // El texto debe ser exacto.
		}
		

		if ($pregunta_king->tipo_pregunta == 'Test') { // Solo una opción es correcta
			if ($opcion) {
				if ($opcion->is_correct) {
					$puntos = $pregunta_king->puntos;
				}
			}	

		}



		$res = new Respuesta;
		$res->examen_respuesta_id	= $request->input('examen_actual_id');
		$res->pregunta_king_id		= $preg_king_id;
		$res->tiempo				= $request->input('tiempo');
		$res->tiempo_aproximado		= $request->input('tiempo_aproximado');
		$res->preg_traduc_id		= $preg_traduc_id;
		$res->idioma_id				= $request->input('idioma_id');
		$res->tipo_pregunta			= $request->input('tipo_pregunta');
		$res->puntos_maximos 		= $puntos;
		$res->puntos_adquiridos 	= $request->input('puntos_adquiridos');
		$res->opcion_id 			= $opcion_id;
		$res->save();
		

		return 'Respuesta guardada.';
	}




	public function putResponderPreguntaAgrupada(Request $request)
	{
		$user = User::fromToken();

		$puntos = 0;
		$examen_actual_id 	= $request->input('examen_actual_id');
		$opcion_agrupada_id = $request->input('opcion_id');
		$grupo_preg_id 		= $request->input('pregunta_top_id');
		$pregunta_agrupada_id = $request->input('pregunta_sub_id');

		$preg_agrupada 	= Pregunta_agrupada::findOrFail($pregunta_agrupada_id);
		$opcion 		= Opcion_agrupada::findOrFail($opcion_agrupada_id);


		$respondida = Respuesta::where('examen_respuesta_id', $examen_actual_id)
								->where('pregunta_agrupada_id', $pregunta_agrupada_id)
								->first();
		if ($respondida) {

			$respondida->opcion_agrupada_id = $opcion_agrupada_id;

			if ($preg_agrupada->tipo_pregunta == 'Test') { // Solo una opción es correcta

				if ($opcion->is_correct) {
					$respondida->puntos_adquiridos = $preg_agrupada->puntos;
				}else{
					$respondida->puntos_adquiridos = 0;
				}
			}
			$respondida->save();

			return 'Respuesta cambiada'; 
		}
		

		if ($preg_agrupada->tipo_pregunta == 'Test') { // Solo una opción es correcta
			
			if ($opcion->is_correct) {
				$puntos = $preg_agrupada->puntos;
			}

		}



		$res = new Respuesta;
		$res->examen_respuesta_id	= $request->input('examen_actual_id');
		$res->pregunta_agrupada_id	= $pregunta_agrupada_id;
		$res->grupo_preg_id			= $grupo_preg_id;
		$res->tiempo				= $request->input('tiempo');
		$res->tiempo_aproximado		= $request->input('tiempo_aproximado');
		$res->idioma_id				= $request->input('idioma_id');
		$res->tipo_pregunta			= $request->input('tipo_pregunta');
		$res->puntos_maximos 		= $puntos;
		$res->puntos_adquiridos 	= $request->input('puntos_adquiridos');
		$res->opcion_agrupada_id 	= $opcion_agrupada_id;
		$res->save();
		

		return 'Respuesta guardada.';
	}






	public function putSetTerminado(Request $request)
	{
		//DB::enableQueryLog();
		$user 				= User::fromToken();
		$examen 			= Examen_respuesta::findOrFail($request->input('exa_id'));
		$examen->terminado 	= true;
		$examen->save();

		return 'Terminado con éxito';
	
	}	



	public function deleteDestroy($id)
	{
		//DB::enableQueryLog();
		$user = User::fromToken();
		$examen = Examen_respuesta::findOrFail($id);
		//$queries = DB::getQueryLog();
		//$last_query = end($queries);
		//return $last_query;

		$examen->delete();

		return $examen;
	
	}	


}