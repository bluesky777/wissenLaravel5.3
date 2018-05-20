<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


use App\Models\User;
use App\Models\Evento;
use App\Models\Inscripcion;
use App\Models\User_event;
use App\Models\ImagenModel;
use App\Models\Role;

use \Hash;
use DB;



class UsersController extends Controller {
	
	//Para probar ngx-admin
	public function getIndex2()
	{
		return User::all();
	}

	public function getIndex()
	{
		$user 		= User::fromToken();
		$evento_id 	= $user->evento_selected_id;

		$consulta 	= 'SELECT u.id, u.nombres, u.apellidos, u.sexo, u.username, u.email, u.is_superuser, 
						u.cell, u.edad, u.idioma_main_id, u.evento_selected_id, 
						IFNULL(e.nivel_id, "") as nivel_id, e.pagado, e.pazysalvo, u.entidad_id, 
						u.imagen_id, IFNULL(CONCAT("perfil/", i.nombre), IF(u.sexo="F", :female, :male)) as imagen_nombre,
						en.nombre as nombre_entidad, en.lider_id, en.lider_nombre, en.logo_id, en.alias   
					FROM users u 
					inner join ws_user_event e on e.user_id = u.id and e.evento_id = :evento_id 
					left join images i on i.id=u.imagen_id and i.deleted_at is null 
					left join ws_entidades en on en.id=u.entidad_id and en.deleted_at is null
					where u.deleted_at is null order by u.id DESC';

		$usuarios 	= DB::select($consulta, [':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id] );


		$cant = count($usuarios);

		for($i = 0; $i < $cant; $i++){

			$categs = Inscripcion::todas($usuarios[$i]->id, $evento_id);
			$usuarios[$i]->inscripciones = $categs;

			$usuT = User::find($usuarios[$i]->id);
			$roles = $usuT->roles()->get();
			$usuarios[$i]->roles = $roles;

		}

		return $usuarios;
	}

	/*
	// http://localhost/wissenLaravel/public/api/usuarios/cambiar-pass
	public function getCambiarPass()
	{
		//$user = User::fromToken();
		
		$usu = User::find(1);
		$usu->password = Hash::make('sub');
		$usu->save();
		return "Contraseña cambiada";

	}
	*/
	
	public function putCambiarPass()
	{
		$user = User::fromToken();
		
		$usu = User::find(Request::usu_id);
		$usu->password = Hash::make(Request::input('password', ''));
		$usu->save();
		return "Contraseña cambiada";

	}


	public function postStore()
	{
		$user 			= User::fromToken();
		$evento_id 		= $user->evento_selected_id;
		$imgUsuario 	= Request::input('imgUsuario');

		$usuario = new User;

		if ($imgUsuario) {
			$usuario->imagen_id 	= $imgUsuario['id'];
		}


		$usuario->nombres 		= Request::input('nombres');
		$usuario->apellidos 	= Request::input('apellidos');
		$usuario->sexo 			= Request::input('sexo');
		$usuario->username 		= Request::input('username');
		$usuario->password 		= \Hash::make(Request::input('password', ''));
		$usuario->email 		= Request::input('email');
		$usuario->is_superuser 	= Request::input('is_superuser', false);
		$usuario->cell 			= Request::input('cell');		
		$usuario->edad 			= Request::input('edad');	
		$usuario->entidad_id	= Request::input('entidad')['id'];
		$usuario->idioma_main_id = 1;
		$usuario->evento_selected_id = $evento_id;


		$usuario->save();
	
		$role = Role::where('name', 'Participante')->first();
		$usuario->attachRole($role);
		$usuario->roles = $usuario->roles()->get();

		$user_event 			= new User_event;
		$user_event->user_id 	= $usuario->id;
		$user_event->evento_id 	= $evento_id;

		$nivel_id = Request::input('nivel_id');
		if ($nivel_id == ''){
			$nivel_id = null;
		}
		if ($nivel_id == "-1" || $nivel_id == -1) {
			$nivel_id = 0;
		}

		$user_event->nivel_id 	= $nivel_id;
		$user_event->signed_by 	= $user->id;
		$user_event->save();

		$inscripciones_nuevas = [];
		$inscripciones = Request::input('inscripciones');
		$cant_ins = count($inscripciones);

		for($i=0; $i < $cant_ins; $i++){

			$inscrip = Inscripcion::inscribir($usuario->id, $inscripciones[$i]['categoria_id'], $user->id);
			array_push($inscripciones_nuevas, $inscrip);

		}

		$usuario->inscripciones = $inscripciones_nuevas;

		$usuario->imagen_nombre = ImagenModel::imagen_de_usuario($usuario->sexo, $usuario->imagen_id);
		$usuario->nivel_id = $user_event->nivel_id;
		
		return $usuario;
	}



	public function putUpdate()
	{
		$user 			= User::fromToken();

		$user_id 		= Request::input('id');
		$imgUsuario 	= Request::input('imgUsuario');


		$usuario = User::findOrFail($user_id);

		if ($imgUsuario) {
			$usuario->imagen_id 	= $imgUsuario['id'];
		}


		$usuario->nombres 		= Request::input('nombres');
		$usuario->apellidos 	= Request::input('apellidos');
		$usuario->sexo 			= Request::input('sexo');
		$usuario->username 		= Request::input('username');
		$usuario->email 		= Request::input('email');
		$usuario->is_superuser 	= Request::input('is_superuser', false);
		$usuario->cell 			= Request::input('cell');		
		$usuario->edad 			= Request::input('edad');

		$pass = Request::input('password', '');
		if ($pass != '') {
			$usuario->password = \Hash::make($pass);
		}

		$usuario->save();

		return $usuario;
	}





	public function putCambiarEntidad()
	{	
		$user = User::fromToken();

		$user_id 		= Request::input('user_id');
		$entidad_id 	= Request::input('entidad_id');

		$usuario 		= User::findOrFail($user_id);
		$usuario->entidad_id = $entidad_id;
		$usuario->save();

		return $entidad_id;
	}


	public function deleteDestroy($id)
	{
		$user = User::fromToken();

		$usuario = User::findOrFail($id);
		$usuario->delete();

		return $id;
	}

}
