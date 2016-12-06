<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



use App\Models\Pregunta_agrupada;



class Contenido_traduc extends Model {

	use SoftDeletes;

	protected $table = 'ws_contenido_traduc';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	public static function traducciones_and_push(&$grupos_preg, &$preg_king, $exa_resp_id=false)
	{


		foreach ($grupos_preg as $key => $grupo) {

			$consulta = 'SELECT t.id, t.definicion, t.grupo_pregs_id, 
								t.idioma_id, i.nombre as idioma  
						FROM ws_contenido_traduc t, ws_idiomas i
						where i.id=t.idioma_id and t.grupo_pregs_id = :grupo_pregs_id and t.deleted_at is null';

			$contens_traduc = \DB::select(\DB::raw($consulta), array(':grupo_pregs_id' => $grupo->id) );



			foreach ($contens_traduc as $key => $content) {
				
				$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.duracion, 
								t.tipo_pregunta, t.puntos, t.aleatorias  
						FROM ws_preguntas_agrupadas t
						where t.contenido_id = :contenido_id and t.deleted_at is null';

				$pregs_agrup = \DB::select(\DB::raw($consulta), array(':contenido_id' => $content->id) );

				// Traeremos las opciones de cada pregunta.
				Opcion_agrupada::opciones($pregs_agrup, $exa_resp_id);

				$content->preguntas_agrupadas = $pregs_agrup;



			}


			$grupo->contenidos_traducidos = $contens_traduc;


			array_push($preg_king, $grupo);


		}

		return $grupos_preg;
	}


	public static function traducciones_single(&$grupo)
	{
		
		$consulta = 'SELECT t.id, t.definicion, t.grupo_pregs_id, 
							t.idioma_id, i.nombre as idioma  
					FROM ws_contenido_traduc t, ws_idiomas i
					where i.id=t.idioma_id and t.grupo_pregs_id =:grupo_pregs_id and t.deleted_at is null';

		$contents_trads = \DB::select(\DB::raw($consulta), array(':grupo_pregs_id' => $grupo->id) );

		
		foreach ($contents_trads as $key => $content) {
				
			$consulta = 'SELECT t.id, t.enunciado, t.ayuda, t.duracion, 
							t.tipo_pregunta, t.puntos, t.aleatorias  
					FROM ws_preguntas_agrupadas t
					where t.contenido_id = :contenido_id and t.deleted_at is null';

			$pregs_agrup = \DB::select(\DB::raw($consulta), array(':contenido_id' => $content->id) );

			// Traeremos las opciones de cada pregunta.
			Opcion_agrupada::opciones($pregs_agrupd);

			$content->preguntas_agrupadas = $pregs_agrup;

		}

		$grupo->contenidos_traducidos = $contents_trads;



		return $grupo;
	}


}
