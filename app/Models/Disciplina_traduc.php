<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disciplina_traduc extends Model {

	protected $table="ws_disciplinas_traduc";
	use SoftDeletes;

	protected $softDelete = true;

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function traducciones(&$disciplinas_king)
	{
		
		$cant_dis = count($disciplinas_king);

		for($i=0; $i < $cant_dis; $i++){

			$consulta = 'SELECT dt.id, dt.nombre, dt.disciplina_id, dt.descripcion, dt.idioma_id, dt.traducido, i.nombre as idioma  
					FROM ws_disciplinas_traduc dt, ws_idiomas i
					where i.id=dt.idioma_id and dt.disciplina_id =:disciplina_id and dt.deleted_at is null';

			$dis_trads = \DB::select(\DB::raw($consulta), array(':disciplina_id' => $disciplinas_king[$i]->id) );

			//$dis_trads = Disciplina_traduc::where('disciplina_id', '=', $disciplinas_king[$i]->id)->get();
			$disciplinas_king[$i]->disciplinas_traducidas = $dis_trads;

		}
		
		

		return $disciplinas_king;
	}



	public static function traducciones_single(&$disciplina_king)
	{
		
		$consulta = 'SELECT dt.id, dt.nombre, dt.disciplina_id, dt.descripcion, dt.idioma_id, dt.traducido, i.nombre as idioma  
				FROM ws_disciplinas_traduc dt, ws_idiomas i
				where i.id=dt.idioma_id and dt.disciplina_id =:disciplina_id and dt.deleted_at is null';

		$dis_trads = \DB::select(\DB::raw($consulta), array(':disciplina_id' => $disciplina_king->id) );

		$disciplina_king->disciplinas_traducidas = $dis_trads;


		return $disciplina_king;
	}

}
