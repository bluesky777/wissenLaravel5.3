<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWseventosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		// EVENTOS
		Schema::create('ws_eventos', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre');
            $table->string('alias')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('password')->nullable();
            $table->boolean('mostrar_nombre_punto')->default(true);
            $table->boolean('gran_final')->default(false)->nullable(); // Si es false, está en ELIMINATORIAS. Si es true, está en la GRAN FINAL.
            $table->boolean('with_pay')->nullable();
            $table->boolean('por_sumatoria_puntos')->default(false)->nullable(); // Los resultados se sacarán por sumar cada punto obtenido o por promediar las correctas entre la cantidad.
            $table->boolean('actual')->default(false)->nullable();
            $table->integer('precio1')->nullable();
            $table->integer('precio2')->nullable();
            $table->integer('precio3')->nullable();
            $table->integer('precio4')->nullable();
            $table->integer('precio5')->nullable();
            $table->integer('precio6')->nullable();
            $table->integer('idioma_principal_id')->unsigned()->nullable();
            $table->boolean('es_idioma_unico')->default(false)->nullable();
            $table->boolean('enable_public_chat')->nullable();
            $table->boolean('enable_private_chat')->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });



		// USER EVENT
		Schema::create('ws_user_event', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('evento_id')->unsigned()->nullable();
            $table->integer('nivel_id')->unsigned()->nullable(); // No puedo volverla foránea
            $table->integer('pagado')->nullable(); // Dinero pagado por el usuario en este evento.
            $table->boolean('pazysalvo')->nullable()->default(false); // Dinero pagado por el usuario en este evento.
            $table->integer('signed_by')->unsigned()->nullable();
            $table->timestamps();
        });
		Schema::table('ws_user_event', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('evento_id')->references('id')->on('ws_eventos')->onDelete('cascade');
			$table->foreign('signed_by')->references('id')->on('users')->onDelete('cascade');
		});






        // Idiomas en los que se traducirán los datos ingresados al sistema, como las disciplinas, preguntas, etc. """
		Schema::create('ws_idiomas_registrados', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('evento_id')->unsigned();
            $table->integer('idioma_id')->unsigned();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_idiomas_registrados', function(Blueprint $table) {
			$table->foreign('evento_id')->references('id')->on('ws_eventos')->onDelete('cascade');
			$table->foreign('idioma_id')->references('id')->on('ws_idiomas')->onDelete('cascade');
		});





	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ws_idiomas_registrados');
		Schema::drop('ws_user_event');
		Schema::drop('ws_eventos');
	}

}
