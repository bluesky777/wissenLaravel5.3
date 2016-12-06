<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;



use App\Models\Qrcode;
use App\Models\User;


class QrCodesController extends Controller {

	public function postIndex(Request $request)
	{
		$user = User::fromToken();

		$codigo = $request->input('code');
		$qr = Qrcode::where('codigo', $codigo)->first();

		if ($qr) {
			if ($qr->reconocido){
				$qr->accepted = false;
				return $qr;
			}else{
				$qr->reconocido = true;
				$qr->save();
				$qr->accepted = true;
				return $qr;
			}
		
		}else{
			return ["res" => "no_encontrado"];
		}

		
	}


	public function putValidarUsuario(Request $request)
	{
		$token_auth = $request->token_auth;
		$user_auth = User::fromToken($token_auth);

		if ( $user_auth->hasRole('Admin') || $user_auth->hasRole('Profesor') || $user_auth->hasRole('Asesor') ||  $user_auth->is_superuser) {
			$user_id 	= $request->user_id;
			$user 		= User::find($user_id);
			$token 		= JWTAuth::fromUser($user);
			return ['token'=>$token];
		}else{
			return 'No autorizado';
		}

	}


}

