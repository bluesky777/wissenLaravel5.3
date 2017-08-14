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


class PuestosController extends Controller {




	public function putTodosExamenesEnt(){
		$user 		= User::fromToken();
		$evento_id 	= Request::input('evento_id');
		$idioma_id 	= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final = Request::input('gran_final', false);

		$requested_entidades 	= Request::input('requested_entidades');


		
		$consulta = 'SELECT distinct en.id as entidad_id, en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
						en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre
					FROM  ws_entidades en 
					inner join users u on en.id=u.entidad_id and en.deleted_at is null and u.deleted_at is null  
					left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
					where en.deleted_at is null and en.evento_id=:evento_id ';

		$entidades_f = DB::select($consulta, [':evento_id' => $evento_id] );
		$entidades = [];
		
		// Si hay entidades especificadas en el pedido...
		if ($requested_entidades) {
			// Eliminamos las entidades NO pedidas
			$cant = count($entidades_f);

			for ($i=0; $i < $cant; $i++) { 
				$hay = in_array($entidades_f[$i]->entidad_id.'', $requested_entidades);

				if ($hay) {
					array_push($entidades, $entidades_f[$i]);
				}
			}
		}else{
			$entidades = $entidades_f;
		}

		$cant_ent = count($entidades);
		for ($j=0; $j < $cant_ent; $j++) {
			
			$entidad_id 					= $entidades[$j]->entidad_id;
			
			$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
							e.terminado, e.timeout, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
						    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
						    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
						    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
						FROM ws_examen_respuesta e
						inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
						inner join ws_evaluaciones ev on ev.id=e.evaluacion_id and ev.actual=true and e.deleted_at is null
						inner join users u on u.id=i.user_id and u.deleted_at is null
						inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.evento_id=:evento_id and ck.deleted_at is null
						left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
						left join images im on im.id=u.imagen_id and im.deleted_at is null 
						where e.deleted_at is null and u.entidad_id=:entidad_id and e.gran_final='.$gran_final;

			$examenes = DB::select($consulta_ex, [':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id, ':idioma_id' => $idioma_id, ':entidad_id' => $entidad_id] );

			$cant = count($examenes);
			for ($i=0; $i < $cant; $i++) { 
				$examenes[$i]->resultados = CalculoExamen::calcular($examenes[$i]);
			}

			$entidades[$j]->examenes = $examenes;

		}


		return $entidades;

	}



	public function putExamenesEntCateg(){
		$user 		= User::fromToken();
		$evento_id 	= Request::input('evento_id');
		$idioma_id 	= Request::input('idioma_id', $user->idioma_main_id);
		$gran_final = Request::input('gran_final', false);

		$requested_entidades 	= Request::input('requested_entidades');
		$requested_categorias 	= Request::input('requested_categorias');


		
		$consulta = 'SELECT distinct en.id as entidad_id, en.nombre as nombre_entidad, en.alias as alias_entidad, en.lider_id, en.lider_nombre, en.alias,
						en.logo_id, IFNULL(CONCAT("perfil/", im2.nombre), CONCAT("perfil/system/avatars/no-photo.jpg")) as logo_nombre
					FROM  ws_entidades en 
					inner join users u on en.id=u.entidad_id and en.deleted_at is null and u.deleted_at is null  
					left join images im2 on im2.id=en.logo_id and im2.deleted_at is null 
					where en.deleted_at is null and en.evento_id=:evento_id ';

		$entidades_f = DB::select($consulta, [':evento_id' => $evento_id] );
		$entidades = [];

		// Si hay entidades especificadas en el pedido...
		if ($requested_entidades) {
			// Eliminamos las entidades NO pedidas
			$cant = count($entidades_f);

			for ($i=0; $i < $cant; $i++) { 
				$hay = in_array($entidades_f[$i]->entidad_id.'', $requested_entidades);

				if ($hay) {
					array_push($entidades, $entidades_f[$i]);
				}
			}
		}else{
			$entidades = $entidades_f;
		}


	


		$categorias 		= [];
		$cant_categorias 	= count($requested_categorias);

		// Si hay categorías especificadas ...
		if ($requested_categorias) {
			for ($l=0; $l < $cant_categorias; $l++) { 
				
				$consulta_categ = 'SELECT distinct ck.id as categoria_id, ct.id as categ_traduc_id, ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, 
									ct.idioma_id, ct.traducido
								FROM ws_categorias_king ck
								left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
								where ck.deleted_at is null and ck.evento_id=:evento_id and ck.id=:categoria_id';

				$categorias_f = DB::select($consulta_categ, [':idioma_id' => $idioma_id, ':evento_id' => $evento_id, ':categoria_id' => $requested_categorias[$l] ] );
				if (count($categorias_f) > 0) {
					array_push($categorias, $categorias_f[0]);
				}
			}
		}else{
			$consulta_categ = 'SELECT distinct ck.id as categoria_id, ct.id as categ_traduc_id, ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, 
								ct.idioma_id, ct.traducido
							FROM ws_categorias_king ck
							left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
							where ck.deleted_at is null and ck.evento_id=:evento_id';

			$categorias = DB::select($consulta_categ, [':idioma_id' => $idioma_id, ':evento_id' => $evento_id] );
			
		}


		$cant_ent = count($entidades);
		for ($j=0; $j < $cant_ent; $j++) {
			
			$entidad_id 					= $entidades[$j]->entidad_id;
			$entidades[$j]->categorias 		= [];
			$cant_categ 					= count($categorias);

			for ($m=0; $m < $cant_categ; $m++) { 
				array_push($entidades[$j]->categorias, clone $categorias[$m]);
			}

			for ($k=0; $k < $cant_categ; $k++) { 
				
				$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
								e.terminado, e.timeout, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
							    u.nombres, u.apellidos, u.sexo, u.username, u.entidad_id,
							    u.imagen_id, IFNULL(CONCAT("perfil/", im.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
							    ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id, ct.traducido
							FROM ws_examen_respuesta e
							inner join ws_inscripciones i on i.id=e.inscripcion_id and i.deleted_at is null
							inner join ws_evaluaciones ev on ev.id=e.evaluacion_id and ev.actual=true and e.deleted_at is null
							inner join users u on u.id=i.user_id and u.deleted_at is null
							inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.id=:categoria_id and ck.deleted_at is null
							left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
							left join images im on im.id=u.imagen_id and im.deleted_at is null 
							where e.deleted_at is null and u.entidad_id=:entidad_id and e.gran_final='.$gran_final;

				$examenes = DB::select($consulta_ex, [':female'=>User::$default_female, ':male'=>User::$default_male, ':categoria_id' => $entidades[$j]->categorias[$k]->categoria_id, ':idioma_id' => $idioma_id, ':entidad_id' => $entidad_id] );

				$cant = count($examenes);
				for ($i=0; $i < $cant; $i++) { 
					$examenes[$i]->resultados = CalculoExamen::calcular($examenes[$i]);
				}

				$entidades[$j]->categorias[$k]->examenes = $examenes;
			}

		}

		


		return $entidades;

	}




}

