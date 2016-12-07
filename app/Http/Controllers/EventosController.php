<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\User;
use App\Models\Evento;
use App\Models\Idioma_registrado;


use Illuminate\Http\Request;

class EventosController extends Controller {


	public function anyIndex()
	{
		$events = Evento::todos();

		$total = count($events);

		if ($total > 0) {

			for ($i=0; $i < $total; $i++) { 
				
				$events[$i]->idiomas = Evento::idiomas_all($events[$i]->id);


			}
			
		}


		return $events;
	}



	public function putSetEventoActual(Request $request)
	{	
		$user = User::fromToken();

		# Quitamos los que digan actuales
		$consulta = 'UPDATE ws_eventos set actual=false';
		\DB::select(\DB::raw($consulta));


		# Establecemos el actual
		$consulta = 'UPDATE ws_eventos set actual=true where id =:evento_id and deleted_at is null';
		\DB::select(\DB::raw($consulta), array(':evento_id' => $request->id) );

		return "Evento actual establecido";
	}

	

	public function putSetUserEvent(Request $request)
	{	

		$user = User::fromToken();

		# Establecemos el actual del usuario
		$consulta = 'UPDATE users set evento_selected_id=:evento_id where id =:user_id and deleted_at is null';
		\DB::select(\DB::raw($consulta), array(':evento_id' => $request->evento_id, ':user_id' => $user->id) );


		return "Evento del usuario establecido";
	}

	
	public function postStore(Request $request)
	{
		$event = new Evento;

		$event->nombre 					= $request->input('nombre', 'Evento default');
		$event->alias 					= $request->input('alias', NULL);
		$event->descripcion 			= $request->input('descripcion', NULL);
		#$event->examen_actual_id 		= $request->input('examen_actual_id', NULL);
		$event->idioma_principal_id 	= $request->input('idioma_principal_id', 1);
		$event->es_idioma_unico 		= $request->input('es_idioma_unico', false);
		$event->enable_public_chat 		= $request->input('enable_public_chat', false);
		$event->enable_private_chat 	= $request->input('enable_private_chat', false);
		$event->with_pay 				= $request->input('with_pay', false);
		$event->actual 					= $request->input('actual', false);

		if ($event->with_pay){
			
			$event->precio1 = $request->input('precio1', 0);
			$event->precio2 = $request->input('precio2', 0);
			$event->precio3 = $request->input('precio3', 0);
			$event->precio4 = $request->input('precio4', 0);
			$event->precio5 = $request->input('precio5', 0);
			$event->precio6 = $request->input('precio6', 0);

		}

		$event->save();


		// Inscribimos los otros idiomas si no es idioma único
		if (!$event->es_idioma_unico){

			$idiomas_extras = $request->input('idiomas_extras');

			if ( is_array( $idiomas_extras )){

				foreach ($idiomas_extras as $key => $idioma_extra) {
					$idioma_reg = new Idioma_registrado;

					$idioma_reg->idioma_id 	= $idioma_extra['id'];
					$idioma_reg->evento_id 	= $event->id;
					$idioma_reg->save();
				}
			}
		}

		return $event;
	}

	
	public function getShow($id)
	{
		return Evento::all();
	}

	
	public function putUpdate(Request $request)
	{
		$event = Evento::findOrFail($request->id);

		$event->nombre 					= $request->input('nombre', $event->nombre);
		$event->alias 					= $request->input('alias', $event->alias);
		$event->descripcion 			= $request->input('descripcion', $event->descripcion);
		#$event->examen_actual_id 		= $request->input('examen_actual_id', $event->examen_actual_id);
		$event->idioma_principal_id 	= $request->input('idioma_principal_id', $event->idioma_principal_id);
		$event->es_idioma_unico 		= $request->input('es_idioma_unico', $event->es_idioma_unico);
		$event->enable_public_chat 		= $request->input('enable_public_chat', $event->enable_public_chat);
		$event->enable_private_chat 	= $request->input('enable_private_chat', $event->enable_private_chat);
		$event->with_pay 				= $request->input('with_pay', $event->with_pay);

		if ($event->with_pay){
			
			$event->precio1 = $request->input('precio1', $event->precio1);
			$event->precio2 = $request->input('precio2', $event->precio2);
			$event->precio3 = $request->input('precio3', $event->precio3);
			$event->precio4 = $request->input('precio4', $event->precio4);
			$event->precio5 = $request->input('precio5', $event->precio5);
			$event->precio6 = $request->input('precio6', $event->precio6);

		}

		$event->save();


		
		if ($event->es_idioma_unico){

			// Si es idioma único nos aseguramos de desinscribir los extras
			Idioma_registrado::where('evento_id', '=', $event->id)->delete();

		}


		


		return $event;
	}


	public function deleteDestroy($id)
	{
		$evento = Evento::find($id);
		$evento->delete();

		return $evento;
	}
	
	public function deleteForcedelete($id)
	{
		$evento = Evento::onlyTrashed()->findOrFail($id);
		
		if ($evento) {
			$evento->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	
	}

	public function putRestore($id)
	{
		$evento = Evento::onlyTrashed()->findOrFail($id);

		if ($evento) {
			$evento->restore();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_eventos
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
