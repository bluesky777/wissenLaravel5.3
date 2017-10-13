<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWsevaluacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{


		// EVALUACIÓN -> Cuestionario ordenado de preguntas
		Schema::create('ws_evaluaciones', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('categoria_id')->unsigned()->nullable();
            $table->integer('evento_id')->unsigned();
            $table->boolean('actual')->default(false)->nullable(); // Si es el actual, será la evaluación que realizarán los participantes
            $table->string('descripcion')->nullable();
            $table->integer('duracion_preg')->nullable(); // En segundos. Duración de la pregunta si el examen es Dirigido y la pregunta no tiene duración asignada;
            $table->integer('duracion_exam')->nullable(); // En minutos. Duración del examen si es Independiente
            $table->boolean('one_by_one')->nullable(); // Se responde una pregunta a la vez o varias preguntas en una página
            $table->integer('puntaje_por_promedio')->nullable()->default(true); // Promediar en vez de sumar los puntos de cada pregunta.
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_evaluaciones', function(Blueprint $table) {
			$table->foreign('categoria_id')->references('id')->on('ws_categorias_king')->onDelete('cascade');
			$table->foreign('evento_id')->references('id')->on('ws_eventos')->onDelete('cascade');
			$table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
		});



		// PREGUNTA KING
		Schema::create('ws_preguntas_king', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('descripcion')->nullable();
            $table->string('tipo_pregunta')->nullable(); // Test, Multiple, Texto, Lista, Ordenar, Cuadrícula
            $table->integer('duracion')->unsigned()->nullable(); // En segundos
            $table->integer('categoria_id')->unsigned(); 
            $table->integer('puntos')->unsigned()->default(0); // Si el puntaje de la evaluación se saca por promedio, este valor no se toma.  
            $table->boolean('aleatorias')->default(false)->nullable(); // Si true, las opciones no necesariamente saldrán como se hayan creado, sino de manera aleatoria.
            $table->integer('added_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_preguntas_king', function(Blueprint $table) {
			$table->foreign('categoria_id')->references('id')->on('ws_categorias_king')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});




		// PREGUNTA TRANSLATE
		Schema::create('ws_pregunta_traduc', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->text('enunciado')->nullable();
            $table->string('ayuda')->nullable();
            $table->integer('pregunta_id')->unsigned();
            $table->integer('idioma_id')->unsigned();
            $table->string('texto_arriba')->nullable(); // Para el tipo de pregunta ORDENAR
            $table->string('texto_abajo')->nullable(); // Para el tipo de pregunta ORDENAR
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->boolean('traducido')->default(false)->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_pregunta_traduc', function(Blueprint $table) {
			$table->foreign('pregunta_id')->references('id')->on('ws_preguntas_king')->onDelete('cascade');
			$table->foreign('idioma_id')->references('id')->on('ws_idiomas')->onDelete('cascade');
		});




		// OPCIONES
		Schema::create('ws_opciones', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->text('definicion')->nullable();
            $table->integer('pregunta_traduc_id')->unsigned();
            $table->integer('image_id')->unsigned()->nullable(); // si también quiere poner imagen
            $table->integer('orden')->unsigned()->nullable(); // Aparecerá como primera, segunda, etc. Solo importará si la pregunta no está configurada para que sea de opciones aleatoria
            $table->boolean('is_correct')->nullable()->default(false); // Puede ser el id de cualquier pregunta
            $table->integer('added_by')->unsigned()->nullable();
            $table->timestamps();
        });
		Schema::table('ws_opciones', function(Blueprint $table) {
			$table->foreign('pregunta_traduc_id')->references('id')->on('ws_pregunta_traduc')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});




		// *******************************************************
		// ************          GRUPO PREGUNTAS       ***********
		// *******************************************************
		Schema::create('ws_grupos_preguntas', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('descripcion')->nullable();
            $table->integer('categoria_id')->unsigned(); 
            $table->boolean('is_cuadricula')->default(false)->nullable(); // Si true, se creará una cuadrícula de tipo encuesta.
            $table->integer('added_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_grupos_preguntas', function(Blueprint $table) {
			$table->foreign('categoria_id')->references('id')->on('ws_categorias_king')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});




		// CONTENIDO TRASLATE
		Schema::create('ws_contenido_traduc', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->text('definicion')->nullable();
            $table->integer('grupo_pregs_id')->unsigned();
            $table->integer('idioma_id')->unsigned(); // Puede ser el id de cualquier pregunta
            $table->boolean('traducido')->default(false)->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_contenido_traduc', function(Blueprint $table) {
			$table->foreign('grupo_pregs_id')->references('id')->on('ws_grupos_preguntas')->onDelete('cascade');
			$table->foreign('idioma_id')->references('id')->on('ws_idiomas')->onDelete('cascade');
		});





		// PREGUNTA CONTENIDO
		Schema::create('ws_preguntas_agrupadas', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->text('enunciado')->nullable();
            $table->string('ayuda')->nullable();
            $table->integer('contenido_id')->unsigned();
            $table->integer('duracion')->unsigned()->nullable(); // En segundos
            $table->string('tipo_pregunta')->nullable(); // Test, Multiple, Texto, Lista, Ordenar, Cuadrícula
            $table->integer('puntos')->unsigned()->default(0);
            $table->boolean('aleatorias')->default(false)->nullable(); // Si true, las opciones no necesariamente saldrán como se hayan creado, sino de manera aleatoria.
            $table->integer('orden')->unsigned()->nullable(); // Orden de la pregunta dentro del grupo de preguntas.
            $table->integer('added_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
		Schema::table('ws_preguntas_agrupadas', function(Blueprint $table) {
			$table->foreign('contenido_id')->references('id')->on('ws_contenido_traduc')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});





		// OPCIONES AGRUPADAS
		Schema::create('ws_opciones_agrupadas', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('definicion')->nullable();
            $table->integer('preg_agrupada_id')->unsigned();
            $table->integer('orden')->unsigned()->nullable(); // Aparecerá como primera, segunda, etc. Solo importará si la pregunta no está configurada para que sea de opciones aleatoria
            $table->boolean('is_correct')->nullable(); // Puede ser el id de cualquier pregunta
            $table->integer('added_by')->unsigned()->nullable();
            $table->timestamps();
        });
		Schema::table('ws_opciones_agrupadas', function(Blueprint $table) {
			$table->foreign('preg_agrupada_id')->references('id')->on('ws_preguntas_agrupadas')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});


		// OPCIONES CUADRÍCULA
		Schema::create('ws_opciones_cuadricula', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('definicion')->nullable();
            $table->integer('contenido_traduc_id')->unsigned();
            $table->string('icono')->nullable(); // Para poner caritas si así lo desea.
            $table->integer('added_by')->unsigned()->nullable();
            $table->timestamps();
        });
		Schema::table('ws_opciones_cuadricula', function(Blueprint $table) {
			$table->foreign('contenido_traduc_id')->references('id')->on('ws_contenido_traduc')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});




		// PREGUNTA EVALUACION
		Schema::create('ws_pregunta_evaluacion', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('evaluacion_id')->unsigned()->nullable();
            $table->integer('pregunta_id')->unsigned()->nullable(); // Puede ser el id de cualquier pregunta
            $table->integer('grupo_pregs_id')->unsigned()->nullable(); // Puede ser el id de grupo de preguntas
            $table->integer('orden')->unsigned()->nullable();
            $table->boolean('aleatorias')->default(false)->nullable(); // Si true, las preguntas no necesariamente saldrán como se hayan creado, sino de manera aleatoria. Siempre y cuando sea una eliminatoria.
            $table->integer('added_by')->unsigned()->nullable();
            $table->timestamps();
        });
		Schema::table('ws_pregunta_evaluacion', function(Blueprint $table) {
			$table->foreign('evaluacion_id')->references('id')->on('ws_evaluaciones')->onDelete('cascade');
			$table->foreign('pregunta_id')->references('id')->on('ws_preguntas_king')->onDelete('cascade');
			$table->foreign('grupo_pregs_id')->references('id')->on('ws_grupos_preguntas')->onDelete('cascade');
			$table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
		});



	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ws_pregunta_evaluacion');
		Schema::drop('ws_opciones_cuadricula');
		Schema::drop('ws_opciones_agrupadas');
		Schema::drop('ws_preguntas_agrupadas');
		Schema::drop('ws_contenido_traduc');
		Schema::drop('ws_grupos_preguntas');
		Schema::drop('ws_opciones');
		Schema::drop('ws_pregunta_traduc');
		Schema::drop('ws_preguntas_king');
		Schema::drop('ws_evaluaciones');
	}

}
