<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria_traduc extends Model {

	protected $table="ws_categorias_traduc";
	use SoftDeletes;

	protected $softDelete = true;

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function traducciones(&$categorias_king)
	{
		
		$cant_dis = count($categorias_king);

		for($i=0; $i < $cant_dis; $i++){

			$consulta = 'SELECT t.id, t.nombre, t.abrev, t.categoria_id, t.descripcion, t.idioma_id, t.traducido, i.nombre as idioma  
					FROM ws_categorias_traduc t, ws_idiomas i
					where i.id=t.idioma_id and t.categoria_id =:categoria_id and t.deleted_at is null';

			$categs_trads = \DB::select($consulta, array(':categoria_id' => $categorias_king[$i]->id) );

			$categorias_king[$i]->categorias_traducidas = $categs_trads;

		}
		
		

		return $categorias_king;
	}



	public static function traducciones_single(&$categ_king)
	{
		
		$consulta = 'SELECT t.id, t.nombre, t.abrev, t.categoria_id, t.descripcion, t.idioma_id, t.traducido, i.nombre as idioma  
				FROM ws_categorias_traduc t, ws_idiomas i
				where i.id=t.idioma_id and t.categoria_id =:categ_id and t.deleted_at is null';

		$dis_trads = \DB::select($consulta, array(':categ_id' => $categ_king->id) );

		$categ_king->categorias_traducidas = $dis_trads;


		return $categ_king;
	}

}
