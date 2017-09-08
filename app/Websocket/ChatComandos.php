<?php namespace App\Websocket;


use Ratchet\ConnectionInterface;



use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\Pid;
use App\Models\Qrcode;
use App\Models\User;
use App\Models\Inscripcion;
use App\Models\Evento;


use \StdClass;
use DB;
use \Request;


class ChatComandos {

	public function conectar($from, $clients, $msg)
	{
		if ( isset($msg->qr) ) {
			$parametro      = ["resourceId" => $from->resourceId];
			$qr             = Qrcode::where('codigo', $msg->qr)->first();
			$qr->parametro  = json_encode( $parametro );
			$qr->save();
		}
		if (!isset($from->datos)) {
			$from->datos = new StdClass;
		}
		if ($msg->nombre_punto) {
			$from->datos->nombre_punto = $msg->nombre_punto;
		}else{
			$from->datos->nombre_punto = Request::ip();
		}

		$from->datos->respondidas 		= 0;
		$from->datos->correctas 		= 0;
		$from->datos->tiempo 			= 0;
		

		foreach ($clients as $client) {

			if ($from !== $client) {
				$client->send(json_encode(["comando" => "conectado", "cliente" => (array)$from->datos]));
			}else{
				$client->send(json_encode(["comando" => "conectado", "cliente" => (array)$from->datos, "yo_resource_id" => $from->datos->resourceId]));
			}
		}
		
		return true;
	}



	public function registrar($from, $clients, $msg)
	{
		if (!isset($from->datos->registrado)) {
			$datos = new StdClass;
			$datos->registrado      = false;
			$datos->resourceId      = $from->resourceId;
			$datos->remoteAddress   = $from->remoteAddress;
			$datos->categsel   		= 0;
			$datos->respondidas 	= 0;
			$datos->correctas 		= 0;
			$datos->tiempo 			= 0;
			$from->datos = $datos;
		}
		if (!$from->datos->registrado) {
			Pid::nuevo( 'Registrando: ' . $from->datos->respondidas);
			if (isset($msg->usuario->eventos)) {
				unset($msg->usuario->eventos);
			}

			$from->datos->usuario = $msg->usuario;
			$from->datos->registrado = true;
		
			if ($msg->nombre_punto) {
				$from->datos->nombre_punto = $msg->nombre_punto;
			}else{
				$from->datos->nombre_punto = Request::ip();
			}
		
			$evento_id = Evento::actual()->id;

			$categs = Inscripcion::todas($from->datos->usuario->id, $evento_id);
			$from->datos->usuario->inscripciones = $categs;
			
			if (count($categs) > 0) {
				$from->datos->categsel = $categs[0]->categoria_id;
			}
			

			$usuT = User::find($from->datos->usuario->id);
			$roles = $usuT->roles()->get();
			$from->datos->usuario->roles = $roles;


				

			Pid::nuevo( json_encode($msg->usuario) );
			foreach ($clients as $client) {
				if ($from !== $client) {
					$client->send(json_encode(["comando" => "registrado", "clt" => $from->datos]));
				}else{
					$client->send(json_encode(["comando" => "validado", "yo" => $from->datos, "info_evento" => Chat::$info_evento]));
				}
			}
		}else{
			return true;
		}
		
	}


	public function unregister($from, $clients, $msg)
	{
		Pid::nuevo( 'unregister: ' . json_encode((array)$from) );
		if (isset($from->datos)) {
			$nombre_punto = $from->datos->nombre_punto;
			unset($from->datos);
		}
		$datos = new StdClass;
		$datos->registrado      = false;
		$datos->resourceId      = $from->resourceId;
		$datos->remoteAddress   = $from->remoteAddress;
		$datos->nombre_punto   	= $nombre_punto;
		$datos->categsel   		= 0;
		$datos->respondidas 	= 0;
		$datos->correctas 		= 0;
		$datos->tiempo 			= 0;
		$from->datos = $datos;

		foreach ($clients as $client) {
			if ($client->datos->registrado) {
				$client->send(json_encode(["comando"=>"unregistered", "client" => (array)$from->datos] ));
			}
		}
	}



	public function get_clts($from, $clients, $msg)
	{
		$all_clts = [];
		foreach ($clients as $client) {
			array_push($all_clts, $client->datos);

			/*
			// Código de prueba:
			if ($client->datos->usuario->roles[0]->id == 5) {
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
				array_push($all_clts, $client->datos);
			}
			// Termina código para pruebas "mock"
			*/
			
		}
		foreach ($clients as $client) {
			$client->send(json_encode(["comando"=>"take_clts", "clts"=>$all_clts, "info_evento"=>Chat::$info_evento ]));
		}
		return true;
	}



