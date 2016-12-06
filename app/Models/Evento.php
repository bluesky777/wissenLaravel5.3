<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Idioma;
use App\Models\Evento;


class Evento extends Model {

	use SoftDeletes;

	protected $table = 'ws_eventos';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];


	public static function actual(){

		$evento = Evento::where('actual', true)->first();
		return $evento;

	}


	public static function todos(){

		$events = Evento::all();

		$total = count($events);

		if ($total) {
			for($i=0; $i < $total; $i++){
				//$idiomas_reg = Idioma_registrado::where('evento_id', '=', $events[$i]->id)->get();
				//$events[$i]->idiomas_extras = array_column($idiomas_reg->toArray(), 'idioma_id');
				
				$consulta = 'SELECT i.id, ir.id as idioma_reg_id, i.nombre, i.abrev, i.original, i.used_by_system FROM ws_idiomas i, ws_idiomas_registrados ir
						where i.id=ir.idioma_id and ir.evento_id =:evento_id and ir.deleted_at is null';

				$events[$i]->idiomas_extras = \DB::select(\DB::raw($consulta), array(':evento_id' => $events[$i]->id) );

				$events[$i]->idiomas = Evento::idiomas_all($events[$i]->id);
			}
		}else{
			return [];
		}
		

		return $events;

	}


	public static function idiomas_all($evento_id){

		$evento = Evento::find($evento_id);

		$idiomas_all = [];


		if ($evento->es_idioma_unico) {

			// Hago un Where para que venga un array y no un objeto.
			$idiomas_all = Idioma::where('id', '=', $evento->idioma_principal_id)->get();

		}else{
			$consulta = 'SELECT i.id, ir.id as idioma_reg_id, i.nombre, i.abrev, i.original, i.used_by_system 
					FROM ws_idiomas i, ws_idiomas_registrados ir 
					where i.id=ir.idioma_id and 
						ir.evento_id =:evento_id and 
						ir.deleted_at is null';

			$idiomas_all = \DB::select(\DB::raw($consulta), array(':evento_id' => $evento->id) );

			$idioma_prin = Idioma::find($evento->idioma_principal_id);

			array_unshift($idiomas_all, $idioma_prin);

		}
	
		

		return $idiomas_all;

	}


}
