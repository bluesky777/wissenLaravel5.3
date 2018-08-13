<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWsexamenesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/* 
		Examen respondido de un usuario en una inscripción
		El Test hecho por un participante solo cuenta si está habilitado (enable=True) de lo contrario
		este Test se tomará como si fuera de prueba o de error y no contará para los puntajes.
		*/
		Schema::create('ws_examen_respuesta', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('inscripcion_id')->unsigned();
            $table->integer('evaluacion_id')->unsigned();
            $table->integer('idioma_id')->unsigned()->default(1);
            $table->integer('categoria_id')->unsigned(); // Tal vez borren la evaluación. Por lo menos debo saber la categoría.
            $table->boolean('active')->default(true);
            $table->boolean('gran_final')->default(false); // Dice si el examen fue hecho en eliminatoria o como gran final.
            $table->boolean('terminado')->default(false); // Indica si finalizó todas las preguntas o si se le acabó el tiempo
            $table->boolean('timeout')->default(false); // Se le acabó el tiempo??
            
            $table->integer('res_correctas')->nullable(); // Al calcular los resultados, cuántas correctas obtuvo
            $table->integer('res_incorrectas')->nullable(); 
            $table->boolean('res_by_promedio')->nullable()->default(1); // Me dice si el examen se calculó por promedio o por puntos
            $table->decimal('res_promedio', 6, 3)->nullable(); // Guarda el resultado promedio 
            $table->integer('res_puntos')->nullable(); // Si NO es por promedio sino por puntos, aquí aparecerán los puntos obtenidos
            $table->integer('res_cant_pregs')->nullable(); 
            $table->integer('res_tiempo')->nullable(); // Tiempo en milisegundos
            $table->string('res_tiempo_format')->nullable(); // Tiempo formateado 'hh:mm:ss:ms'

            $table->integer('deleted_by')->unsigned()->nullable();
			$table->softDeletes();
            $table->timestamps();
        });
        
		Schema::table('ws_examen_respuesta', function(Blueprint $table) {
			$table->foreign('inscripcion_id')->references('id')->on('ws_inscripciones')->onDelete('cascade');
			$table->foreign('evaluacion_id')->references('id')->on('ws_evaluaciones')->onDelete('cascade');
		});



		/* 
		Respuesta de un usuario a una pregunta de un Test.
		*/
		Schema::create('ws_respuestas', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('examen_respuesta_id')->unsigned()->nullable();

            $table->integer('pregunta_king_id')->unsigned()->nullable();
            $table->integer('preg_traduc_id')->unsigned()->nullable(); // Pregunta traducida que se respondió. Con esta se sabe el idioma en que se respondió la pregunta.

            $table->integer('grupo_preg_id')->unsigned()->nullable();
            $table->integer('pregunta_agrupada_id')->unsigned()->nullable();

            $table->bigInteger('tiempo')->unsigned()->nullable(); // Milisegundos
            $table->bigInteger('tiempo_aproximado')->unsigned()->nullable(); // Segundos
            
            $table->integer('idioma_id')->unsigned()->nullable(); // Idioma de la pregunta que se respondió. Es redundante.
            $table->string('tipo_pregunta')->nullable(); // Test, Multiple, Texto, Lista, Ordenar. Es redundante.
            $table->integer('puntos_maximos')->unsigned()->nullable(); // Es redundante.
            $table->integer('puntos_adquiridos')->unsigned()->nullable();

            $table->integer('opcion_id')->unsigned()->nullable();
			$table->integer('opcion_agrupada_id')->unsigned()->nullable();
			$table->integer('opcion_cuadricula_id')->unsigned()->nullable();

            $table->timestamps();
        });
		Schema::table('ws_respuestas', function(Blueprint $table) {
			$table->engine = "InnoDB";
			$table->foreign('examen_respuesta_id')->references('id')->on('ws_examen_respuesta')->onDelete('cascade');
			$table->foreign('pregunta_king_id')->references('id')->on('ws_preguntas_king')->onDelete('cascade');
			$table->foreign('pregunta_agrupada_id')->references('id')->on('ws_preguntas_agrupadas')->onDelete('cascade');
			$table->foreign('preg_traduc_id')->references('id')->on('ws_pregunta_traduc')->onDelete('cascade');
			$table->foreign('idioma_id')->references('id')->on('ws_idiomas')->onDelete('cascade');
			$table->foreign('opcion_id')->references('id')->on('ws_opciones')->onDelete('cascade');
			$table->foreign('opcion_agrupada_id')->references('id')->on('ws_opciones_agrupadas')->onDelete('cascade');
			$table->foreign('opcion_cuadricula_id')->references('id')->on('ws_opciones_cuadricula')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ws_respuestas');
		Schema::drop('ws_examen_respuesta');
	}

}
