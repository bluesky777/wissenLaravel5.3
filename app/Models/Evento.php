<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Idioma;
use App\Models\Evento;

use DB;

class Evento extends Model {

	use SoftDeletes;

	protected $table = 'ws_eventos';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];


	public static function actual(){

		$consulta = 'SELECT * FROM ws_eventos where actual=true and deleted_at is null';
		$evento = DB::select($consulta)[0];
		return $evento;

	}

	public static function one($evento_id){

		$consulta = 'SELECT * FROM ws_eventos where id=? and deleted_at is null';
		$evento = DB::select($consulta, [$evento_id])[0];
		return $evento;

	}


	public static function todos(){

		$consulta = 'SELECT * FROM ws_eventos where deleted_at is null';
		$events = DB::select($consulta);


		$total = count($events);

		if ($total) {
			for($i=0; $i < $total; $i++){
				//$idiomas_reg = Idioma_registrado::where('evento_id', '=', $events[$i]->id)->get();
				//$events[$i]->idiomas_extras = array_column($idiomas_reg->toArray(), 'idioma_id');
				
				$consulta = 'SELECT i.id, ir.id as idioma_reg_id, i.nombre, i.abrev, i.original, i.used_by_system FROM ws_idiomas i, ws_idiomas_registrados ir
						where i.id=ir.idioma_id and ir.evento_id =:evento_id and ir.deleted_at is null';

				$events[$i]->idiomas_extras = DB::select($consulta, [':evento_id' => $events[$i]->id] );

				$events[$i]->idiomas = Evento::idiomas_all($events[$i]->id, $events[$i]);
			}
		}else{
			return [];
		}
		

		return $events;

	}


	public static function idiomas_all($evento_id, $evento=false){

		if (!$evento) {
			$evento = Evento::find($evento_id);
		}

		$idiomas_all = [];


		if ($evento->es_idioma_unico) {

			$consulta 		= 'SELECT * FROM ws_idiomas where id=? and deleted_at is null';
			$idiomas_all 	= DB::select($consulta, [$evento->idioma_principal_id] );

		}else{
			$consulta = 'SELECT i.id, ir.id as idioma_reg_id, i.nombre, i.abrev, i.original, i.used_by_system 
					FROM ws_idiomas i, ws_idiomas_registrados ir 
					where i.id=ir.idioma_id and 
						ir.evento_id =:evento_id and 
						ir.deleted_at is null and i.deleted_at is null';

			$idiomas_all = DB::select($consulta, [':evento_id' => $evento->id] );


			$consulta 		= 'SELECT * FROM ws_idiomas where id=? and deleted_at is null';
			$idioma_prin 	= DB::select($consulta, [$evento->idioma_principal_id] )[0];

			array_unshift($idiomas_all, $idioma_prin);

		}
	
		

		return $idiomas_all;

	}


}
