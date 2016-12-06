<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;


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



use Illuminate\Http\Request;


class UsersController extends Controller {


	public function getIndex()
	{
		$user = User::fromToken();
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
					where u.deleted_at is null order by u.id DESC';

		$usuarios = DB::select($consulta, [':female'=>User::$default_female, ':male'=>User::$default_male, ':evento_id' => $evento_id] );


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



	public function putCambiarPass(Request $request)
	{
		$user = User::fromToken();
		
		$usu = User::find($request->usu_id);
		$usu->password = Hash::make($request->input('password', ''));
		$usu->save();
		return "Contraseña cambiada";

	}


	public function postStore(Request $request)
	{
		$user 			= User::fromToken();
		$evento_id 		= $user->evento_selected_id;
		$imgUsuario 	= $request->input('imgUsuario');

		$usuario = new User;

		if ($imgUsuario) {
			$usuario->imagen_id 	= $imgUsuario['id'];
		}


		$usuario->nombres 		= $request->input('nombres');
		$usuario->apellidos 	= $request->input('apellidos');
		$usuario->sexo 			= $request->input('sexo');
		$usuario->username 		= $request->input('username');
		$usuario->password 		= \Hash::make($request->input('password', ''));
		$usuario->email 		= $request->input('email');
		$usuario->is_superuser 	= $request->input('is_superuser', false);
		$usuario->cell 			= $request->input('cell');		
		$usuario->edad 			= $request->input('edad');	
		$usuario->entidad_id	= $request->input('entidad')['id'];
		$usuario->idioma_main_id = 1;


		$usuario->save();
	
		$role = Role::where('name', 'Participante')->first();
		$usuario->attachRole($role);
		$usuario->roles = $usuario->roles()->get();

		$user_event 			= new User_event;
		$user_event->user_id 	= $usuario->id;
		$user_event->evento_id 	= $evento_id;
		$user_event->nivel_id 	= $request->input('nivel_id');
		$user_event->save();

		$inscripciones_nuevas = [];
		$inscripciones = $request->input('inscripciones');
		$cant_ins = count($inscripciones);

		for($i=0; $i < $cant_ins; $i++){

			$inscrip = Inscripcion::inscribir($usuario->id, $inscripciones[$i]['categoria_id']);
			array_push($inscripciones_nuevas, $inscrip);

		}

		$usuario->inscripciones = $inscripciones_nuevas;

		$usuario->imagen_nombre = ImagenModel::imagen_de_usuario($usuario->sexo, $usuario->imagen_id);
		$usuario->nivel_id = $user_event->nivel_id;
		
		return $usuario;
	}



	public function putUpdate(Request $request)
	{
		$user 			= User::fromToken();
		$evento_id 		= $user->evento_selected_id;

		$user_id 		= $request->input('id');
		$imgUsuario 	= $request->input('imgUsuario');


		$usuario = User::findOrFail($user_id);

		if ($imgUsuario) {
			$usuario->imagen_id 	= $imgUsuario['id'];
		}

		$usuario 				= User::findOrFail($user_id);
		$usuario->nombres 		= $request->input('nombres');
		$usuario->apellidos 	= $request->input('apellidos');
		$usuario->sexo 			= $request->input('sexo');
		$usuario->username 		= $request->input('username');
		$usuario->email 		= $request->input('email');
		$usuario->is_superuser 	= $request->input('is_superuser', false);
		$usuario->cell 			= $request->input('cell');		
		$usuario->edad 			= $request->input('edad');
		$usuario->entidad_id	= $request->input('entidad')['id'];

		$pass = $request->input('password', '');
		if ($pass != '') {
			$usuario->password = \Hash::make($pass);
		}

		$usuario->save();

		return $usuario;
	}





	public function putCambiarEntidad(Request $request)
	{	
		$user = User::fromToken();

		$user_id = $request->input('user_id');
		$entidad_id = $request->input('entidad_id');

		$usuario = User::findOrFail($user_id);
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
