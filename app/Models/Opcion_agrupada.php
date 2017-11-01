<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Respuesta;

class Opcion_agrupada extends Model {


	protected $table = 'ws_opciones_agrupadas';

	protected $dates = ['created_at', 'updated_at'];



	public static function opciones(&$pregs_agrup, $exa_resp_id=false)
	{
		
		$cant_pregs = count($pregs_agrup);

		for($i=0; $i < $cant_pregs; $i++){

			$consulta = 'SELECT o.id, o.definicion, o.orden, o.preg_agrupada_id, o.is_correct 
					FROM ws_opciones_agrupadas o
					where o.preg_agrupada_id =:preg_agrupada_id';

			$preg_trads = \DB::select(\DB::raw($consulta), array(':preg_agrupada_id' => $pregs_agrup[$i]->id) );

			$pregs_agrup[$i]->opciones = $preg_trads;


			if ($exa_resp_id) {

				$cant_opc = count($pregs_agrup[$i]->opciones);

				for ($j=0; $j < $cant_opc; $j++) { 

					$respuesta = Respuesta::where('opcion_id', $pregs_agrup[$i]->opciones[$j]->id)
									->where('examen_respuesta_id', $exa_resp_id)
									->whereNull('pregunta_king_id')
									->first();

					if ($respuesta) {
						$pregs_agrup[$i]->opciones[$j]->elegida = true;
						$pregs_agrup[$i]->opciones[$j]->respondida = true;
					}
				}
				
			}

		}
		

		return $pregs_agrup;
	}


}
