<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampamentoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		Schema::create('ca_usuarios', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre_completo');
            $table->string('username')->unique();
            $table->string('password')->default('');
            $table->boolean('active')->default(true);
            $table->string('grado');
            $table->timestamps();
        });


		Schema::create('ca_mensajes', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->integer('emisor_id'); 
            $table->integer('receptor_id')->nullable();
            $table->string('mensaje')->default('');
			$table->boolean('leido');
            $table->timestamps();
        });


		Schema::create('ca_actividades', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('nombre');
            $table->integer('Sexto')->default(0);
            $table->integer('Septimo')->default(0);
            $table->integer('Octavo')->default(0);
            $table->integer('Noveno')->default(0);
            $table->integer('Decimo')->default(0);
            $table->integer('Once')->default(0);
            $table->timestamps();
        });

		Schema::create('ca_penalizaciones', function(Blueprint $table) {
			$table->engine = "InnoDB";
            $table->increments('id');
            $table->string('descripcion');
            $table->integer('Sexto')->default(0);
            $table->integer('Septimo')->default(0);
            $table->integer('Octavo')->default(0);
            $table->integer('Noveno')->default(0);
            $table->integer('Decimo')->default(0);
            $table->integer('Once')->default(0);
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
		Schema::drop('ca_mensajes');
		Schema::drop('ca_usuarios');
		Schema::drop('ca_penalizaciones');
		Schema::drop('ca_actividades');
	}

}
