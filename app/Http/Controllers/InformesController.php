<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

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

use App\Http\Controllers\informes\CalculoExamen;


use DB;


class InformesController extends Controller {



	public function getMisExamenes()
	{
		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		
		$consulta = 'SELECT i.*, ct.categoria_id, ct.nombre, ct.abrev, ct.descripcion, ct.idioma_id, ct.traducido 
				FROM ws_inscripciones i 
				inner join ws_categorias_king c on c.id=i.categoria_id and c.deleted_at is null 
				inner join ws_categorias_traduc ct on ct.categoria_id=c.id and ct.idioma_id=:idioma_id and ct.deleted_at is null 
				where i.user_id=:user_id and i.deleted_at is null';

		$inscripciones = DB::select($consulta, array(':idioma_id' => $user->idioma_main_id, 'user_id' => $user->id));

		$examenes_all = [];

		foreach ($inscripciones as $key => $inscripcion) {
			
			$examenes = Examen_respuesta::where('inscripcion_id', $inscripcion->id)->get();
			
			$cant_exams = count($examenes);
			//$examenes_all = array_merge($examenes_all, (array)$examenes);
			
			for($i=0; $i < $cant_exams; $i++){
				array_push($examenes_all, $examenes[$i]);
			}

		}

		$examenes_puntajes = [];
		$cant_exams = count($examenes_all);

		for($i=0; $i < $cant_exams; $i++){

			$examen = $this->calcularExamen($examenes_all[$i]->id);
			array_push($examenes_puntajes, $examen);

		}


		return $examenes_puntajes;
	}


