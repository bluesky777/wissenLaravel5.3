<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('password_resets', function(Blueprint $table)
		{
			$table->engine = "InnoDB";
			$table->string('email')->index();
			$table->string('token')->index();
			$table->timestamp('created_at');
		});


		Schema::create('qrcodes', function(Blueprint $table)
		{
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('codigo');
			$table->string('comando')->nullable(); // Dar paso a un participante, etc.
			$table->string('parametro')->nullable();
			$table->boolean('reconocido')->default(false);
			$table->timestamps();
		});

		Schema::create('pids', function(Blueprint $table)
		{
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->text('codigo');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qrcodes');
		Schema::drop('pids');
		Schema::drop('password_resets');
	}

}
