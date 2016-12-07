<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;



use App\Models\Pregunta_agrupada;
use App\Models\Grupo_pregunta;
use App\Models\Contenido_traduc;
use App\Models\Evento;
use App\Models\User;
use App\Models\Opcion_agrupada;




class Preguntas_agrupadasController extends Controller {

	

	public function anyIndex(Request $request)
	{	
		/* Probando los Providers personales
		(\Preg $preguntaServ)
		$respon = $preguntaServ::haciendo();
		return $respon;

		\Preg::haciendo();
		$pre = new PreguntasService();
		return $pre->haciendo();
		*/

		$user = User::fromToken();
		$evento_id = $user->evento_selected_id;

		$categoria_id = $request->input('categoria_id');
		$examen_id = $request->input('examen_id', null);

		$pres_agrup = Grupo_pregunta::where('categoria_id', '=', $categoria_id)->get();

		Pregunta_traduc::traducciones($pres_agrup); // Paso por referencia la nivel_king

		return $pres_agrup;
		
	}

	
	public function postStore(Request $request)
	{
		$user = User::fromToken();

		$contenido_id = $request->input('contenido_id');

		$preg_agrup 				= new Pregunta_agrupada;
		$preg_agrup->enunciado 		= 'Pregunta agrupada';
		$preg_agrup->added_by 		= $user->id;
		$preg_agrup->tipo_pregunta 	= 'Test';
		$preg_agrup->contenido_id 	= $contenido_id;

		$preg_agrup->save();

		$preg_agrup->opciones = [];

		return $preg_agrup;
	}





	public function putUpdate(Request $request)
	{
		$user = User::fromToken();

		$preg_agrup 				= Pregunta_agrupada::findOrFail($request->input('id'));
		$preg_agrup->enunciado 		= $request->input('enunciado');
		$preg_agrup->ayuda 			= $request->input('ayuda', null);
		$preg_agrup->puntos 		= $request->input('puntos', 1);
		$preg_agrup->aleatorias 	= $request->input('aleatorias');
		$preg_agrup->duracion 		= $request->input('duracion');
		$preg_agrup->tipo_pregunta 	= $request->input('tipo_pregunta');
		
		$preg_agrup->save();

		
		foreach ($request->input('opciones') as $key => $opcion) {


			if (array_key_exists('nueva', $opcion)) {

				if ($opcion['nueva'] == true) {
					// No es una opción a agregar, es el botón.
				}else{
					$opcion_t 				= Opcion_agrupada::findOrFail($opcion['id']);
					$opcion_t->definicion 	= $opcion['definicion'];
					$opcion_t->is_correct 	= $opcion['is_correct'];
					$opcion_t->save();
				}
				
			}else{
				$opcion_t 				= Opcion_agrupada::findOrFail($opcion['id']);
				$opcion_t->definicion 	= $opcion['definicion'];
				$opcion_t->is_correct 	= $opcion['is_correct'];
				$opcion_t->save();
			}

		}


		return $preg_agrup;
	}

	public function deleteDestroy($id)
	{
		$user = User::fromToken();
		
		$preg_agrup = Pregunta_agrupada::findOrFail($id);
		$preg_agrup->delete();

		return $preg_agrup;
	}



}



