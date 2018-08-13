<?php namespace App\Http\Controllers\ExportarImportar;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Carbon\Carbon;

use App\Models\Categoria_traduc;
use App\Models\Nivel_king;
use App\Models\Nivel_traduc;
use App\Models\Disciplina_king;
use App\Models\Disciplina_traduc;
use App\Models\Entidad;



use DB;
use Excel;


class DatosElectronController extends Controller {




	public function getDescargar(){
		//$user 		= User::fromToken();
		
		
		$now 			= Carbon::now('America/Bogota');
		$resultado 		= [];
		$evento_id 		= Request::input('evento_id', 1);
		
		
		// Niveles
		$consulta = 'SELECT id as rowid, id, nombre, evento_id FROM ws_niveles_king WHERE deleted_at is null AND evento_id=? ';
		$niveles = DB::select($consulta, [$evento_id]);
		$resultado['niveles'] = $niveles;
		
		
		$consulta = 'SELECT t.id as rowid, t.id, t.nombre, t.nivel_id, t.descripcion, t.idioma_id, t.traducido FROM ws_niveles_traduc t 
				INNER JOIN ws_niveles_king k on k.id=t.nivel_id and k.deleted_at is null and t.deleted_at is null  and t.idioma_id=1';
		$niveles_traducidas = DB::select($consulta);
		$resultado['niveles_traducidas'] = $niveles_traducidas;
		
		
		// Disciplinas 
		$consulta = 'SELECT id as rowid, id, nombre, evento_id FROM ws_disciplinas_king WHERE deleted_at is null AND evento_id=? ';
		$disciplinas = DB::select($consulta, [$evento_id]);
		$resultado['disciplinas'] = $disciplinas;

		
		$consulta = 'SELECT t.id as rowid, t.id, t.nombre, t.disciplina_id, t.descripcion, t.idioma_id, t.traducido FROM ws_disciplinas_traduc t 
				INNER JOIN ws_disciplinas_king k on k.id=t.disciplina_id and k.deleted_at is null and t.deleted_at is null  and t.idioma_id=1';
		$disciplinas_traducidas = DB::select($consulta);
		$resultado['disciplinas_traducidas'] = $disciplinas_traducidas;
		
		
		

		// Categorías
		$consulta = 'SELECT id as rowid, id, nombre, nivel_id, disciplina_id, evento_id FROM  ws_categorias_king WHERE deleted_at is null AND evento_id=? ';
		$categorias = DB::select($consulta, [$evento_id]);
		$resultado['categorias'] = $categorias;

		
		$consulta = 'SELECT t.id as rowid, t.id, t.nombre, t.abrev, t.categoria_id, t.descripcion, t.idioma_id, t.traducido FROM ws_categorias_traduc t 
				INNER JOIN ws_categorias_king k on k.id=t.categoria_id and k.deleted_at is null and t.deleted_at is null and t.idioma_id=1';
		$categorias_traducidas = DB::select($consulta);
		$resultado['categorias_traducidas'] = $categorias_traducidas;
		
		
		
		
		// Entidades
		$consulta = 'SELECT e.id as rowid, e.id, e.nombre, e.lider_id, e.lider_nombre, e.logo_id, e.telefono, e.alias, e.evento_id, e.logo_id
					FROM ws_entidades e WHERE e.evento_id=? and e.deleted_at is null';
		$enti = DB::select($consulta, [$evento_id]);
		$resultado['entidades'] = $enti;
		
		
		$consulta 		= 'SELECT id as rowid, id, categoria_id, evento_id, actual, descripcion, duracion_preg, duracion_exam, one_by_one, puntaje_por_promedio FROM ws_evaluaciones WHERE evento_id = ? and actual=1 and deleted_at is null';
		$evaluaciones 	= DB::select($consulta, [$evento_id]);
		$resultado['evaluaciones'] = $evaluaciones;
		
		
		
		
		// PREGUNTAS
		$consulta = 'SELECT pk.id as rowid, pk.id, pk.descripcion, pk.tipo_pregunta, pk.duracion, pk.categoria_id, pk.puntos, pk.aleatorias, pk.added_by
			FROM ws_preguntas_king pk
			INNER JOIN ws_pregunta_evaluacion pev on pev.pregunta_id=pk.id and pk.deleted_at is null 
			INNER JOIN ws_evaluaciones ev on ev.id=pev.evaluacion_id and ev.evento_id=? and ev.actual=1 and ev.deleted_at is null 
			WHERE ev.categoria_id!=9 and ev.categoria_id!=10 and ev.categoria_id!=11';
			// Para que NO traiga las preguntas de religión
		$preguntas = DB::select($consulta, [$evento_id]);
		$resultado['preguntas'] = $preguntas;

		
		
		$consulta = 'SELECT p.id as rowid, p.id, p.enunciado, p.ayuda, p.pregunta_id, p.idioma_id, p.texto_arriba, p.texto_abajo, p.traducido
			FROM ws_pregunta_traduc p
			INNER JOIN ws_preguntas_king pk on pk.id=p.pregunta_id and p.deleted_at is null and pk.deleted_at is null and p.idioma_id=1
			INNER JOIN ws_pregunta_evaluacion pev on pev.pregunta_id=pk.id
			INNER JOIN ws_evaluaciones ev on ev.id=pev.evaluacion_id and ev.evento_id=? and ev.actual=1 and ev.deleted_at is null';
		$preguntas_traducidas 				= DB::select($consulta, [$evento_id]);
		$resultado['preguntas_traducidas'] 	= $preguntas_traducidas;
		
		
		
		$consulta = 'SELECT o.id as rowid, o.id, o.definicion, o.pregunta_traduc_id, o.image_id, o.orden, o.is_correct, o.added_by 
			FROM ws_opciones o
			INNER JOIN ws_pregunta_traduc p on p.id=o.pregunta_traduc_id and p.deleted_at is null
			INNER JOIN ws_preguntas_king pk on pk.id=p.pregunta_id and pk.deleted_at is null and p.idioma_id=1
			INNER JOIN ws_pregunta_evaluacion pev on pev.pregunta_id=pk.id
			INNER JOIN ws_evaluaciones ev on ev.id=pev.evaluacion_id and ev.evento_id=? and ev.actual=1 and ev.deleted_at is null';
		$opciones = DB::select($consulta, [$evento_id]);
		$resultado['opciones'] 	= $opciones;


		
		$consulta = 'SELECT o.id as rowid, o.id, o.evaluacion_id, o.pregunta_id, o.grupo_pregs_id, o.orden, o.aleatorias, o.added_by 
			FROM ws_pregunta_evaluacion o
			INNER JOIN ws_evaluaciones ev on ev.id=o.evaluacion_id and ev.evento_id=? and ev.actual=1 and ev.deleted_at is null';
		$pregunta_evaluacion = DB::select($consulta, [$evento_id]);
		$resultado['pregunta_evaluacion'] 	= $pregunta_evaluacion;


				
		

		
		

		return $resultado;

	}





}


