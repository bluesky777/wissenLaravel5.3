<?php namespace App\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use App\Models\Pid;
use App\Models\Qrcode;
use App\Models\User;

use \StdClass;


class Chat implements MessageComponentInterface {
    protected $clients;
    protected $chatComandos;
    protected $chatScComandos;
    public static $info_evento;

    public function __construct() {
        $this->clients          = new \SplObjectStorage;
        $this->chatComandos     = new ChatComandos;
        $this->chatScComandos   = new ChatSCComandos;
        self::$info_evento      = new infoEvento;
    }

    public function onOpen(ConnectionInterface $conn) {

        $datos = new StdClass;
        $datos->registrado      = false;
        $datos->resourceId      = $conn->resourceId;
        $datos->remoteAddress   = $conn->remoteAddress;
        $datos->categsel        = 0;
        $datos->respondidas     = 0;
        $conn->datos            = $datos;

        $this->clients->attach($conn);

        /*
        foreach ($this->clients as $client) {

            $aEnviar = ["comando" => "conectado", "cliente" => (array)$conn->datos];
            $aEviarString = json_encode($aEnviar);
            $client->send($aEviarString);
        }
        */


        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {


        $msg = json_decode($msg);

        
        switch ($msg->comando) {
            case 'conectar':
                $this->chatComandos->conectar($from, $this->clients, $msg);
                
                break;
            
            case 'registrar':
                $this->chatComandos->registrar($from, $this->clients, $msg);
                
                break;
            
            case 'unregister':
                $this->chatComandos->unregister($from, $this->clients, $msg);
                
                break;
            
            case 'get_clts':
                $this->chatComandos->get_clts($from, $this->clients, $msg);
                
                break;
            

            case 'got_qr':
                $this->chatComandos->got_qr($from, $this->clients, $msg);
                break;
            

            case 'correspondencia':
                $this->chatComandos->correspondencia($from, $this->clients, $msg);
                break;


            case 'cerrar_sesion':
                $this->chatComandos->cerrar_sesion($from, $this->clients, $msg);
                break;


            case 'guardar_nombre_punto':
                $this->chatComandos->guardar_nombre_punto($from, $this->clients, $msg);
                break;


            case 'get_usuarios':
                $this->chatComandos->get_usuarios($from, $this->clients, $msg);
                break;


            case 'let_him_enter':
                $this->chatComandos->let_him_enter($from, $this->clients, $msg);
                break;


            case 'change_a_categ_selected':
                $this->chatComandos->change_a_categ_selected($from, $this->clients, $msg);
                break;

            
            case 'warn_my_categ_selected':
                $this->chatComandos->warn_my_categ_selected($from, $this->clients, $msg);
                break;

            
            case 'empezar_examen':
                $this->chatComandos->empezar_examen($from, $this->clients, $msg);
                break;

            
            case 'empezar_examen_cliente':
                $this->chatComandos->empezar_examen_cliente($from, $this->clients, $msg);
                break;

            
            case 'sc_show_participantes':
                $this->chatScComandos->sc_show_participantes($from, $this->clients, $msg);
                break;

            case 'sc_show_question':
                $this->chatScComandos->sc_show_question($from, $this->clients, $msg);
                break;

            case 'sc_reveal_answer':
                $this->chatScComandos->sc_reveal_answer($from, $this->clients, $msg);
                break;

            case 'sc_show_logo_entidad_partici':
                $this->chatScComandos->sc_show_logo_entidad_partici($from, $this->clients, $msg);
                break;

            case 'sc_show_puntaje_particip':
                $this->chatScComandos->sc_show_puntaje_particip($from, $this->clients, $msg);
                break;

            case 'sc_show_puntaje_examen':
                $this->chatScComandos->sc_show_puntaje_examen($from, $this->clients, $msg);
                break;


            case 'establecer_fondo':
                $this->chatScComandos->establecer_fondo($from, $this->clients, $msg);
                break;


            case 'mostrar_solo_fondo':
                $this->chatScComandos->mostrar_solo_fondo($from, $this->clients, $msg);
                break;


            case 'cambiar_teleprompter':
                $this->chatScComandos->cambiar_teleprompter($from, $this->clients, $msg);
                break;


            case 'sc_answered':
                $this->chatScComandos->sc_answered($from, $this->clients, $msg);
                break;

            case 'next_question':
                $this->chatScComandos->next_question($from, $this->clients, $msg);
                break;

            case 'next_question_cliente':
                $this->chatScComandos->next_question_cliente($from, $this->clients, $msg);
                break;
            
            case 'goto_question_no':
                $this->chatScComandos->goto_question_no($from, $this->clients, $msg);
                break;

            case 'goto_question_no_clt':
                $this->chatScComandos->goto_question_no_clt($from, $this->clients, $msg);
                break;

            
            default:
                # code...
                break;
        }

    }

    public function onClose(ConnectionInterface $conn) {
        Pid::nuevo( 'onCLose: ' . json_encode($conn) );
        
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        foreach ($this->clients as $client) {
            $aEnviar = ["comando" => "desconectado", "clt" => (array)$conn->datos];
            $aEviarString = json_encode($aEnviar);
            $client->send($aEviarString);
        }
        
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

        $conn->send(json_encode(["error"=>$e->getMessage()]));
        Pid::nuevo(json_encode( $e->getMessage()));
        echo "An error has occurred: {$e->getMessage()}\n";
    }





}