	public function got_qr($from, $clients, $msg)
	{
		$qr = Qrcode::where('codigo', $msg->qr->codigo)->first();
				
		if($qr){

			switch ($qr->comando) {
				case 'let_in':
					Pid::nuevo( 'Entra en let_in' );
					if (isset( $msg->from_token)) {
						Pid::nuevo( $msg->from_token );
						$user = User::fromToken($msg->from_token);
					}else{
						$user = User::fromToken();
					}
					
					Pid::nuevo( json_encode($user) );


					if ( $user->hasRole('Admin') || $user->hasRole('Profesor') || $user->hasRole('Asesor') ||  $user->is_superuser) {
						Pid::nuevo( 'Entra con permisos' );
						$qr->reconocido = true;

						$qr->parametro = json_decode($qr->parametro);

						if ($qr->parametro != null) {
							foreach ($clients as $client) {
								if($client->resourceId == $qr->parametro->resourceId || $client->resourceId == (int)$qr->parametro->resourceId){
									if (isset( $msg->usuario_id) ){
										$user = User::find($msg->usuario_id);
										$token = JWTAuth::fromUser($user);
										$client->send(json_encode(["comando"=>"got_your_qr", "codigo"=>$qr->codigo, "token"=>$token ]));
									}else{
										Pid::nuevo( 'No está $msg->usuario_id' );
										$evento_id = $user->evento_selected_id;

										$consulta = 'SELECT u.id, u.nombres, u.apellidos, u.sexo, u.username, u.email, u.is_superuser, 
														u.cell, u.edad, u.idioma_main_id, u.evento_selected_id, 
														IFNULL(e.nivel_id, "") as nivel_id, e.pagado, e.pazysalvo, u.entidad_id, 
														u.imagen_id, IFNULL(CONCAT("perfil/", i.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
														en.nombre as nombre_entidad, en.lider_id, en.lider_nombre, en.logo_id, en.alias  
													FROM users u 
													inner join ws_user_event e on e.user_id = u.id and e.evento_id = :evento_id 
													left join images i on i.id=u.imagen_id and i.deleted_at is null 
													left join ws_entidades en on en.id=u.entidad_id and en.deleted_at is null 
													where u.deleted_at is null';

										$usuarios = DB::select($consulta, array(':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id) );
										$token_t = (string)$user->token;
										Pid::nuevo("$user->token->getToken()");
										
										
										Pid::nuevo(gettype($token_t) .' - ' . $token_t );
										$client->send(json_encode(["comando"=>"got_your_qr", "codigo"=>$qr->codigo, "seleccionar"=>true, "usuarios"=>$usuarios, "token"=> $token_t ]));
									}

								}
							}
							$qr->delete();
							
						}else{
							$from->send(json_encode(["comando"=>"qr_no_param"]));
						}
					}else{
						Pid::nuevo( 'NO Entra, no permisos' );
					}
					break;
				
				default:
					# code...
					break;
			}
		}
		return true;
	}


	public function correspondencia($from, $clients, $msg)
	{
		$mensaje = $msg->mensaje;
		$datos_from = [];

		foreach ($clients as $client) {
			if ($client->datos->resourceId == $from->resourceId) {
				$datos_from = json_encode($client->datos);
				break;
			}
		}

		$msg->mensaje = ["from"=>$datos_from, "texto"=>$mensaje];

		foreach ($clients as $client) {
			$client->send(json_encode(["comando"=>"correspondencia", "mensaje"=>$msg->mensaje ]));
		}
		return true;
	}


	public function cerrar_sesion($from, $clients, $msg)
	{
		$cliente = [];
		if ($msg->resourceId) {
			$resourceId = $msg->resourceId;

			foreach ($clients as $client) {
				if ($client->datos->resourceId == $resourceId) {
					$cliente = $client;
					break;
				}
			}
			Pid::nuevo( 'Verifica 11' );

		}else{
			$cliente = $from;
		}

		Pid::nuevo( 'Verifica 12' );
		
		$cliente->datos->registrado = false;
		unset($from->datos->usuario);
		Pid::nuevo( 'Verifica 13' );

		foreach ($clients as $client) {
			$client->send(json_encode(["comando"=>"sesion_closed", "clt"=>$cliente->datos ]));
		}
		Pid::nuevo( 'Verifica 14' );
		return true;
	}



	public function guardar_nombre_punto($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ($client->resourceId == $msg->resourceId) {
				$client->datos->nombre_punto = $msg->nombre;
			}
			$client->send(json_encode(["comando"=>"nombre_punto_cambiado", "resourceId"=>$msg->resourceId, "nombre"=>$msg->nombre ]));
		}

		return true;
	}


	public function get_usuarios($from, $clients, $msg)
	{
		if (isset( $msg->from_token)) {
			Pid::nuevo( $msg->from_token );
			$user = User::fromToken($msg->from_token);
		}else{
			$user = User::fromToken();
		}

		$evento_id = $user->evento_selected_id;

		$consulta = 'SELECT u.id, u.nombres, u.apellidos, u.sexo, u.username, u.email, u.is_superuser, 
						u.cell, u.edad, u.idioma_main_id, u.evento_selected_id, 
						IFNULL(e.nivel_id, "") as nivel_id, e.pagado, e.pazysalvo, u.entidad_id, 
						u.imagen_id, IFNULL(CONCAT("perfil/", i.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
						en.nombre as nombre_entidad, en.lider_id, en.lider_nombre, en.logo_id, en.alias  
					FROM users u 
					inner join ws_user_event e on e.user_id = u.id and e.evento_id = :evento_id 
					left join images i on i.id=u.imagen_id and i.deleted_at is null 
					left join ws_entidades en on en.id=u.entidad_id and en.deleted_at is null 
					where u.deleted_at is null';

		$usuarios = DB::select($consulta, [':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id] );

		$from->send(json_encode(["comando"=>"take_usuarios", "usuarios"=>$usuarios ]));

		return true;
	}


	public function let_him_enter($from, $clients, $msg)
	{
		if (isset( $msg->from_token)) {
			Pid::nuevo( $msg->from_token );
			$user = User::fromToken($msg->from_token);
		}else{
			$user = User::fromToken();
		}

		$evento_id = $user->evento_selected_id;

		if ( $user->hasRole('Admin') || $user->hasRole('Profesor') || $user->hasRole('Asesor') ||  $user->is_superuser) {
			Pid::nuevo( 'Entra con permisos' );

			if (isset( $msg->usuario_id) ){
				$user = User::find($msg->usuario_id);
				$token = JWTAuth::fromUser($user);
				foreach ($clients as $client) {
					if($client->resourceId == $msg->resourceId ){
						$client->send(json_encode(["comando"=>"enter", "token"=>$token ]));
					}
				}
			}
		}
		return true;
	}


	public function change_a_categ_selected($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if($client->resourceId == $msg->resourceId ){
				$client->datos->categsel = $msg->categsel;
				$client->send(json_encode(["comando"=>"change_the_categ_selected", "categsel"=>$msg->categsel ]));
			}elseif(count($client->datos->usuario->roles) > 0){
				if($client->datos->usuario->roles[0]->name == "Pantalla" || $client->datos->usuario->roles[0]->name == "Admin"){
					$client->send(json_encode(["comando"=>"change_a_categ_selected", "categsel"=>$msg->categsel, "resourceId"=>$msg->resourceId ]));
				}
			}
		}
		return true;
	}


	public function warn_my_categ_selected($from, $clients, $msg)
	{
		$from->datos->categsel = $msg->categsel;
		foreach ($clients as $client) {
			if($client->resourceId != $from->resourceId ){
				$client->send(json_encode(["comando"=>"a_categ_selected_change", "categsel"=>$msg->categsel, "resourceId"=>$from->resourceId ]));
			}
		}
		return true;
	}


	public function empezar_examen($from, $clients, $msg)
	{
		Chat::$info_evento->examen_iniciado 	= true;
		Chat::$info_evento->preg_actual 		= 1;
		
		foreach ($clients as $client) {
			if($client->resourceId != $from->resourceId ){
				$client->send(json_encode(["comando"=>"empezar_examen"]));
			}
		}
		return true;
	}


	public function empezar_examen_cliente($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if($client->resourceId == $msg->resourceId ){
				$client->send(json_encode(["comando"=>"empezar_examen"]));
			}
		}
		return true;
	}


	public function liberar_hasta_pregunta($from, $clients, $msg)
	{
		Chat::$info_evento->free_till_question 	= $msg->numero;
		Chat::$info_evento->preg_actual 		= $msg->numero;

		foreach ($clients as $client) {
			if($client->resourceId != $from->resourceId ){
				$client->send(json_encode(["comando"=>"set_free_till_question", "free_till_question"=>$msg->numero]));
			}
		}
		return true;
	}



		
}