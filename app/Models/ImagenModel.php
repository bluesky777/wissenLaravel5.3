<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class ImagenModel extends Model {
	protected $fillable = [];

	protected $table = 'images';

	use SoftDeletes;
	protected $softDelete = true;




	
	public static function imagen_de_usuario($sexo='M', $imagen_id=false)
	{

		if ($imagen_id) {

			$img = ImagenModel::find($imagen_id);

			if ($img) {

				if ($img->publica) {
					//return 'publics/' . $img->nombre; // Falta copiar las imágenes a publics cuando estas son públicas
					return 'perfil/' . $img->nombre;
				}else{
					return 'perfil/' . $img->nombre;
				}
					
			}else{
				return ImagenModel::default_image_name($sexo);
			}
			
		}else{

			return ImagenModel::default_image_name($sexo);

		}
	}

	

	public static function default_image_id($sexo)
	{
		if ($sexo == 'F') {
			return 2;
		}else{
			return 1; // ID de la imagen masculina
		}
	}
	public static function default_image_name($sexo)
	{
		if ($sexo == 'F') {
			return User::$default_female;
		}else{
			return User::$default_male;
		}
	}


	
	public static function ruta_imagen($imagen_id=false)
	{
		if ($imagen_id) {

			$img = ImagenModel::find($imagen_id);

			if ($img) {

				if ($img->publica) {
					//return 'publics/' . $img->nombre; // Falta copiar las imágenes a publics cuando estas son públicas
					return 'perfil/' . $img->nombre;
				}else{
					return 'perfil/' . $img->nombre;
				}

			}else{
				return 'perfil/system/avatars/no-photo.jpg';
			}


		}else{

			return 'perfil/system/avatars/no-photo.jpg';

		}
	}

	


}