<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nivel_traduc extends Model {

	protected $table="ws_niveles_traduc";
	use SoftDeletes;

	protected $softDelete = true;

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function traducciones(&$niveles_king)
	{
		
		$cant_dis = count($niveles_king);

		for($i=0; $i < $cant_dis; $i++){

			$consulta = 'SELECT t.id, t.nombre, t.nivel_id, t.descripcion, t.idioma_id, t.traducido, i.nombre as idioma  
					FROM ws_niveles_traduc t, ws_idiomas i
					where i.id=t.idioma_id and t.nivel_id =:nivel_id and t.deleted_at is null';

			$niv_trads = \DB::select(\DB::raw($consulta), array(':nivel_id' => $niveles_king[$i]->id) );

			$niveles_king[$i]->niveles_traducidos = $niv_trads;

		}
		
		

		return $niveles_king;
	}



	public static function traducciones_single(&$nivel_king)
	{
		
		$consulta = 'SELECT t.id, t.nombre, t.nivel_id, t.descripcion, t.idioma_id, t.traducido, i.nombre as idioma  
				FROM ws_niveles_traduc t, ws_idiomas i
				where i.id=t.idioma_id and t.nivel_id =:nivel_id and t.deleted_at is null';

		$dis_trads = \DB::select(\DB::raw($consulta), array(':nivel_id' => $nivel_king->id) );

		$nivel_king->niveles_traducidos = $dis_trads;


		return $nivel_king;
	}

}
