<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idioma extends Model
{
    use SoftDeletes;

	protected $table = 'ws_idiomas';

	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	protected $hidden = ['deleted_at', 'created_at', 'updated_at'];
}
