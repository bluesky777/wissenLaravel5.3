<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;


class Entidad extends Model {

	protected $table="ws_entidades";
	use SoftDeletes;

	protected $softDelete = true;

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function todas($evento_id)
	{
		$consulta = 'SELECT e.id, e.nombre, e.lider_id, e.lider_nombre, e.logo_id, 
						e.telefono, e.alias, e.evento_id as idioma,
						e.logo_id, IFNULL(i.nombre, "system/avatars/no-photo.jpg") as logo_nombre, i.publica
					FROM ws_entidades e
					left join images i on i.id=e.logo_id and i.deleted_at is null
					where e.evento_id =:evento_id and e.deleted_at is null';

		$entidades = DB::select($consulta, [':evento_id' => $evento_id] );

		return $entidades;

	}



}
