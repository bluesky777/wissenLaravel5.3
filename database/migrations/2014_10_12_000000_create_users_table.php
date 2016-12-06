<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		// SET FOREIGN_KEY_CHECKS = 0; // Para llenar la DB cuando sea necesario con el .sql



		Schema::create('users', function(Blueprint $table)
		{
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('nombres');
			$table->string('apellidos')->nullable();
			$table->string('sexo')->default('M');
			$table->string('username')->unique();
			$table->string('password', 60)->default('');
			$table->string('email')->nullable()->unique();
			$table->integer('entidad_id')->unsigned()->nullable();
			$table->boolean('is_superuser')->default(false);
			$table->string('cell')->nullable();
			$table->integer('edad')->nullable();
			$table->integer('imagen_id')->unsigned()->nullable();
			$table->integer('idioma_main_id')->unsigned()->nullable()->default(1);
			$table->integer('evento_selected_id')->unsigned()->nullable(); // Solo para administradores que puedan pasar de un evento a otro
			$table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
			$table->rememberToken();
			$table->timestamps();
		});

		Schema::create('images', function(Blueprint $table)
		{
			$table->engine = "InnoDB";
			$table->increments('id');
			$table->string('nombre'); // Si no es pública, este nombre indica una imagen dentro de la carpeta del usuario.
			$table->integer('user_id')->nullable();
			$table->boolean('publica')->nullable(); // Esto indica que la imagen no se buscará en la carpeta del usuario, sino en la carpeta del colegio.
			$table->integer('created_by')->nullable();
			$table->integer('updated_by')->nullable();
			$table->integer('deleted_by')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});



		// IDIOMAS
		Schema::create('ws_idiomas', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->string('abrev')->nullable();
            $table->string('original')->nullable();
            $table->boolean('used_by_system')->default(false)->nullable();
			$table->softDeletes();
            $table->timestamps();
        });



	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ws_idiomas');
		Schema::drop('images');
		Schema::drop('users');
	}

}
