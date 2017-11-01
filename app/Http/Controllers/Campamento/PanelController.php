<?php namespace App\Http\Controllers\Campamento;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Campamento\Usuario;
use App\Models\Campamento\CaMensaje;



use Request;
use DB;


class PanelController extends Controller {

	
	public function putDatos()
	{
		$grados = [];
		$mi_id = Request::input('mi_id');

		
		$consulta = 'SELECT * FROM  ca_usuarios u where u.grado="Sexto" and u.id<>?';
		$datos =[ "nombre"=>'Sexto', "usuarios"=>DB::select($consulta, [$mi_id]) ];
		array_push($grados, $datos);
		
		$consulta = 'SELECT * FROM  ca_usuarios u where u.grado="Séptimo" and u.id<>?';
		$datos =[ "nombre"=>'Séptimo', "usuarios"=>DB::select($consulta, [$mi_id]) ];
		array_push($grados, $datos);

		$consulta = 'SELECT * FROM  ca_usuarios u where u.grado="Octavo" and u.id<>?';
		$datos =[ "nombre"=>'Octavo', "usuarios"=>DB::select($consulta, [$mi_id]) ];
		array_push($grados, $datos);

		$consulta = 'SELECT * FROM  ca_usuarios u where u.grado="Noveno" and u.id<>?';
		$datos =[ "nombre"=>'Noveno', "usuarios"=>DB::select($consulta, [$mi_id]) ];
		array_push($grados, $datos);

		$consulta = 'SELECT * FROM  ca_usuarios u where u.grado="Décimo" and u.id<>?';
		$datos =[ "nombre"=>'Décimo', "usuarios"=>DB::select($consulta, [$mi_id]) ];
		array_push($grados, $datos);

		$consulta = 'SELECT * FROM  ca_usuarios u where u.grado="Once" and u.id<>?';
		$datos =[ "nombre"=>'Once', "usuarios"=>DB::select($consulta, [$mi_id]) ];
		array_push($grados, $datos);

		$consulta = 'SELECT * FROM  ca_mensajes m inner join ca_usuarios u ON u.id=m.emisor_id WHERE m.receptor_id is null order by m.id desc limit 20';
		$mensajes_grupales = DB::select($consulta);


		return ["grados" => $grados, "mensajes_grupales" => $mensajes_grupales];
	}
	


	public function putMisMensajesNuevos()
	{
		$mi_id = Request::input('mi_id');


		$consulta = 'SELECT * FROM  ca_mensajes m where m.receptor_id=? and m.leido=false ';
		$mensajes = DB::select($consulta, [$mi_id]);
		
		if (count($mensajes) > 0) {
			$consulta = 'UPDATE ca_mensajes SET leido=true WHERE receptor_id=? ';
			DB::update($consulta, [$mi_id]);
		}

		$consulta = 'SELECT * FROM  ca_mensajes m inner join ca_usuarios u ON u.id=m.emisor_id WHERE m.receptor_id is null order by m.id desc limit 30';
		$mensajes_grupales = DB::select($consulta);
		

		return ["mensajes" => $mensajes, "mensajes_grupales" => $mensajes_grupales ];


	}
	

	public function putMensajesAnteriores()
	{
		$mi_id = Request::input('mi_id');
		$el_id = Request::input('el_id');


		$consulta = 'SELECT * FROM  ca_mensajes m where (m.receptor_id=? and m.emisor_id=?) or (m.receptor_id=? and m.emisor_id=?) order by id limit 5';
		$mensajes = DB::select($consulta, [$el_id, $mi_id, $mi_id, $el_id]);
		
		
		for ($i=0; $i < count($mensajes); $i++) { 
			if ($mensajes[$i]->emisor_id == $mi_id) {
				$mensajes[$i]->mio = true;
			}
		}

		return $mensajes;


	}
	


	public function putEnviarMensaje()
	{
		$mi_id = Request::input('mi_id');

		$mens = new CaMensaje;
		$mens->leido		= false;
		$mens->receptor_id	= Request::input('receptor_id');
		$mens->emisor_id	= $mi_id;
		$mens->mensaje		= Request::input('mensaje');
		$mens->save();
		

		return $mens;


	}
	
	
	public function putEnviarMensajeGrupal()
	{
		$mi_id = Request::input('mi_id');

		$mens = new CaMensaje;
		$mens->leido		= false;
		$mens->emisor_id	= $mi_id;
		$mens->mensaje		= Request::input('mensaje');
		$mens->save();

		return $mens;


	}
	
	
	public function putPuntajes()
	{
		
		$consulta = 'SELECT * FROM  ca_actividades ';
		$actividades = DB::select($consulta);

		$consulta = 'SELECT * FROM  ca_penalizaciones ';
		$penalizaciones = DB::select($consulta);
		
		

		return ["actividades"=>$actividades, "penalizaciones"=>$penalizaciones];


	}
	
	
	
}
