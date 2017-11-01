<?php namespace App\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Pid;
use \StdClass;


class ConnectionInterfaceMia implements ConnectionInterface {


    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function enviar($msg)
    {
    	$msgString = json_encode($msg);
    	$this->send($msgString);
    	return true;
    }

    public function send($msg)
    {
    	parent::send($msg);
    }

    public function close()
    {
    	parent::close();
    }

}
