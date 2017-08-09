<?php namespace App\Http\Controllers\ExportarImportar;

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

use App\Models\User_event;
use App\Models\Role;


use DB;
use Excel;


class ExportarImportarController extends Controller {




	public function putVerCambios(){
		$user 		= User::fromToken();
		$fecha_ini 	= Request::input('fecha_ini');
		$fecha_fin 	= Request::input('fecha_fin');


		$consulta = 'SELECT u.*, e.nombre as nombre_entidad, e.alias as alias_entidad, 
						ue.user_id, ue.evento_id, ue.nivel_id, ue.pagado, ue.pazysalvo, ue.signed_by, True as exportar
					FROM  users u 
					INNER JOIN ws_user_event ue on ue.user_id=u.id 
					LEFT JOIN ws_entidades e on e.id=u.entidad_id
					where u.deleted_at is null and u.created_at > ? and u.created_at < ? ';

		$usuarios = DB::select($consulta, [ $fecha_ini, $fecha_fin ] );

		$cant = count($usuarios);

		for ($i=0; $i < $cant; $i++) { 
			
			$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
							e.terminado, e.gran_final, e.timeout, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
							ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id
						FROM ws_examen_respuesta e
						inner join ws_inscripciones i on i.id=e.inscripcion_id and i.user_id=:user_id and i.deleted_at is null
						inner join ws_evaluaciones ev on ev.id=e.evaluacion_id and e.deleted_at is null
						inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null
						left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
						where e.deleted_at is null ';

			$examenes = DB::select($consulta_ex, [ ':user_id' => $usuarios[$i]->id, ':idioma_id' => $usuarios[$i]->idioma_main_id ] );


			$cant_exa = count($examenes);

			for ($j=0; $j < $cant_exa; $j++) { 
					
				$consulta_ex = 'SELECT * FROM ws_respuestas r WHERE r.examen_respuesta_id=:examen_id';

				$respuestas = DB::select($consulta_ex, [ ':examen_id' => $examenes[$j]->examen_id ] );

				$examenes[$j]->respuestas = $respuestas;
			}

			$usuarios[$i]->examenes = $examenes;
		}

		return $usuarios;

	}



	public function putRevisarDatos(){
		$user 				= User::fromToken();
		$array_usuarios 	= Request::input('array_usuarios');
		$array_res 			= [];

		$cant = count($array_usuarios);

		for ($i=0; $i < $cant; $i++) { 

			$usuario 	= $array_usuarios[$i];

			$consulta = 'SELECT u.*, e.nombre as nombre_entidad, e.alias as alias_entidad, 
							ue.user_id, ue.evento_id, ue.nivel_id, ue.pagado, ue.pazysalvo, ue.signed_by
						FROM  users u 
						INNER JOIN ws_user_event ue on ue.user_id=u.id 
						LEFT JOIN ws_entidades e on e.id=u.entidad_id
						where u.deleted_at is null and u.username=? ';

			$usuarios = DB::select($consulta, [ $usuario['username'] ] );

			if (count($usuarios)>0) {

				$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
								e.terminado, e.timeout, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
								ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id
							FROM ws_examen_respuesta e
							inner join ws_inscripciones i on i.id=e.inscripcion_id and i.user_id=:user_id and i.deleted_at is null
							inner join ws_evaluaciones ev on ev.id=e.evaluacion_id and e.deleted_at is null
							inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null
							left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
							where e.deleted_at is null ';

				$examenes = DB::select($consulta_ex, [ ':user_id' => $usuarios[0]->id, ':idioma_id' => $usuarios[0]->idioma_main_id ] );

				$usuarios[0]->examenes = $examenes;

				$comparando = [ "en_db" => $usuarios[0], "a_importar" => $usuario ];

				array_push($array_res, $comparando);
			}
		}

		return $array_res;


	}





