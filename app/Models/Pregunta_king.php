<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pregunta_king extends Model {

	use SoftDeletes;

	protected $table = 'ws_preguntas_king';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];


	public static function ordenarMultiArray ($toOrderArray, $field, $inverse = false) {
	    $position = array();
	    $newRow = array();
	    foreach ($toOrderArray as $key => $row) {
	            $position[$key]  = $row[$field];
	            $newRow[$key] = $row;
	    }
	    if ($inverse) {
	        arsort($position);
	    }
	    else {
	        asort($position);
	    }
	    $returnArray = array();
	    foreach ($position as $key => $pos) {     
	        $returnArray[] = $newRow[$key];
	    }
	    return $returnArray;
	}


	public static function deEvaluacion($evaluacion_id, $exa_resp_id=false)
	{
		$consulta = 'SELECT p.*, pe.evaluacion_id, pe.orden FROM ws_preguntas_king p 
					inner join ws_pregunta_evaluacion pe on pe.pregunta_id=p.id
					where pe.evaluacion_id=:evaluacion_id and p.deleted_at is null;';

		$preguntas_king = \DB::select(\DB::raw($consulta), array(':evaluacion_id' => $evaluacion_id) );


		Pregunta_traduc::traducciones($preguntas_king, $exa_resp_id); // Paso por referencia la nivel_king

		return $preguntas_king;

	}



}



