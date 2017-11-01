<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pregunta_agrupada extends Model {

	use SoftDeletes;

	protected $table = 'ws_preguntas_agrupadas';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];



	

}
