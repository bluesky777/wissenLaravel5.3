<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_event extends Model {

	protected $table="ws_user_event";
	use SoftDeletes;

	protected $softDelete = true;

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function todas($evento_id)
	{
		$consulta = 'SELECT * 
					FROM ws_user_event e
					where e.deleted_at is null';

		$entidades = \DB::select(\DB::raw($consulta), array(':evento_id' => $evento_id) );

		return $entidades;

	}



}
