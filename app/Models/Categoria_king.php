<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria_king extends Model {

	protected $table="ws_categorias_king";
	use SoftDeletes;

	protected $softDelete = true;

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

}
