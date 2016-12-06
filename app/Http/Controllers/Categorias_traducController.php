<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class Categorias_traducController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	
	public function deleteDestroy($id)
	{
		$evento = Evento::find($id);
		$evento->delete();

		return $evento;
	}
	
	public function deleteForcedelete($id)
	{
		$evento = Evento::onlyTrashed()->findOrFail($id);
		
		if ($evento) {
			$evento->forceDelete();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	
	}

	public function putRestore($id)
	{
		$evento = Evento::onlyTrashed()->findOrFail($id);

		if ($evento) {
			$evento->restore();
		}else{
			return \App::abort(400, 'Evento no encontrado en la Papelera.');
		}
		return $evento;
	}

	public function getTrashed()
	{
		//$user = User::fromToken();
		$consulta = 'SELECT * FROM ws_eventos
					where deleted_at is not null';

		return \DB::select(\DB::raw($consulta));
	}

}