	public function putImportarDatos(){
		$user 				= User::fromToken();
		$array_usuarios 	= Request::input('array_usuarios');
		$array_res 			= ['importados' => 0, 'no_importados' => 0, 'usuarios_agregados' => [] ];

		$cant = count($array_usuarios);

		for ($i=0; $i < $cant; $i++) { 

			$usu_a_importar 	= $array_usuarios[$i]['a_importar'];

			$consulta = 'SELECT * FROM  users u where u.username=? ';

			$usuarios = DB::select($consulta, [ $usu_a_importar['username'] ] );

			if (count($usuarios)>0) {
				$usu_a_importar['username'] = $usu_a_importar['username'] . '-' . rand(99, 999);
			}

			$usuario 				= new User;
			$usuario->nombres 		= $usu_a_importar['nombres'];
			$usuario->apellidos 	= $usu_a_importar['apellidos'];
			$usuario->sexo 			= $usu_a_importar['sexo'];
			$usuario->username 		= $usu_a_importar['username'];
			$usuario->password 		= $usu_a_importar['password'];
			$usuario->email 		= $usu_a_importar['email'];
			$usuario->is_superuser 	= $usu_a_importar['is_superuser'];
			$usuario->cell 			= $usu_a_importar['cell'];		
			$usuario->edad 			= $usu_a_importar['edad'];	
			$usuario->entidad_id	= $usu_a_importar['entidad_id'];
			$usuario->idioma_main_id 		= $usu_a_importar['idioma_main_id'];
			$usuario->evento_selected_id 	= $usu_a_importar['evento_selected_id'];
			$usuario->save();

			array_push($array_res['usuarios_agregados'], $usuario);
		
			$role = Role::where('name', 'Participante')->first();
			$usuario->attachRole($role);

			$user_event 			= new User_event;
			$user_event->user_id 	= $usuario->id;
			$user_event->evento_id 	= $usu_a_importar['evento_id'];
			$user_event->nivel_id 	= $usu_a_importar['nivel_id'];
			$user_event->signed_by 	= $usu_a_importar['signed_by'];
			$user_event->save();


			$examenes_a_insert 		= $usu_a_importar['examenes'];
			$cant_ins = count($examenes_a_insert);

			for($j=0; $j < $cant_ins; $j++){
				$categoria_id 	= $examenes_a_insert[$j]['categoria_id'];	
				$consulta 		= 'INSERT INTO ws_inscripciones(user_id, categoria_id, signed_by) VALUES(:user_id, :categoria_id, :yo_id)';
				DB::select($consulta, [':user_id' => $usuario->id, ':categoria_id' => $categoria_id, ":yo_id" => $user_event->signed_by] );
				$inscripcion 	= Inscripcion::one($usuario->id, $categoria_id);
				


				$examen 					= new Examen_respuesta;
				$examen->inscripcion_id 	= $inscripcion->id;
				$examen->evaluacion_id 		= $examenes_a_insert[$j]['evaluacion_id'];
				$examen->categoria_id 		= $categoria_id;
				$examen->terminado 			= $examenes_a_insert[$j]['terminado'];
				$examen->gran_final 		= $examenes_a_insert[$j]['gran_final'];
				$examen->save();

				$respuestas 		= $examenes_a_insert[$j]['respuestas'];
				$cant_respuestas 	= count($respuestas);

				for ($k=0; $k < $cant_respuestas; $k++) { 
					
					$res = new Respuesta;
					$res->examen_respuesta_id	= $examen->id;
					$res->pregunta_king_id		= $respuestas[$k]['pregunta_king_id'];
					$res->tiempo				= $respuestas[$k]['tiempo'];
					$res->tiempo_aproximado		= $respuestas[$k]['tiempo_aproximado'];
					$res->preg_traduc_id		= $respuestas[$k]['preg_traduc_id'];
					$res->idioma_id				= $respuestas[$k]['idioma_id'];
					$res->tipo_pregunta			= $respuestas[$k]['tipo_pregunta'];
					$res->puntos_maximos 		= $respuestas[$k]['puntos_maximos'];
					$res->puntos_adquiridos 	= $respuestas[$k]['puntos_adquiridos'];
					$res->opcion_id 			= $respuestas[$k]['opcion_id'];
					$res->save();
					
				}


			}

			$array_res['importados'] = $array_res['importados'] + 1;
			
		}

		return $array_res;


	}




}


