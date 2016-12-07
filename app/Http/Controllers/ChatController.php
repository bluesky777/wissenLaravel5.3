<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Websocket\Chat;


use App\Models\Pid;
use App\Models\Evento;


class ChatController extends Controller {

	public function anyIndex()
	{

		try {

			// Guardo código del proceso actual para eliminarlo cuando quiera reiniciar el chat
		    $pid = getmypid();
		    $nPid = new Pid;
		    $nPid->codigo = $pid;
		    $nPid->save();


		    // Iniciamos el servidor del chat
			$server = IoServer::factory(
		        new HttpServer(
		            new WsServer(
		                new Chat()
		            )
		        ),
		        8787
		    );
		    $server->run();
		    

		} catch (React\Socket\ConnectionException $e) {
			return 'Error';
		}
		

		return 'No se creó servidor de chat';
	}


	public function putCerrarServidor(Request $request)
	{
		$pids = Pid::all();

		foreach ($pids as $key => $pid) {
			if (is_numeric($pid->codigo)) {
				exec("kill -9 " . $pid->codigo);
				exec("kill -9 " . $pid->codigo);
			}
			$pid->delete();
		}

		return 'Procesos eliminados';
	}





}
