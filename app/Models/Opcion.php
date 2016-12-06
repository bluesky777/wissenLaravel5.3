<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Respuesta;



class Opcion extends Model {


	protected $table = 'ws_opciones';

	protected $dates = ['created_at', 'updated_at'];




	public static function opciones(&$pregs_trads, $exa_resp_id=false)
	{
		
		$cant_dis = count($pregs_trads);

		for($i=0; $i < $cant_dis; $i++){

			$consulta = 'SELECT o.id, o.definicion, o.orden, o.pregunta_traduc_id, o.is_correct 
					FROM ws_opciones o
					where o.pregunta_traduc_id =:pregunta_traduc_id';

			$preg_trads = \DB::select($consulta, array(':pregunta_traduc_id' => $pregs_trads[$i]->id) );

			//$pregs_trads[$i]->opciones = [];
			$pregs_trads[$i]->opciones = $preg_trads;

			if ($exa_resp_id) {

				$cant_opc = count($pregs_trads[$i]->opciones);

				for ($j=0; $j < $cant_opc; $j++) { 

					$respuesta = Respuesta::where('opcion_id', $pregs_trads[$i]->opciones[$j]->id)
									->where('examen_respuesta_id', $exa_resp_id)
									->whereNull('pregunta_agrupada_id')
									->first();

					if ($respuesta) {
						$pregs_trads[$i]->opciones[$j]->elegida = true;
						$pregs_trads[$i]->opciones[$j]->respondida = true;
					}
				}
				
			}

		}
		

		return $pregs_trads;
	}


	public static function traducciones_single(&$preg_trad)
	{
		
		$consulta = 'SELECT o.id, o.definicion, o.orden, o.pregunta_traduc_id, o.is_correct 
					FROM ws_opcion o
					where o.pregunta_traduc_id =:pregunta_traduc_id';

		$pregs_trads = \DB::select(\DB::raw($consulta), array(':pregunta_traduc_id' => $preg_trad->id) );

		$preg_trad->preguntas_traducidas = $pregs_trads;


		return $preg_trad;
	}



}
