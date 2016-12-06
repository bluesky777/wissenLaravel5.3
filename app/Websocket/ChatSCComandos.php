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


class ChatSCComandos {

	public function sc_show_participantes($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ( isset($client->datos->usuario) ) {
				if (count($client->datos->usuario->roles) > 0) {
					if($client->datos->usuario->roles[0]->name == "Pantalla"){
						$client->send(json_encode(["comando" => "sc_show_participantes", "categorias_traduc" => $msg->categorias_traduc ]));
					}
				}
			}
            
        }
		
		return true;
	}


	public function sc_show_question($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ( isset($client->datos->usuario) ) {
				if (count($client->datos->usuario->roles) > 0) {
					if($client->datos->usuario->roles[0]->name == "Pantalla"){
						$client->send(json_encode(["comando"=>"sc_show_question", "pregunta"=>$msg->pregunta, "no_question"=>$msg->no_question ]));
					}
				}
			}
		}
		return true;
	}


	public function sc_reveal_answer($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ( isset($client->datos->usuario) ) {
				if (count($client->datos->usuario->roles) > 0) {
					if($client->datos->usuario->roles[0]->name == "Pantalla"){
						$client->send(json_encode(["comando"=>"sc_reveal_answer"]));
					}
				}
			}
		}
		return true;
	}


	public function sc_show_logo_entidad_partici($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ( isset($client->datos->usuario) ) {
				if (count($client->datos->usuario->roles) > 0) {
					if($client->datos->usuario->roles[0]->name == "Pantalla"){
						$client->send(json_encode(["comando"=>"sc_show_logo_entidad_partici", "valor"=>$msg->valor ]));
					}
				}
			}
		}
		return true;
	}

	public function sc_show_puntaje_particip($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ( isset($client->datos->usuario) ) {
				if (count($client->datos->usuario->roles) > 0) {
					if($client->datos->usuario->roles[0]->name == "Pantalla"){
						$client->send(json_encode(["comando"=>"sc_show_puntaje_particip", "cliente"=>$msg->cliente ]));
					}
				}
			}
		}
		return true;
	}

	public function sc_show_puntaje_examen($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			if ( isset($client->datos->usuario) ) {
				if (count($client->datos->usuario->roles) > 0) {
					if($client->datos->usuario->roles[0]->name == "Pantalla"){
						$client->send(json_encode(["comando"=>"sc_show_puntaje_examen", "examen"=>$msg->examen ]));
					}
				}
			}
			
		}
		return true;
	}

	public function sc_answered($from, $clients, $msg)
	{

		foreach ($clients as $client) {
			if ($client->resourceId == $from->resourceId) {
				// 'correct', 'incorrect' y 'waiting'
				$client->datos->answered 	= $msg->valor;
				$client->datos->respondidas++;
				$client->datos->tiempo 		= $client->datos->tiempo + $msg->tiempo;
				if ($msg->valor == 'correct') {
					$client->datos->correctas++;
				}
				$particip = $client->datos;
			}

		}
		
		foreach ($clients as $client) {

			if ( isset($client->datos->usuario) ) {
				
				if (count($client->datos->usuario->roles) > 0) {
					$name = $client->datos->usuario->roles[0]->name;
					if($name == "Pantalla" || $name == "Admin"){
						$client->send(json_encode(["comando"=>"sc_answered", "resourceId"=>$from->resourceId, "cliente"=>$particip ]));
					}
				}
			}
		}
		return true;
	}


	public function next_question($from, $clients, $msg)
	{
		Chat::$info_evento->preg_actual = Chat::$info_evento->preg_actual + 1;
		
		foreach ($clients as $client) {
			$client->datos->answered = 'waiting';
			if($client->datos->registrado && $client->resourceId != $from->resourceId){
				$client->send(json_encode(["comando"=>"next_question" ]));
			}
		}
		return true;
	}

	public function next_question_cliente($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			$client->datos->answered = 'waiting';
			if($client->datos->registrado && $client->resourceId == $msg->resourceId){
				$client->send(json_encode(["comando"=>"next_question" ]));
			}
		}
		return true;
	}

	public function goto_question_no($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			$client->datos->answered = 'waiting';
			if($client->datos->registrado && $client->resourceId != $from->resourceId){
				$client->send(json_encode(["comando"=>"goto_question_no", "numero"=>$msg->numero ]));
			}
		}
		return true;
	}

	public function goto_question_no_clt($from, $clients, $msg)
	{
		foreach ($clients as $client) {
			$client->datos->answered = 'waiting';
			if($client->datos->registrado && $client->resourceId == $msg->resourceId){
				$client->send(json_encode(["comando"=>"goto_question_no", "numero"=>$msg->numero ]));
			}
		}
		return true;
	}



		
}