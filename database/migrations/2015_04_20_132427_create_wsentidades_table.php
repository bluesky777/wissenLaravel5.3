<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWsentidadesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ws_entidades', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre');
            $table->integer('lider_id')->unsigned()->nullable(); // Si se quiere registrar
            $table->string('lider_nombre')->nullable(); // Si no es registrado, sol o copiamos el nombre
            $table->integer('logo_id')->unsigned()->nullable();
            $table->string('telefono')->nullable();
            $table->string('alias')->nullable();
            $table->integer('evento_id')->unsigned();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });



		/*
		Nombre de las ciencias de las preguntas 
		ejemplos:
			- Matemáticas, Español, etc.
			- Apocalipsis, Daniel, etc.
		*/
		Schema::create('ws_disciplinas_king', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->integer('evento_id')->unsigned();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_disciplinas_king', function(Blueprint $table) {
			$table->foreign('evento_id')->references('id')->on('ws_eventos')->onDelete('cascade');
		});




		Schema::create('ws_disciplinas_traduc', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->integer('disciplina_id')->unsigned(); // Disciplina a la que pertenece esta traducción
            $table->string('descripcion')->nullable();
            $table->integer('idioma_id')->unsigned();
            $table->boolean('traducido')->default(false)->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
 			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_disciplinas_traduc', function(Blueprint $table) {
			$table->foreign('disciplina_id')->references('id')->on('ws_disciplinas_king')->onDelete('cascade');
		});



		/*
		Nombre de niveles en el evento
		ejemplos:
			- A, B, etc.
			- Niño, Adulto, etc.
		*/
		Schema::create('ws_niveles_king', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->integer('evento_id')->unsigned();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_niveles_king', function(Blueprint $table) {
			$table->foreign('evento_id')->references('id')->on('ws_eventos')->onDelete('cascade');
		});




		Schema::create('ws_niveles_traduc', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->integer('nivel_id')->unsigned(); // Nivel al que pertenece esta traducción
            $table->string('descripcion')->nullable();
            $table->integer('idioma_id')->unsigned();
            $table->boolean('traducido')->default(false)->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_niveles_traduc', function(Blueprint $table) {
			$table->foreign('nivel_id')->references('id')->on('ws_niveles_king')->onDelete('cascade');
		});




		/*
		Nombre de categorías en el evento. Estas son la combinación entre nivel y disciplina
		ejemplos:
			- Español A, Inglés B, etc.
			- Apocalipsis (Niño), Génesis (Adulto), etc.
		*/
		Schema::create('ws_categorias_king', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->integer('nivel_id')->unsigned()->nullable();
            $table->integer('disciplina_id')->unsigned()->nullable();
            $table->integer('evento_id')->unsigned();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_categorias_king', function(Blueprint $table) {
			$table->foreign('nivel_id')->references('id')->on('ws_niveles_king')->onDelete('cascade');
			$table->foreign('disciplina_id')->references('id')->on('ws_disciplinas_king')->onDelete('cascade');
			$table->foreign('evento_id')->references('id')->on('ws_eventos')->onDelete('cascade');
		});


		

		Schema::create('ws_categorias_traduc', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->string('abrev')->nullable();
            $table->integer('categoria_id')->unsigned(); // Nivel al que pertenece esta traducción
            $table->string('descripcion')->nullable();
            $table->integer('idioma_id')->unsigned();
            $table->boolean('traducido')->default(false)->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_categorias_traduc', function(Blueprint $table) {
			$table->foreign('categoria_id')->references('id')->on('ws_categorias_king')->onDelete('cascade');
		});


		// NIVEL PARTICIPANTES
		Schema::create('ws_nivel_participante', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('nivel_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });
		Schema::table('ws_nivel_participante', function(Blueprint $table) {
			$table->foreign('nivel_id')->references('id')->on('ws_niveles_king')->onDelete('cascade');
		});



		// INSCRIPCIONES
		Schema::create('ws_inscripciones', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('categoria_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->boolean('allowed_to_answer')->nullable()->default(true); // Cuando el participante haga un test, cambia a False, pero un asesor puede darle otra oportunidad, volviendolo True de nuevo.
            $table->integer('signed_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_inscripciones', function(Blueprint $table) {
			$table->foreign('categoria_id')->references('id')->on('ws_categorias_king')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('signed_by')->references('id')->on('users')->onDelete('cascade');
		});

	



	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ws_inscripciones');
		Schema::drop('ws_nivel_participante');
		Schema::drop('ws_categorias_traduc');
		Schema::drop('ws_categorias_king');
		Schema::drop('ws_niveles_traduc');
		Schema::drop('ws_niveles_king');
		Schema::drop('ws_disciplinas_traduc');
		Schema::drop('ws_disciplinas_king');
		Schema::drop('ws_entidades');
	}

}
