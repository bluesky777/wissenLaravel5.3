<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo_pregunta extends Model {

	use SoftDeletes;

	protected $table = 'ws_grupos_preguntas';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function deEvaluacion(&$preguntas_king, $evaluacion_id, $exa_resp_id=false)
	{
		$consulta = 'SELECT gp.*, pe.evaluacion_id, pe.orden FROM ws_grupos_preguntas gp 
					inner join ws_pregunta_evaluacion pe on pe.grupo_pregs_id=gp.id
					where pe.evaluacion_id=:evaluacion_id and gp.deleted_at is null;';

		$grupos_preg = \DB::select(\DB::raw($consulta), array(':evaluacion_id' => $evaluacion_id) );

		Contenido_traduc::traducciones_and_push($grupos_preg, $preguntas_king, $exa_resp_id);

		return $preguntas_king;

	}



}
