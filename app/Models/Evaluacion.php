<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


//use App\Models\Idioma;


class Evaluacion extends Model {

	use SoftDeletes;

	protected $table = 'ws_evaluaciones';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];


	public static function setElseNotActual($evaluacion){

		$evaluaciones = Evaluacion::where('actual', true)
									->where('categoria_id', $evaluacion->categoria_id)
									->where('evento_id', $evaluacion->evento_id)
									->update(['actual' => false]);
		
		return $evaluaciones;

	}


	public static function actual($evento_id, $categoria_id){

		$evaluaciones = Evaluacion::where('actual', true)
									->where('categoria_id', $categoria_id)
									->where('evento_id', $evento_id)
									->first();
		
		return $evaluaciones;

	}


	public static function crearPrimera($evento_id, $categoria_id, $descripcion, $duracion_preg=20, $duracion_exam=20, $id=false){

		$evaluacion 				= new Evaluacion;
		$evaluacion->categoria_id 	= $categoria_id;
		$evaluacion->evento_id 		= $evento_id;
		$evaluacion->descripcion 	= $descripcion;
		$evaluacion->duracion_preg 	= $duracion_preg;
		$evaluacion->duracion_exam 	= $duracion_exam;
		$evaluacion->one_by_one 	= true;
		$evaluacion->actual 		= true;

		if ($id) {
			$evaluacion->id = $id;
		}
		
		$evaluacion->save();
		
		return $evaluacion;

	}


}
