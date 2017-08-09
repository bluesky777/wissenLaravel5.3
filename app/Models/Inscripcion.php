<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Examen_respuesta;
use DB;


class Inscripcion extends Model {

	use SoftDeletes;

	protected $table = 'ws_inscripciones';

	protected $dates = ['created_at', 'updated_at'];



	public static function todas($user_id, $evento_id)
	{
		$consulta = 'SELECT i.id, i.categoria_id, i.allowed_to_answer
						FROM ws_inscripciones i 
						inner join ws_categorias_king c on c.deleted_at is null and c.id=i.categoria_id and c.evento_id = :evento_id
						where i.user_id=:user_id and i.deleted_at is null ';

		$inscripciones = DB::select($consulta, [':evento_id' => $evento_id, ':user_id' => $user_id] );

		foreach ($inscripciones as $key => $inscripcion) {

			$examenes = Examen_respuesta::where('inscripcion_id', $inscripcion->id)->get();
			$inscripcion->examenes = $examenes;

		}

		return $inscripciones;
	}


	public static function inscribir($user_id, $categoria_id, $yo_id=null)
	{
		// Traer la inscripción si existe, esté eliminada o activa.
		$inscripcion = Inscripcion::one_uncare($user_id, $categoria_id);

		if (count($inscripcion) > 0 ) {

			// Si no es null, lo pondrémos null para activarlo
			if (is_null($inscripcion[0]->deleted_at)) {
				// Ya está inscripto y activo, no hay problema supuestamente
			}else{
				
				$consulta = 'UPDATE ws_inscripciones i SET i.deleted_at = NULL where i.id=:inscrip_id';
				$inscripcion = \DB::select($consulta, array(':inscrip_id' => $inscripcion[0]->id) );

			}
		// Si no encontramos ni uno, debemos crearlo
		}else{
			// falta la fecha creación y modificación
			$consulta = 'INSERT INTO ws_inscripciones(user_id, categoria_id, signed_by) VALUES(:user_id, :categoria_id, :yo_id)';
			$inscripcion = \DB::select($consulta, [':user_id' => $user_id, ':categoria_id' => $categoria_id, ":yo_id" => $yo_id] );
		
		}


		$inscripcion = Inscripcion::one($user_id, $categoria_id);
		return $inscripcion;
	}

	public static function desinscribir($user_id, $categoria_id)
	{
		// Traer la inscripción si existe, esté eliminada o activa.
		$inscripcion = Inscripcion::where('user_id', '=', $user_id)
									->where('categoria_id', '=', $categoria_id)->first();

		if ($inscripcion) {
			$inscripcion->delete();
		}

		return $inscripcion;
	}

	public static function one_uncare($user_id, $categoria_id)
	{
		// Traer la inscripción si existe, esté eliminada o activa.
		$consulta = 'SELECT i.id, i.categoria_id, i.allowed_to_answer, i.deleted_at
						FROM ws_inscripciones i 
						where i.user_id=:user_id and i.categoria_id=:categoria_id';

		$inscripcion = \DB::select(\DB::raw($consulta), array(':user_id' => $user_id, ':categoria_id' => $categoria_id) );
		return $inscripcion;
	}

	public static function one($user_id, $categoria_id)
	{
		// Traer la inscripción activa.
		$consulta = 'SELECT i.id, i.categoria_id, i.user_id, i.allowed_to_answer, i.deleted_at
						FROM ws_inscripciones i 
						where i.user_id=:user_id and i.categoria_id=:categoria_id and i.deleted_at is null';

		$inscripcion = \DB::select($consulta, [':user_id' => $user_id, ':categoria_id' => $categoria_id] );
		if (count($inscripcion)>0){
			return $inscripcion[0];
		}else{
			return;
		}
		
	}

}