	public function getTodosLosExamenes(){
		$user 			= User::fromToken();
		$evento_id 		= $user->evento_selected_id;
		$idioma_id 		= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final 	= Request::input('gran_final', false);

		$consulta = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
						e.terminado, e.timeout, e.res_by_promedio, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
						e.res_correctas, e.res_incorrectas, e.res_promedio, e.res_puntos, e.res_cant_pregs, e.res_tiempo, e.res_tiempo_format,
					    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
					    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
					    en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
					    en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre,
					    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
					FROM ws_examen_respuesta e
					inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
					inner join users u on u.id=i.user_id and u.deleted_at is null
					inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null
					inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
					inner join ws_entidades en on en.id=u.entidad_id and en.deleted_at is null 
					left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
					left join images im on im.id=u.imagen_id and im.deleted_at is null 
					left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
					where e.deleted_at is null and e.gran_final='.$gran_final;

		$examenes = DB::select($consulta, [':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id] );


		return $examenes;

	}


	public function getExamenesEntidades(){
		$user 		= User::fromToken();
		$evento_id 	= $user->evento_selected_id;
		$idioma_id 	= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final = Request::input('gran_final', false);

		
		$consulta = 'SELECT distinct en.id as entidad_id, en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
						en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre
					FROM  ws_entidades en 
					inner join users u on en.id=u.entidad_id and en.deleted_at is null and u.deleted_at is null  
					inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
					inner join ws_inscripciones i on u.id=i.user_id and i.deleted_at is null 
					inner join ws_examen_respuesta e on e.inscripcion_id=i.id and e.deleted_at is null 
					left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
					where e.deleted_at is null and e.gran_final='.$gran_final;

		$entidades = DB::select($consulta, [':evento_id' => $evento_id] );

		$cant_ent = count($entidades);
		for ($j=0; $j < $cant_ent; $j++) {
			
			$entidad_id = $entidades[$j]->entidad_id;

			$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
							e.terminado, e.timeout, e.res_by_promedio, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
						    e.res_correctas, e.res_incorrectas, e.res_promedio, e.res_puntos, e.res_cant_pregs, e.res_tiempo, e.res_tiempo_format,
						    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
						    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
						    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
						FROM ws_examen_respuesta e
						inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
						inner join users u on u.id=i.user_id and u.deleted_at is null
						inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null
						inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
						left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
						left join images im on im.id=u.imagen_id and im.deleted_at is null 
						where e.deleted_at is null and u.entidad_id=:entidad_id and e.gran_final='.$gran_final;

			$examenes = DB::select($consulta_ex, [':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id, ':entidad_id' => $entidad_id] );

			$entidades[$j]->examenes = $examenes;
		}

		


		return $entidades;

	}




	public function getExamenesEntidadesCategorias(){
		$user 		= User::fromToken();
		$evento_id 	= $user->evento_selected_id;
		$idioma_id 	= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final = Request::input('gran_final', false);

		
		$consulta = 'SELECT distinct en.id as entidad_id, en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
						en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre
					FROM  ws_entidades en 
					inner join users u on en.id=u.entidad_id and en.deleted_at is null and u.deleted_at is null  
					inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
					inner join ws_inscripciones i on u.id=i.user_id and i.deleted_at is null 
					inner join ws_examen_respuesta e on e.inscripcion_id=i.id and e.deleted_at is null 
					left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
					where e.deleted_at is null and e.gran_final='.$gran_final;

		$entidades = DB::select($consulta, [':evento_id' => $evento_id] );

		$cant_ent = count($entidades);
		for ($j=0; $j < $cant_ent; $j++) {
			
			$entidad_id = $entidades[$j]->entidad_id;

			$consulta_categ = 'SELECT distinct ck.id as categoria_id, ct.id as categ_traduc_id, ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, 
								ct.idioma_id, ct.traducido
							FROM ws_categorias_king ck
							inner join ws_inscripciones i on i.categoria_id=ck.id and i.deleted_at is null
							INNER JOIN ws_examen_respuesta e ON i.id=e.inscripcion_id AND e.deleted_at is null
							inner join users u on u.id=i.user_id and u.deleted_at is null and u.entidad_id=:entidad_id
							inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
							left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
							where ck.deleted_at is null and ck.evento_id=:evento_id2 and e.gran_final='.$gran_final;

			$categorias = DB::select($consulta_categ, [':entidad_id' => $entidad_id, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id, ':evento_id2' => $evento_id] );


			$cant_cat = count($categorias);
			for ($k=0; $k < $cant_cat; $k++) { 
				
				$categoria_id = $categorias[$k]->categoria_id;

				$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
								e.terminado, e.timeout, e.res_by_promedio, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
							    e.res_correctas, e.res_incorrectas, e.res_promedio, e.res_puntos, e.res_cant_pregs, e.res_tiempo, e.res_tiempo_format,
							    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
							    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
							    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
							FROM ws_examen_respuesta e
							inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
							inner join users u on u.id=i.user_id and u.deleted_at is null
							inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null and ck.id=:categoria_id
							inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
							left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
							left join images im on im.id=u.imagen_id and im.deleted_at is null 
							where e.deleted_at is null and u.entidad_id=:entidad_id and e.gran_final='.$gran_final;

				$examenes = DB::select($consulta_ex, [':female'=>User::$default_female, ':male'=>User::$default_male, ':categoria_id'=>$categoria_id, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id, ':entidad_id' => $entidad_id] );

				$categorias[$k]->examenes = $examenes;

			}

			$entidades[$j]->categorias = $categorias;
		}

		


		return $entidades;

	}




	public function getExamenesCategorias(){
		$user 		= User::fromToken();
		$evento_id 	= $user->evento_selected_id;
		$idioma_id 	= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final = Request::input('gran_final', false);

		
		$consulta = 'SELECT distinct ck.id as categoria_id, ct.id as categ_traduc_id, ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, 
						ct.idioma_id, ct.traducido
					FROM ws_categorias_king ck
					inner join ws_inscripciones i on i.categoria_id=ck.id and i.deleted_at is null
					INNER JOIN ws_examen_respuesta e ON i.id=e.inscripcion_id AND e.deleted_at is null
					inner join users u on u.id=i.user_id and u.deleted_at is null 
					inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
					left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
					where ck.deleted_at is null and ck.evento_id=:evento_id2 and e.gran_final='.$gran_final;

		$categorias = DB::select($consulta, [':evento_id' => $evento_id, ':idioma_id' => $idioma_id, ':evento_id2' => $evento_id] );

		$cant_cat = count($categorias);
		for ($j=0; $j < $cant_cat; $j++) {
			
			$categoria_id = $categorias[$j]->categoria_id;

				
			$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
							e.terminado, e.timeout, e.res_by_promedio, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
						    e.res_correctas, e.res_incorrectas, e.res_promedio, e.res_puntos, e.res_cant_pregs, e.res_tiempo, e.res_tiempo_format,
						    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
						    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
						    en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
					    	en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre,
						    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
						FROM ws_examen_respuesta e
						inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
						inner join users u on u.id=i.user_id and u.deleted_at is null
						inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null and ck.id=:categoria_id
						inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
						inner join ws_entidades en on en.id=u.entidad_id and en.deleted_at is null 
						left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
						left join images im on im.id=u.imagen_id and im.deleted_at is null 
						left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
						where e.deleted_at is null  and e.gran_final='.$gran_final;

			$examenes = DB::select($consulta_ex, [':female'=>User::$default_female, ':male'=>User::$default_male, ':categoria_id'=>$categoria_id, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id ] );

			$categorias[$j]->examenes = $examenes;

		}

		
		return $categorias;

	}



	// Exámenes de UNA sola categoría
	public function getExamenesCategoria(){
		$user 		= User::fromToken();
		$evento_id 	= $user->evento_selected_id;
		$idioma_id 	= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final = Request::input('gran_final', false);
		$cat_id 	= Request::input('categoria_id');

		
		$consulta = 'SELECT distinct ck.id as categoria_id, ct.id as categ_traduc_id, ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, 
						ct.idioma_id, ct.traducido
					FROM ws_categorias_king ck
					inner join ws_inscripciones i on i.categoria_id=ck.id and i.deleted_at is null
					INNER JOIN ws_examen_respuesta e ON i.id=e.inscripcion_id AND e.deleted_at is null
					inner join users u on u.id=i.user_id and u.deleted_at is null 
					inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
					left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
					where ck.deleted_at is null and ck.evento_id=:evento_id2 and ck.id=:categoria_id and e.gran_final='.$gran_final;

		$categorias = DB::select($consulta, [':evento_id' => $evento_id, ':idioma_id' => $idioma_id, ':evento_id2' => $evento_id, ':categoria_id' => $cat_id] );

		$cant_cat = count($categorias);
		for ($j=0; $j < $cant_cat; $j++) {
			
			$categoria_id = $categorias[$j]->categoria_id;

				
			$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
							e.terminado, e.timeout, e.res_by_promedio, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
						    e.res_correctas, e.res_incorrectas, e.res_promedio, e.res_puntos, e.res_cant_pregs, e.res_tiempo, e.res_tiempo_format,
						    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
						    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
						    en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
					    	en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre,
						    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
						FROM ws_examen_respuesta e
						inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
						inner join users u on u.id=i.user_id and u.deleted_at is null
						inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null and ck.id=:categoria_id
						inner join ws_user_event ue on ue.user_id=u.id and ue.evento_id=:evento_id
						inner join ws_entidades en on en.id=u.entidad_id and en.deleted_at is null 
						left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
						left join images im on im.id=u.imagen_id and im.deleted_at is null 
						left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
						where e.deleted_at is null  and e.gran_final='.$gran_final;

			$examenes = DB::select($consulta_ex, [':female'=>User::$default_female, ':male'=>User::$default_male, ':categoria_id'=>$categoria_id, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id ] );

			$categorias[$j]->examenes = $examenes;

		}

		
		return $categorias;

	}



	public function getCalcularExamen($examen_id)
	{
		$examen = $this->calcularExamen($examen_id);
		return $examen;
	}



	public function calcularExamen($examen_id)
	{
		$examen = Examen_respuesta::findOrFail($examen_id);

		$inscripcion 	= Inscripcion::findOrFail($examen->inscripcion_id);
		$evaluacion 	= Evaluacion::findOrFail($examen->evaluacion_id);

		$categoria 		= Categoria_king::find($evaluacion->categoria_id);
		$user 			= User::find($inscripcion->user_id);


		$respuestas		= Respuesta::where('examen_respuesta_id', $examen_id)->get();

		$cantidad_pregs =  CalculoExamen::cantidadPreguntas($evaluacion->id);


		$cant_res = count($respuestas);
		$correctas = 0;
		$puntos = 0;

		for($i=0; $i < $cant_res; $i++){

			if ($respuestas[$i]->opcion_id) {
				
				$opcion = Opcion::find($respuestas[$i]->opcion_id);
				if ($opcion->is_correct) {
					$correctas++;

					$preg_king	= Pregunta_king::find($respuestas[$i]->pregunta_king_id);
					$puntos_preg = $preg_king['puntos'];

					$puntos = $puntos + $puntos_preg;
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
		


		$examen->promedio 		= $promedio;
		$examen->cantidad_pregs = $cantidad_pregs;
		$examen->correctas 		= $correctas;
		$examen->user 			= $user;
		$examen->categoria 		= $categoria;
		$examen->categoria 		= $categoria;
		$examen->evaluacion 	= $evaluacion;

		

		return $examen;
	}



}

