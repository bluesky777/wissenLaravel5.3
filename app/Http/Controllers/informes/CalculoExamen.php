<?php namespace App\Http\Controllers\informes;

use App\Models\Examen_respuesta;
use App\Models\Inscripcion;
use App\Models\Evaluacion;
use App\Models\Pregunta_evaluacion;
use App\Models\Respuesta;

use App\Models\Pregunta_king;
use App\Models\Pregunta_traduc;
use App\Models\Opcion;
use App\Models\Grupo_pregunta;
use App\Models\Contenido_traduc;
use App\Models\Pregunta_agrupada;
use App\Models\Opcion_agrupada;

use App\Models\Categoria_king;
use App\Models\User;
use App\Models\Pid;


use DB;
use \stdClass;


class CalculoExamen {

	public static function calcular($examen)
	{

		$respuestas		= Respuesta::where('examen_respuesta_id', $examen->examen_id)->get();

		$cantidad_pregs =  CalculoExamen::cantidadPreguntas($examen->evaluacion_id);


		$cant_res 		= count($respuestas);
		$correctas 		= 0;
		$incorrectas 	= 0;
		$puntos 		= 0;
		$tiempo 		= DB::table('ws_respuestas')->where('examen_respuesta_id', $examen->examen_id)->sum('tiempo');

		for($i=0; $i < $cant_res; $i++){

			if ($respuestas[$i]->opcion_id) {
				
				//$opcion = Opcion::find($respuestas[$i]->opcion_id);

				$consulta 	= 'SELECT * FROM ws_opciones where id=?';
				$opcion 	= DB::select($consulta, [ $respuestas[$i]->opcion_id ] );

				if (count($opcion) > 0) { $opcion = $opcion[0]; }


				if ($opcion->is_correct) {
					$correctas++;

					//$preg_king	= Pregunta_king::find($respuestas[$i]->pregunta_king_id);
					$consulta 	= 'SELECT * FROM ws_preguntas_king where deleted_at is null and id=?';
					$preg_king 	= DB::select($consulta, [ $respuestas[$i]->pregunta_king_id ] );

					if (count($preg_king) > 0) { $preg_king = $preg_king[0]; }

					$puntos_preg = $preg_king->puntos;

					$puntos = $puntos + $puntos_preg;
				}else{
					$incorrectas++;
				}

			}elseif($respuestas[$i]->opcion_agrupada_id){

				$opcion = Opcion_agrupada::find($respuestas[$i]->opcion_agrupada_id);
				if ($opcion->is_correct) {
					$correctas++;

					$preg_agrup	= Pregunta_agrupada::find($respuestas[$i]->pregunta_agrupada_id);

					$puntos = $puntos + $preg_agrup->puntos;
				}

			}

		}

		// Calculamos por promedio
		if ($cantidad_pregs > 0) {
			$promedio = $correctas * 100 / $cantidad_pregs;
		}else{
			$promedio = 0;
		}
		

		$res 					= new stdClass();
		$res->promedio 			= $promedio;
		$res->cantidad_pregs 	= $cantidad_pregs;
		$res->correctas 		= $correctas;
		$res->incorrec_reales 	= $incorrectas;
		$res->tiempo 			= (integer)$tiempo;

		return $res;
	}



	public static function cantidadPreguntas($evaluacion_id)
	{
		#Pid::nuevo( 'CALCULANDO PREGUNTAS EXAMEN, evaluacion_id: ' . $evaluacion_id);
		$preguntas = Pregunta_evaluacion::where('evaluacion_id', $evaluacion_id)->get();
		$cant = 0;

		foreach ($preguntas as $key => $pregunta) {
			if ($pregunta->pregunta_id) {
				$cant = $cant + 1;
			}else{
				$cant_preg_grupo = CalculoExamen::cantPreguntasEnGrupo($pregunta->grupo_pregs_id);
				$cant = $cant + $cant_preg_grupo;
			}		

		}

		return $cant;

	}



	public static function cantPreguntasEnGrupo($grupo_pregs_id, $idioma_id=false)
	{
		$contenido_trad = [];

		if ($idioma_id) {
			$contenido_trad = Contenido_traduc::where('grupo_pregs_id', $grupo_pregs_id)
												->where('idioma_id', $idioma_id)->first();
		}else{
			$contenido_trad = Contenido_traduc::where('grupo_pregs_id', $grupo_pregs_id)->first();
		}

		$conten_trad_id = $contenido_trad['id'];

		$preguntas_agrup = Pregunta_agrupada::where('contenido_id', $conten_trad_id)->get();

		$cant = count($preguntas_agrup);
			
		return $cant;


	}




}


