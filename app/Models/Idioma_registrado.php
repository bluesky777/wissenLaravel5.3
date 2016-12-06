<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idioma_registrado extends Model {

	use SoftDeletes;

	protected $table = 'ws_idiomas_registrados';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

}
