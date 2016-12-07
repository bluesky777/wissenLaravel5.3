<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


use App\Models\Idioma;
use App\Models\Idioma_registrado;
use App\Models\Categoria_king;
use App\Models\Categoria_traduc;
use App\Models\User;


class IdiomasController extends Controller {

	public function anyIndex()
	{
		$idiomas = Idioma::all();
		return $idiomas;
	}


	public function postStore(Request $request)
	{
		
		$idioma_preg = Idioma_registrado::where('evento_id', '=', $request->input('evento_id'))
							->where('idioma_id', '=', $request->input('idioma_id'))
							->onlyTrashed()
							->first();

		if ($idioma_preg) {
			$idioma_preg->restore();
		}else{

			$idioma_preg = new Idioma_registrado;

			$idioma_preg->evento_id = $request->input('evento_id');
			$idioma_preg->idioma_id = $request->input('idioma_id');
			$idioma_preg->save();
		}


		
		// Comprobar traducciones de categorías
		$categorias = Categoria_king::where('evento_id', '=', $idioma_preg->evento_id)->get();

		foreach ($categorias as $key => $categoriaking) {

			$cat_traducs_trash = Categoria_traduc::onlyTrashed()->where('categoria_id', '=', $categoriaking->id)
													->where('idioma_id', '=', $idioma_preg->id)
													->first();

			if ($cat_traducs_trash) {

				$cat_traducs_trash->restore();

			}else{

				$cat_traduc = Categoria_traduc::where('categoria_id', '=', $categoriaking->id)
											->where('idioma_id', '=', $idioma_preg->id)
											->first();

				if (!$cat_traduc) {
					
					$categoria_trad 				= new Categoria_traduc;
					$categoria_trad->nombre			= '';
					$categoria_trad->categoria_id	= $categoriaking->id;
					$categoria_trad->idioma_id		= $idioma_preg->id;
					$categoria_trad->traducido 		= false;
					$categoria_trad->save();

				}
				
			}
			
			

		}

		return $idioma_preg;

	}



	public function putCambiarIdioma(Request $request)
	{
		$user = User::fromToken();

		$usu = User::findOrFail($user->id);
		$usu->idioma_main_id = $request->input('idioma_id');
		$usu->save();

		return 'Idioma cambiado';
	}

	public function update($id)
	{
		//
	}


	public function deleteDestroy(Request $request)
	{
		$idioma_preg = Idioma_registrado::where('evento_id', '=', $request->input('evento_id'))
							->where('idioma_id', '=', $request->input('idioma_id'))
							->first();

		if ($idioma_preg) {
			
			$idioma_preg->delete();

		}
		
		return $idioma_preg;
	}

}
