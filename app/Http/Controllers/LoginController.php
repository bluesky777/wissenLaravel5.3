<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;



use App\Models\User;
use App\Models\ImagenModel;
use App\Models\Evento;
use App\Models\Inscripcion;


use Illuminate\Http\Request;

class LoginController extends Controller {


	public function putLogout()
	{	
		try {
			JWTAuth::invalidate(JWTAuth::getToken());
		} catch (JWTException $e) {
			
		}
		
		return 'Deslogueando';
	}



	public function postLogin(Request $request)
	{
		$user = [];
		$token = [];

		// grab credentials from the request
        $credentials = $request->only('username', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

		$user = JWTAuth::toUser($token);
		$user->token = $token;

		User::datos_usuario_logueado($user);


		return $user;
	
	}



	public function postVerificar()
	{
		try {
			
			$user = JWTAuth::parseToken()->authenticate();
			User::datos_usuario_logueado($user);

		} catch (JWTException $e) {
			return response()->json(['error' => 'Al parecer el token expiró'], 401);
		}

		
		return $user;
	}



}
