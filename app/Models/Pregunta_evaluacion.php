<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Categoria_king;
use DB;



class Pregunta_evaluacion extends Model {

	protected $table = 'ws_pregunta_evaluacion';

	protected $dates = ['created_at', 'updated_at'];




	public static function preguntas($evaluacion_id)
	{

		$consulta = 'SELECT p.id, pe.id as inscripcion_id, pe.evaluacion_id, pe.pregunta_id, pe.grupo_pregs_id, pe.orden, pe.aleatorias, pe.added_by, 
						p.descripcion, p.tipo_pregunta, p.duracion, p.categoria_id, p.puntos
					FROM ws_preguntas_king p
					inner join ws_pregunta_evaluacion pe on pe.pregunta_id=p.id and p.deleted_at is null
					where pe.evaluacion_id = :evaluacion_id';

		return DB::select($consulta, [":evaluacion_id"=>$evaluacion_id]);
		
	}


	

}
