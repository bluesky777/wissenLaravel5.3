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


use DB;
use Excel;


class ExportarImportarController extends Controller {




	public function putVerCambios(){
		$user 		= User::fromToken();
		$fecha_ini 	= Request::input('fecha_ini');
		$fecha_fin 	= Request::input('fecha_fin');


		$consulta = 'SELECT u.*, e.nombre as nombre_entidad, e.alias as alias_entidad, 
						ue.user_id, ue.evento_id, ue.nivel_id, ue.pagado, ue.pazysalvo, ue.signed_by
					FROM  users u 
					INNER JOIN ws_user_event ue on ue.user_id=u.id 
					LEFT JOIN ws_entidades e on e.id=u.entidad_id
					where u.deleted_at is null and u.created_at > ? and u.created_at < ? ';

		$usuarios = DB::select($consulta, [ $fecha_ini, $fecha_fin ] );

		$cant = count($usuarios);

		for ($i=0; $i < $cant; $i++) { 
			
			$consulta_ex = 'SELECT e.id as examen_id, e.inscripcion_id, e.evaluacion_id, i.categoria_id, e.active,
							e.terminado, e.timeout, e.created_at as examen_at, i.user_id, i.allowed_to_answer, i.signed_by, i.created_at as inscrito_at,
							ct.nombre as nombre_categ, ct.abrev as abrev_categ, ct.descripcion as descripcion_categ, ct.idioma_id
						FROM ws_examen_respuesta e
						inner join ws_inscripciones i on i.id=e.inscripcion_id and i.user_id=:user_id and i.deleted_at is null
						inner join ws_evaluaciones ev on ev.id=e.evaluacion_id and e.deleted_at is null
						inner join ws_categorias_king ck on ck.id=i.categoria_id and ck.deleted_at is null
						left join ws_categorias_traduc ct on ck.id=ct.categoria_id and ct.idioma_id=:idioma_id and ct.deleted_at is null
						where e.deleted_at is null ';

			$examenes = DB::select($consulta_ex, [ ':user_id' => $usuarios[$i]->id, ':idioma_id' => $usuarios[$i]->idioma_main_id ] );

			$usuarios[$i]->examenes = $examenes;
		}

		return $usuarios;

	}



	public function putExportarExcel(){
		$user 		= User::fromToken();
		$fecha_ini 	= Request::input('fecha_ini');
		$fecha_fin 	= Request::input('fecha_fin');


		$consulta = 'SELECT u.*, e.nombre as nombre_entidad, e.alias as alias_entidad, 
						ue.user_id, ue.evento_id, ue.nivel_id, ue.pagado, ue.pazysalvo, ue.signed_by
					FROM  users u 
					INNER JOIN ws_user_event ue on ue.user_id=u.id 
					LEFT JOIN ws_entidades e on e.id=u.entidad_id
					where u.deleted_at is null and u.created_at > ? and u.created_at < ? ';

		$usuarios = DB::select($consulta, [ $fecha_ini, $fecha_fin ] );

		return Excel::create('Examenes_exportados', function($excel) use ($usuarios, $fecha_ini) {
			//$excel->setTitle('Examenes exportados '.$fecha_ini);
		    //$excel->setCreator('WissenSystem')->setCompany('Joseth BlueSky');
			
			$excel->sheet('Participantes', function($sheet) use ($usuarios)
			{
				//$sheet->fromArray($usuarios);
			});
		})->download('xlsx');


	}




}


