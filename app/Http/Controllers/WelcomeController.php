<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\DB;


use App\Models\Evento;
use App\Models\Qrcode;



class WelcomeController extends Controller {


	public function anyIndex(Request $request)
	{
		$evento = Evento::where('actual', true)->first();
		$evento->ip = $request->ip();

		if ($request->qr == false) {
			$qr = new Qrcode;
			$qr->codigo = (string)mt_rand(0,999999);
			$qr->comando = "let_in";
			$qr->save();

			$evento->qr = $qr->codigo;
		}
		

		

		return $evento;
	}



	public function postIndex()
	{

	}

}
