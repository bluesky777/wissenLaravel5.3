<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

//use App\Http\Controllers\WebSocketController;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Websocket\Chat;

use App\Models\Pid;
use App\Models\Evento;


class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializing Websocket server to receive and manage connections';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
}
