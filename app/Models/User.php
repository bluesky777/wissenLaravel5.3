<?php namespace App\Models;
/*
//use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
//use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;



*/


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\SoftDeletes;





use App\Models\ImagenModel;
use App\Models\Entidad;



use Illuminate\Http\Request;




class User extends Authenticatable {

	use Notifiable;

	use SoftDeletes, EntrustUserTrait {

	    SoftDeletes::restore as sfRestore;
	    EntrustUserTrait::restore as euRestore;

	}
    public function restore() {
	    $this->sfRestore();
	    Cache::tags(Config::get('entrust.role_user_table'))->flush();
	}


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	protected $softDelete = true;


	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];



    public static $default_female = 'perfil/system/avatars/female1.jpg';
    public static $default_male = 'perfil/system/avatars/male1.jpg';
    public static $perfil_path = 'perfil/';







	public static function fromToken($already_parsed=false)
	{
		$usuario = [];
		$token = [];
		try
		{
			if ($already_parsed) {

				$token = $already_parsed;
				$usuario = JWTAuth::toUser($token);

			}else{

				$token = JWTAuth::parseToken();
				
				if ($token){

					try {
						$usuario = $token->toUser();
					} catch (Exception $e) {
						//Request::header();
						\App::abort(401, 'error con $token->toUser()');
					}
					
				}else if ( !(Request::has('username')) )  {
					\App::abort(401, 'No existe Token');
				}
			}


			if (!$usuario) {
				\App::abort(401, 'Token inválido, prohibido entrar.');
			}



		}
		catch(JWTException $e)
		{
			//if (! count( Request::all() )) {
				\App::abort(401, 'token_expired');
			//}
		}


		// *************************************************
		//    Traeremos los roles y permisos
		// *************************************************
		User::roles_y_permisos($usuario);

		$usuario->imagen_nombre = ImagenModel::imagen_de_usuario($usuario->sexo, $usuario->imagen_id);

		

		$usuario->token = $token;

		
		// Traemos la entidad
		$entidad = Entidad::find($usuario->entidad_id);
		if ($entidad) {
			$entidad->logo = ImagenModel::ruta_imagen($entidad->logo_id);
			$usuario->entidad = $entidad;
		}
		


		return $usuario;
	}




	public static function datos_usuario_logueado(&$usuario)
	{

		User::roles_y_permisos($usuario);

		$usuario->imagen_nombre = ImagenModel::imagen_de_usuario($usuario->sexo, $usuario->imagen_id);

		
		// Traemos la entidad
		$entidad = Entidad::find($usuario->entidad_id);

		if ($entidad) {
			$entidad->logo = ImagenModel::ruta_imagen($entidad->logo_id);
			$usuario->entidad = $entidad;
		}else{
			$usuario->entidad = array('logo' => ImagenModel::ruta_imagen());
		}
		

		// Traemos evento o eventos
		$evento_id = [];

		if($usuario->hasRole('Admin') || $usuario->hasRole('Asesor')){
			$usuario->eventos = Evento::todos();
			$evento_id = $usuario->evento_selected_id;
		}else{
			$usuario->evento_actual = Evento::actual(); // Creo que debería traer el evento al que está inscrito, no el actual
			$evento_id = $usuario->evento_actual->id;
		}

		
		// Inscripciones
		$inscripciones = Inscripcion::todas($usuario->id, $evento_id);
		$usuario->inscripciones = $inscripciones;


		return $usuario;
	}




	public static function roles_y_permisos(&$usuario)
	{
		$usuario->roles = $usuario->roles()->get();
		$perms = [];

		foreach($usuario->roles as $role )
		{
			$consulta = 'SELECT pm.name, pm.display_name, pm.description from permission_role pmr
					inner join permissions pm on pm.id = pmr.permission_id 
						and pmr.role_id = :role_id';
			
			$permisos = \DB::select(\DB::raw($consulta), array(':role_id' => $role->id));
			
			foreach ($permisos as $permiso) {
				array_push($perms, $permiso->name);
			}
		}

		$usuario->perms = $perms;
	}



	// Todos los permisos de un usuario, con el objeto permiso, o solo con el string name del permiso
	public function permissions($detailed=false)
	{
		$perms = [];

		foreach( $this->roles()->get() as $role )
		{
			$permisos = $role->permissions($detailed);
			// No quiero un array con multiples arrays dentro que contengan los permisos
			// así que recorro cada array con permisos y voy agregando cada elemento permiso al array $perms donde estarán unidos.
			foreach ($permisos as $value) {
				array_push($perms, $value);
			}
		}

		return $perms;
	}











}
