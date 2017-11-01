<?php namespace App\Http\Controllers\informes;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

use App\Models\Examen_respuesta;
use App\Models\Inscripcion;
use App\Models\Evaluacion;
use App\Models\Pregunta_evaluacion;
use App\Models\Respuesta;
use App\Models\Evento;

use App\Models\Pregunta_king;
use App\Models\Pregunta_traduc;
use App\Models\Opcion;
use App\Models\Grupo_pregunta;
use App\Models\Contenido_traduc;
use App\Models\Pregunta_agrupada;
use App\Models\Opcion_agrupada;

use App\Models\Categoria_king;
use App\Models\Categoria_traduc;
use App\Models\User;
use App\Models\Pid;


use DB;


class DatosController extends Controller {



	public function putDatos()
	{
		$user 	= User::fromToken();
		$datos 	= [];

		$events = Evento::todos();

		$cantEv = count($events);

		for ($i=0; $i < $cantEv; $i++) { 
			$evento_id 				= $events[$i]->id;
			$events[$i]->idiomas 	= Evento::idiomas_all($evento_id);

			$consulta 	= 'SELECT * FROM ws_categorias_king c where c.evento_id=? and c.deleted_at is null';
			$categorias = DB::select($consulta, [$evento_id] );
			
			Categoria_traduc::traducciones($categorias); // Paso por referencia la categoria_king
			
			$cantCat = count($categorias);
			for ($h=0; $h < $cantCat; $h++) { 
				
				$consulta 		= 'SELECT * FROM ws_evaluaciones e where e.categoria_id=? and e.evento_id=? and e.deleted_at is null';
				$evaluaciones 	= DB::select($consulta, [$categorias[$h]->id, $evento_id] );

				$cant = count($evaluaciones);

				// Traemos las preguntas de cada evaluación
				/*
				for($j = 0; $j < $cant; $j++){

					$evaluacion = $evaluaciones[$j];

					$pregs_eval = Pregunta_evaluacion::preguntas($evaluacion->id);
					$cant_preg 	= count($pregs_eval);
					
					for($k=0; $k < $cant_preg; $k++){

						$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.pregunta_id, 
											t.idioma_id, t.traducido, i.nombre as idioma  
									FROM ws_pregunta_traduc t, ws_idiomas i
									where i.id=t.idioma_id and t.pregunta_id =:pregunta_id and t.deleted_at is null';

						$preg_trads = DB::select($consulta, [':pregunta_id' => $pregs_eval[$k]->pregunta_id] );
						$pregs_eval[$k]->preguntas_traducidas = $preg_trads;

					}
					$evaluacion->preguntas_evaluacion = $pregs_eval;
				}
				*/
				$categorias[$h]->evaluaciones = $evaluaciones;
			}
			$events[$i]->categorias 	= $categorias;



			// Ahora las entidades
			$consulta 				= 'SELECT * FROM ws_entidades e where e.evento_id=? and e.deleted_at is null';
			$entidades 				= DB::select($consulta, [$evento_id] );
			$events[$i]->entidades 	= $entidades;

		}



		$datos['eventos'] = $events;

		return $datos;
	}



}


