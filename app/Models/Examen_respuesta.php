<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examen_respuesta extends Model {

	use SoftDeletes;

	protected $table = 'ws_examen_respuesta';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

}
