<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Opcion;


class Pregunta_traduc extends Model {

	use SoftDeletes;

	protected $table = 'ws_pregunta_traduc';



	public static function traducciones(&$pregs_king, $exa_resp_id=false)
	{
		
		$cant_preg = count($pregs_king);

		for($i=0; $i < $cant_preg; $i++){

			$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.pregunta_id, 
								t.idioma_id, t.traducido, i.nombre as idioma  
						FROM ws_pregunta_traduc t, ws_idiomas i
						where i.id=t.idioma_id and t.pregunta_id =:pregunta_id and t.deleted_at is null';

			$preg_trads = \DB::select(\DB::raw($consulta), array(':pregunta_id' => $pregs_king[$i]->id) );

			


			// Traeremos las opciones de cada traducción.
			Opcion::opciones($preg_trads, $exa_resp_id);

			$pregs_king[$i]->preguntas_traducidas = $preg_trads;


		}
		

		return $pregs_king;
	}


	public static function traducciones_single(&$preg_king)
	{
		
		$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.pregunta_id, 
							t.idioma_id, t.traducido, i.nombre as idioma  
					FROM ws_pregunta_traduc t, ws_idiomas i
					where i.id=t.idioma_id and t.pregunta_id =:pregunta_id and t.deleted_at is null';

		$preg_trads = \DB::select(\DB::raw($consulta), array(':pregunta_id' => $preg_king->id) );

		// Traeremos las opciones de cada traducción.
		Opcion::opciones($preg_trads);
		
		$preg_king->preguntas_traducidas = $preg_trads;


		return $preg_king;
	}

}


