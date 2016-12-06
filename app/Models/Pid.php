<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pid extends Model {


	protected $table = 'pids';
	public $timestamps = false;

	public static function nuevo($value)
	{
		$pr = new Pid;
        $pr->codigo = $value;
        $pr->save();
        return $pr;
	}

}
