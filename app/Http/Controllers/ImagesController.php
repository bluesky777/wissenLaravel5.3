<?php namespace App\Http\Controllers;

use Request;
use DB;
use File;
use Image;
use \stdClass;

use App\Models\User;
use App\Models\ImagenModel;


class ImagesController extends Controller {

	public function anyIndex()
	{
		$user = User::fromToken();
		
		$imagenes = ImagenModel::where('user_id', $user['id'])
								->get();

		return $imagenes;
	}

	public function getUsuarios()
	{
		$user = User::fromToken();

		$respuesta = [];

		$cons = 'SELECT i.*, CONCAT("'.User::$perfil_path.'", i.nombre) as nombre  
				FROM images i WHERE i.user_id=:user_id and i.deleted_at is null;';
		$respuesta['imagenes'] = DB::select($cons, [':user_id'=> $user['id']]);


		if ($user['is_superuser']) {
			$cons = 'SELECT u.*, i.nombre as imagen_nombre,
					u.imagen_id, IFNULL(CONCAT("'.User::$perfil_path.'", i.nombre), IF(u.sexo="F","'.User::$default_female.'", "'.User::$default_male.'") ) as imagen_nombre  
					FROM users u  
					LEFT JOIN images i on i.id=u.imagen_id and i.deleted_at is null;';
			$respuesta['usuarios'] = DB::select($cons);
		}else{
			$cons = 'SELECT u.*, i.nombre as imagen_nombre 
					u.imagen_id, IFNULL(CONCAT("'.User::$perfil_path.'", i.nombre), IF(u.sexo="F","'.User::$default_female.'", "'.User::$default_male.'") ) as imagen_nombre  
					FROM users u  
					LEFT JOIN images i on i.id=u.imagen_id and i.deleted_at is null
					WHERE u.is_superuser = false;';
			$respuesta['usuarios'] = DB::select($cons);
		}


		return $respuesta;
	}


	public function getDatosImagen()
	{
		//$user = User::fromToken();
		$user_id = Request::input('user_id');
		$imagen_id = Request::input('imagen_id');
		

		$datos_imagen = ImagenModel::DatosImagen($imagen_id, $user_id);

		return $datos_imagen;
	}




	public function postStore()
	{
		$user = User::fromToken();

		$folder = 'images/perfil/';

		if (Request::has('foto')) {
			$newImg = $this->guardar_imagen_tomada($user);
		}else{
			$newImg = $this->guardar_imagen($user);
		}

		
		
		try {
			
			$img = Image::make($folder . $newImg->nombre);
			$img->fit(300);
			$img->resize(300, null, function ($constraint) {
				$constraint->aspectRatio();
			});
			$img->save();
		} catch (Exception $e) {
			
		}

		return $newImg;
	}



	public function putCambiarImgUsuario($usuarioElegido)
	{
		$user = User::findOrFail($usuarioElegido);
		$user->imagen_id = Request::input('imgUsuario');
		$user->save();
		return $user;
	}



	public function postStoreIntacta()
	{
		$user = User::fromToken();
		
		$newImg = $this->guardar_imagen($user);
		$newImg->publica = true;
		$newImg->save();

		return $newImg;
	}

	public function guardar_imagen($user)
	{
		$folderName = 'user_'.$user['id'];
		$folder = 'images/perfil/'.$folderName;

		if (!File::exists($folder)) {
			File::makeDirectory($folder, $mode = 0777, true, true);
		}

		$file = Request::file("file");
		
		try {
			/**/
			//separamos el nombre de la img y la extensión
			$info = explode(".", $file->getClientOriginalName());
			//asignamos de nuevo el nombre de la imagen completo
			$miImg = $file->getClientOriginalName();
			
			//$miImg = date('Y-m-d-H:i:s'); 
		} catch (Exception $e) {
			$miImg = 'cam';
		}
		
		

		//return Request::file('file')->getMimeType(); // Puedo borrarlo
		//mientras el nombre exista iteramos y aumentamos i
		$i = 0;
		while(file_exists($folder.'/'. $miImg)){
			$i++;
			$miImg = $info[0]."(".$i.")".".".$info[1];              
		}

		//guardamos la imagen con otro nombre ej foto(1).jpg || foto(2).jpg etc
		$file->move($folder, $miImg);
		
		$newImg = new ImagenModel;
		$newImg->nombre = $folderName.'/'.$miImg;
		$newImg->user_id = $user['id'];
		$newImg->save();

		return $newImg;
	}


	public function guardar_imagen_tomada($user)
	{
		$folderName = 'user_'.$user['id'];
		$folder = 'images/perfil/'.$folderName;

		if (!File::exists($folder)) {
			File::makeDirectory($folder, $mode = 0777, true, true);
		}
		$consulta 	= "SELECT * FROM images ORDER BY id DESC LIMIT 1";
		$ulti 		= DB::select($consulta)[0];


		//asignamos de nuevo el nombre de la imagen completo
		$miImg = ($ulti->id + 1) . '.jpg';


		$file = Request::input("foto");
		$binary_data = base64_decode( $file );

		//guardamos la imagen con otro nombre ej foto(1).jpg || foto(2).jpg etc
		$result = file_put_contents($folder .'/'. $miImg, $binary_data);
		//$file->move($folder, $miImg);

		
		$newImg = new ImagenModel;
		$newImg->nombre = $folderName.'/'.$miImg;
		$newImg->user_id = $user['id'];
		$newImg->save();

		return $newImg;
	}

	public function putRotarimagen($imagen_id)
	{
		$imagen = ImagenModel::findOrFail($imagen_id);

		$folderName = $imagen->nombre;
		$img_dir = 'images/perfil/'.$folderName;

		$img = Image::make($img_dir);

		$img->rotate(-90);

		$img->save();

		return $imagen->nombre;
	}



	public function putCambiarImagenPerfil($id)
	{
		$user = User::findOrFail($id);
		$user->imagen_id = Request::input('imagen_id');
		$user->save();
		return $user;
	}


	public function putCambiarLogo()
	{
		$user = User::fromToken();

		$conf = Configuracion::all()->first();
		$conf->logo_id = Request::input('logo_id');
		$conf->save();
		return $conf;
	}


	public function deleteDestroy($id)
	{
		$img = ImagenModel::findOrFail($id);
		
		$filename = 'images/perfil/'.$img->nombre;


		// Debería crear un código que impida borrar si la imagen es usada.


		if (File::exists($filename)) {
			File::delete($filename);
			$img->delete();
		}else{
			return 'No se encuentra la imagen a eliminar. '.$img->nombre;
		}


		// Elimino cualquier referencia que otros tengan a esa imagen borrada.
		$users = User::where('imagen_id', $id)->get();
		foreach ($users as $user) {
			$user->imagen_id = null;
			$user->save();
		}


		
		return $img;
	}

}