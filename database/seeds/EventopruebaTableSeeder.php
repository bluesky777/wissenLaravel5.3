<?php

use Illuminate\Database\Seeder;


use App\Models\User;
use App\Models\Role;
use App\Models\Evento;
use App\Models\Idioma_registrado;
use App\Models\Entidad;

use App\Models\Categoria_king;
use App\Models\Disciplina_king;
use App\Models\Nivel_king;
use App\Models\Categoria_traduc;
use App\Models\Disciplina_traduc;
use App\Models\Nivel_traduc;
use App\Models\User_event;

use App\Models\Pregunta_king;
use App\Models\Pregunta_traduc;
use App\Models\Opcion;

use App\Models\Evaluacion;
use App\Models\Pregunta_evaluacion;




class EventopruebaTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creando evento de prueba ...');
		DB::table('ws_eventos')->delete();

		$NewEvent = new Evento;
		$NewEvent->id 					= 1;
		$NewEvent->nombre 				= 'Olimpiadas Académicas Fuiciosas 2015';
		$NewEvent->alias 				= 'OAF15';
		$NewEvent->descripcion 			= 'Olimpiadas para probar el programa Wissen System';
		$NewEvent->gran_final 			= false;
		$NewEvent->with_pay 			= true;
		$NewEvent->actual 				= true;
		$NewEvent->precio1 				= 2000;
		$NewEvent->precio2 				= 3000;
		$NewEvent->precio3 				= 4000;
		$NewEvent->precio4 				= 5000;
		$NewEvent->precio5 				= 5000;
		$NewEvent->precio6 				= 5000;
		$NewEvent->idioma_principal_id	= 1; // 1 es español
		$NewEvent->es_idioma_unico		= false; 
		$NewEvent->enable_public_chat	= false;
		$NewEvent->enable_private_chat	= false;
		$NewEvent->save();



		// Registramos los idiomas extras además del Español
		$idiom = new Idioma_registrado;
		$idiom->id 			= 1;
		$idiom->evento_id 	= 1;
		$idiom->idioma_id 	= 2;
		$idiom->save();

		$idiom = new Idioma_registrado;
		$idiom->id 			= 2;
		$idiom->evento_id 	= 1;
		$idiom->idioma_id 	= 3;
		$idiom->save();

		$idiom = new Idioma_registrado;
		$idiom->id 			= 3;
		$idiom->evento_id 	= 1;
		$idiom->idioma_id 	= 4;
		$idiom->save();





		$this->command->info('Creando entidades ...');
		DB::table('ws_entidades')->delete();

		$ent = new Entidad;
		$ent->id 			= 1;
		$ent->nombre 		= 'Liceo Adventista Libertad';
		$ent->alias 		= 'LAL';
		$ent->evento_id 	= 1;
		$ent->save();

		$ent = new Entidad;
		$ent->id 			= 2;
		$ent->nombre 		= 'Liceo Tame';
		$ent->alias 		= 'LiTame';
		$ent->evento_id 	= 1;
		$ent->save();

		$ent = new Entidad;
		$ent->id 			= 3;
		$ent->nombre 		= 'Instituto Oriental Femenino';
		$ent->alias 		= 'IOF';
		$ent->evento_id 	= 1;
		$ent->save();





		$this->command->info('Creando Categorías ...');
		DB::table('ws_disciplinas_traduc')->delete();
		DB::table('ws_niveles_traduc')->delete();
		DB::table('ws_categorias_traduc')->delete();
		DB::table('ws_disciplinas_king')->delete();
		DB::table('ws_niveles_king')->delete();
		DB::table('ws_categorias_king')->delete();

		$disc = new Disciplina_king;
		$disc->id 			= 1;
		$disc->nombre 		= 'Biología';
		$disc->evento_id 	= 1;
		$disc->save();

		$disc = new Disciplina_king;
		$disc->id 			= 2;
		$disc->nombre 		= 'Sociales';
		$disc->evento_id 	= 1;
		$disc->save();

		$disc = new Disciplina_king;
		$disc->id 			= 3;
		$disc->nombre 		= 'Religión';
		$disc->evento_id 	= 1;
		$disc->save();



		// Agregamos las traducciones de las disciplinas
		$disc = new Disciplina_traduc;
		$disc->id 			= 1;
		$disc->nombre 		= 'Biología';
		$disc->descripcion 	= 'Incluye química';
		$disc->disciplina_id = 1;
		$disc->idioma_id 	= 1; // Español
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 2;
		$disc->nombre 		= 'Biology';
		$disc->descripcion 	= 'Includes chemical';
		$disc->disciplina_id = 1;
		$disc->idioma_id 	= 2; // Inglés
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 3;
		$disc->nombre 		= 'Biologia';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 1;
		$disc->idioma_id 	= 3; // Portugués
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 4;
		$disc->nombre 		= 'Biologie';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 1;
		$disc->idioma_id 	= 4; // Francés
		$disc->traducido 	= true;
		$disc->save();


		$disc = new Disciplina_traduc;
		$disc->id 			= 5;
		$disc->nombre 		= 'Sociales';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 2;
		$disc->idioma_id 	= 1; // Español
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 6;
		$disc->nombre 		= 'Social';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 2;
		$disc->idioma_id 	= 2; // Inglés
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 7;
		$disc->nombre 		= 'Social';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 2;
		$disc->idioma_id 	= 3; // Portugués
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 8;
		$disc->nombre 		= 'Social';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 2;
		$disc->idioma_id 	= 4; // Francés
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 9;
		$disc->nombre 		= 'Religión';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 3;
		$disc->idioma_id 	= 1; // Español
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 10;
		$disc->nombre 		= 'Religion';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 3;
		$disc->idioma_id 	= 2; // Inglés
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 11;
		$disc->nombre 		= 'Religião';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 3;
		$disc->idioma_id 	= 3; // Portugués
		$disc->traducido 	= true;
		$disc->save();

		$disc = new Disciplina_traduc;
		$disc->id 			= 12;
		$disc->nombre 		= 'Religion';
		$disc->descripcion 	= '';
		$disc->disciplina_id = 3;
		$disc->idioma_id 	= 4; // Francés
		$disc->traducido 	= true;
		$disc->save();



		// NIVELES KING

		// NIVEL A
		$niv = new Nivel_king;
		$niv->id 			= 1;
		$niv->nombre 		= 'A';
		$niv->evento_id 	= 1;
		$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 1;
			$niv->nombre 		= 'A';
			$niv->nivel_id 		= 1;
			$niv->descripcion 	= 'De sexto a séptimo';
			$niv->idioma_id 	= 1; // Español
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 2;
			$niv->nombre 		= 'A';
			$niv->nivel_id 		= 1;
			$niv->descripcion 	= 'From 6 to 7';
			$niv->idioma_id 	= 2; // Inglés
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 3;
			$niv->nombre 		= 'A';
			$niv->nivel_id 		= 1;
			$niv->descripcion 	= '';
			$niv->idioma_id 	= 3; // Portugués
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 4;
			$niv->nombre 		= 'A';
			$niv->nivel_id 		= 1;
			$niv->descripcion 	= '';
			$niv->idioma_id 	= 4; // Francés
			$niv->traducido 	= true;
			$niv->save();


		// NIVEL B
		$niv = new Nivel_king;
		$niv->id 			= 2;
		$niv->nombre 		= 'B';
		$niv->evento_id 	= 1;
		$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 5;
			$niv->nombre 		= 'B';
			$niv->nivel_id 		= 2;
			$niv->descripcion 	= 'De Octavo a noveno';
			$niv->idioma_id 	= 1; // Español
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 6;
			$niv->nombre 		= 'B';
			$niv->nivel_id 		= 2;
			$niv->descripcion 	= 'From 8 to 9';
			$niv->idioma_id 	= 2; // Inglés
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 7;
			$niv->nombre 		= 'B';
			$niv->nivel_id 		= 2;
			$niv->descripcion 	= '';
			$niv->idioma_id 	= 3; // Portugués
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 8;
			$niv->nombre 		= 'B';
			$niv->nivel_id 		= 2;
			$niv->descripcion 	= '';
			$niv->idioma_id 	= 4; // Francés
			$niv->traducido 	= true;
			$niv->save();


		// NIVEL C
		$niv = new Nivel_king;
		$niv->id 			= 3;
		$niv->nombre 		= 'C';
		$niv->evento_id 	= 1;
		$niv->save();


			$niv = new Nivel_traduc;
			$niv->id 			= 9;
			$niv->nombre 		= 'C';
			$niv->nivel_id 		= 3;
			$niv->descripcion 	= 'De décimo a once';
			$niv->idioma_id 	= 1; // Español
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 10;
			$niv->nombre 		= 'C';
			$niv->nivel_id 		= 3;
			$niv->descripcion 	= 'From 10 to 11';
			$niv->idioma_id 	= 2; // Inglés
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 11;
			$niv->nombre 		= 'C';
			$niv->nivel_id 		= 3;
			$niv->descripcion 	= '';
			$niv->idioma_id 	= 3; // Portugués
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 12;
			$niv->nombre 		= 'C';
			$niv->nivel_id 		= 3;
			$niv->descripcion 	= '';
			$niv->idioma_id 	= 4; // Francés
			$niv->traducido 	= true;
			$niv->save();

		// NIVEL NIÑOS
		$niv = new Nivel_king;
		$niv->id 			= 4;
		$niv->nombre 		= 'Niños';
		$niv->evento_id 	= 1;
		$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 13;
			$niv->nombre 		= 'Niños';
			$niv->nivel_id 		= 4;
			$niv->descripcion 	= 'Niños de 9 a 17 años';
			$niv->idioma_id 	= 1; // Español
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 14;
			$niv->nombre 		= 'Children';
			$niv->nivel_id 		= 4;
			$niv->descripcion 	= 'Children 9 to 17 years';
			$niv->idioma_id 	= 2; // Inglés
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 15;
			$niv->nombre 		= 'Crianças';
			$niv->nivel_id 		= 4;
			$niv->descripcion 	= 'Crianças de 9 a 17 anos';
			$niv->idioma_id 	= 3; // Portugués
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 16;
			$niv->nombre 		= 'Enfants';
			$niv->nivel_id 		= 4;
			$niv->descripcion 	= 'Enfants de 9 à 17 ans';
			$niv->idioma_id 	= 4; // Francés
			$niv->traducido 	= true;
			$niv->save();




		// NIVEL JÓVENES
		$niv = new Nivel_king;
		$niv->id 			= 5;
		$niv->nombre 		= 'Jóvenes';
		$niv->evento_id 	= 1;
		$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 17;
			$niv->nombre 		= 'Jóvenes';
			$niv->nivel_id 		= 5;
			$niv->descripcion 	= 'Jóvenes de 18 a 30 años';
			$niv->idioma_id 	= 1; // Español
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 18;
			$niv->nombre 		= 'Children';
			$niv->nivel_id 		= 5;
			$niv->descripcion 	= 'Boys 18 to 30 years';
			$niv->idioma_id 	= 2; // Inglés
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 19;
			$niv->nombre 		= 'Crianças';
			$niv->nivel_id 		= 5;
			$niv->descripcion 	= 'Jovem de 9 a 17 anos';
			$niv->idioma_id 	= 3; // Portugués
			$niv->traducido 	= true;
			$niv->save();

			$niv = new Nivel_traduc;
			$niv->id 			= 20;
			$niv->nombre 		= 'Enfants';
			$niv->nivel_id 		= 5;
			$niv->descripcion 	= 'Jeune de 9 à 17 ans';
			$niv->idioma_id 	= 4; // Francés
			$niv->traducido 	= true;
			$niv->save();







		// CATEGORÍA BIOLOGÍA
		$cat = new Categoria_king;
		$cat->id 			= 1;
		$cat->nombre 		= 'Biología A';
		$cat->nivel_id 		= 1;
		$cat->disciplina_id = 1;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 1, 'Evaluación de Biología A', 20, 10, 1);

		$cat = new Categoria_king;
		$cat->id 			= 2;
		$cat->nombre 		= 'Biología B';
		$cat->nivel_id 		= 2;
		$cat->disciplina_id = 1;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 2, 'Evaluación de Biología B');

		$cat = new Categoria_king;
		$cat->id 			= 3;
		$cat->nombre 		= 'Biología C';
		$cat->nivel_id 		= 3;
		$cat->disciplina_id = 1;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 3, 'Evaluación de Biología C');



			$cat = new Categoria_traduc;
			$cat->id 			= 1;
			$cat->nombre 		= 'Biología A';
			$cat->abrev 		= 'BioA';
			$cat->categoria_id 	= 1;
			$cat->descripcion 	= 'Biología en español para grados sexto y séptimo';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 2;
			$cat->nombre 		= 'Biology A';
			$cat->abrev 		= 'BioA';
			$cat->categoria_id 	= 1;
			$cat->descripcion 	= 'Biology in english for gruops 6 and 7';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 3;
			$cat->nombre 		= 'Biologia A';
			$cat->abrev 		= 'BioA';
			$cat->categoria_id 	= 1;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 4;
			$cat->nombre 		= 'Biologie A';
			$cat->abrev 		= 'BioA';
			$cat->categoria_id 	= 1;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();




			$cat = new Categoria_traduc;
			$cat->id 			= 5;
			$cat->nombre 		= 'Biología B';
			$cat->abrev 		= 'BioB';
			$cat->categoria_id 	= 2;
			$cat->descripcion 	= 'Biología en español para grados OCTAVO y noveno';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 6;
			$cat->nombre 		= 'Biology B';
			$cat->abrev 		= 'BioB';
			$cat->categoria_id 	= 2;
			$cat->descripcion 	= 'Biology in english for gruops 8 and 9';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 7;
			$cat->nombre 		= 'Biologia B';
			$cat->abrev 		= 'BioB';
			$cat->categoria_id 	= 2;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 8;
			$cat->nombre 		= 'Biologie B';
			$cat->abrev 		= 'BioB';
			$cat->categoria_id 	= 2;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();




			$cat = new Categoria_traduc;
			$cat->id 			= 9;
			$cat->nombre 		= 'Biología C';
			$cat->abrev 		= 'BioC';
			$cat->categoria_id 	= 3;
			$cat->descripcion 	= 'Biología en español para grados sexto y séptimo';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 10;
			$cat->nombre 		= 'Biology C';
			$cat->abrev 		= 'BioC';
			$cat->categoria_id 	= 3;
			$cat->descripcion 	= 'Biology in english for gruops 6 and 7';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 11;
			$cat->nombre 		= 'Biologia C';
			$cat->abrev 		= 'BioC';
			$cat->categoria_id 	= 3;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 12;
			$cat->nombre 		= 'Biologie C';
			$cat->abrev 		= 'BioC';
			$cat->categoria_id 	= 3;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();





		// CATEGORÍA SOCIALES
		$cat = new Categoria_king;
		$cat->id 			= 4;
		$cat->nombre 		= 'Sociales A';
		$cat->nivel_id 		= 1;
		$cat->disciplina_id = 2;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 4, 'Evaluación de Biología A');

		$cat = new Categoria_king;
		$cat->id 			= 5;
		$cat->nombre 		= 'Sociales B';
		$cat->nivel_id 		= 2;
		$cat->disciplina_id = 2;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 5, 'Evaluación de Biología A');

		$cat = new Categoria_king;
		$cat->id 			= 6;
		$cat->nombre 		= 'Sociales C';
		$cat->nivel_id 		= 3;
		$cat->disciplina_id = 2;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 6, 'Evaluación de Biología A');



			$cat = new Categoria_traduc;
			$cat->id 			= 13;
			$cat->nombre 		= 'Sociales A';
			$cat->abrev 		= 'SocA';
			$cat->categoria_id 	= 4;
			$cat->descripcion 	= 'Sociales en español para grados sexto y séptimo';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 14;
			$cat->nombre 		= 'Sociales A';
			$cat->abrev 		= 'SocA';
			$cat->categoria_id 	= 4;
			$cat->descripcion 	= 'Social in english for gruops 6 and 7';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 15;
			$cat->nombre 		= 'Sociales A';
			$cat->abrev 		= 'SocA';
			$cat->categoria_id 	= 4;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 16;
			$cat->nombre 		= 'Sociales A';
			$cat->abrev 		= 'SocA';
			$cat->categoria_id 	= 4;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();




			$cat = new Categoria_traduc;
			$cat->id 			= 17;
			$cat->nombre 		= 'Sociales B';
			$cat->abrev 		= 'SocB';
			$cat->categoria_id 	= 5;
			$cat->descripcion 	= 'Sociales en español para grados OCTAVO y noveno';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 18;
			$cat->nombre 		= 'Sociales B';
			$cat->abrev 		= 'SocB';
			$cat->categoria_id 	= 5;
			$cat->descripcion 	= 'Sociales in english for gruops 8 and 9';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 19;
			$cat->nombre 		= 'Social B';
			$cat->abrev 		= 'SocB';
			$cat->categoria_id 	= 5;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 20;
			$cat->nombre 		= 'Social B';
			$cat->abrev 		= 'SocB';
			$cat->categoria_id 	= 5;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();




			$cat = new Categoria_traduc;
			$cat->id 			= 21;
			$cat->nombre 		= 'Sociales C';
			$cat->abrev 		= 'SocC';
			$cat->categoria_id 	= 6;
			$cat->descripcion 	= 'Sociales en español para grados décimo y once';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 22;
			$cat->nombre 		= 'Sociales C';
			$cat->abrev 		= 'SocC';
			$cat->categoria_id 	= 6;
			$cat->descripcion 	= 'Sociales in english for gruops 10 and 11';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 23;
			$cat->nombre 		= 'Sociales C';
			$cat->abrev 		= 'SocC';
			$cat->categoria_id 	= 6;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 24;
			$cat->nombre 		= 'Sociales C';
			$cat->abrev 		= 'SocC';
			$cat->categoria_id 	= 6;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();



		// CATEGORÍA RELIGIÓN
		$cat = new Categoria_king;
		$cat->id 			= 7;
		$cat->nombre 		= 'Religión Niños';
		$cat->nivel_id 		= 4;
		$cat->disciplina_id = 3;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 7, 'Evaluación de Religión Niños');

		$cat = new Categoria_king;
		$cat->id 			= 8;
		$cat->nombre 		= 'Religión Jóvenes';
		$cat->nivel_id 		= 5;
		$cat->disciplina_id = 3;
		$cat->evento_id 	= 1;
		$cat->save();
		Evaluacion::crearPrimera(1, 8, 'Evaluación de Religión Jóvenes');




			$cat = new Categoria_traduc;
			$cat->id 			= 25;
			$cat->nombre 		= 'Religión Niños';
			$cat->abrev 		= 'RelN';
			$cat->categoria_id 	= 7;
			$cat->descripcion 	= 'Religión en español para niños';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 26;
			$cat->nombre 		= 'Religión Niños';
			$cat->abrev 		= 'RelN';
			$cat->categoria_id 	= 7;
			$cat->descripcion 	= 'Religion in english for children';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 27;
			$cat->nombre 		= 'Religião Crianças';
			$cat->abrev 		= 'RelN';
			$cat->categoria_id 	= 7;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 28;
			$cat->nombre 		= 'Religion enfants';
			$cat->abrev 		= 'RelN';
			$cat->categoria_id 	= 7;
			$cat->descripcion 	= '';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();



			$cat = new Categoria_traduc;
			$cat->id 			= 29;
			$cat->nombre 		= 'Religión Juvenil';
			$cat->abrev 		= 'RelJ';
			$cat->categoria_id 	= 8;
			$cat->descripcion 	= 'Religión en español para jóvenes';
			$cat->idioma_id 	= 1; // Español
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 30;
			$cat->nombre 		= 'Religion Juvenil';
			$cat->abrev 		= 'RelJ';
			$cat->categoria_id 	= 8;
			$cat->descripcion 	= 'Religion in english for young';
			$cat->idioma_id 	= 2; // Inglés
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 31;
			$cat->nombre 		= 'Nova religião';
			$cat->abrev 		= 'RelJ';
			$cat->categoria_id 	= 8;
			$cat->descripcion 	= 'Português juventude religião';
			$cat->idioma_id 	= 3; // Portugués
			$cat->traducido 	= true;
			$cat->save();

			$cat = new Categoria_traduc;
			$cat->id 			= 32;
			$cat->nombre 		= 'la religion jeunesse';
			$cat->abrev 		= 'RelJ';
			$cat->categoria_id 	= 8;
			$cat->descripcion 	= 'la religion de la jeunesse';
			$cat->idioma_id 	= 4; // Francés
			$cat->traducido 	= true;
			$cat->save();








		// PREGUNTAS
		$this->command->info('Creando Preguntas King ...');
		DB::table('ws_opciones')->delete();
		DB::table('ws_pregunta_traduc')->delete();
		DB::table('ws_preguntas_king')->delete();

		$preg = new Pregunta_king;
		$preg->id 			= 1;
		$preg->categoria_id = 1;
		$preg->descripcion 	= 'Primera pregunta';
		$preg->duracion 	= 20; // segundos
		$preg->puntos 		= true;
		$preg->tipo_pregunta = "Test";
		$preg->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 1;
			$preg->enunciado 	= "<p>Cu&aacute;nto es 2+2</p>";
			$preg->ayuda 		= 'Tienes que saber sumar para responder esta.';
			$preg->pregunta_id 	= 1;
			$preg->idioma_id	= 1; // Español
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 1;
				$opc->definicion 		= "Cuatro";
				$opc->is_correct 		= true;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 1;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 2;
				$opc->definicion 		= "Tres";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 1;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 3;
				$opc->definicion 		= "Cinco";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 1;
				$opc->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 2;
			$preg->enunciado 	= "<p>How much <strong>is 2+2</strong></p>";
			$preg->ayuda 		= 'You have to know';
			$preg->pregunta_id 	= 1;
			$preg->idioma_id	= 2; // Inglés
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 4;
				$opc->definicion 		= "Four";
				$opc->is_correct 		= true;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 2;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 5;
				$opc->definicion 		= "Three";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 2;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 6;
				$opc->definicion 		= "Five";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 2;
				$opc->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 3;
			$preg->enunciado 	= "<p><strong><u>Quanto</u></strong> &eacute; 2 + 2?</p>";
			$preg->ayuda 		= 'Você tem que saber como adicionar.';
			$preg->pregunta_id 	= 1;
			$preg->idioma_id	= 3; // Portugués
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 7;
				$opc->definicion 		= "quatro";
				$opc->is_correct 		= true;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 3;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 8;
				$opc->definicion 		= "três";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 3;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 9;
				$opc->definicion 		= "cinco";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 3;
				$opc->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 4;
			$preg->enunciado 	= "<p>Combien font 2 + 2? en Franc&eacute;s :P</p>";
			$preg->ayuda 		= 'Vous devez savoir comment ajouter.';
			$preg->pregunta_id 	= 1;
			$preg->idioma_id	= 4; // Francés
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 10;
				$opc->definicion 		= "quatre";
				$opc->is_correct 		= true;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 4;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 11;
				$opc->definicion 		= "trois";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 4;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 12;
				$opc->definicion 		= "cinq";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 4;
				$opc->save();



		$preg = new Pregunta_king;
		$preg->id 			= 2;
		$preg->categoria_id = 1;
		$preg->descripcion 	= 'Segunda pregunta';
		$preg->duracion 	= 20; // segundos
		$preg->puntos 		= true;
		$preg->tipo_pregunta = "Test";
		$preg->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 5;
			$preg->enunciado 	= "<p>Otra, Cu&aacute;nto es 4+5</p>";
			$preg->ayuda 		= 'Tienes que saber sumar para responder esta.';
			$preg->pregunta_id 	= 2;
			$preg->idioma_id	= 1; // Español
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 13;
				$opc->definicion 		= "Cuatro";
				$opc->is_correct 		= true;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 5;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 14;
				$opc->definicion 		= "Tres";
				$opc->is_correct 		= false;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 5;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 15;
				$opc->definicion 		= "Cinco";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 5;
				$opc->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 6;
			$preg->enunciado 	= "<p>Otra, How much <strong>is 2+2</strong></p>";
			$preg->ayuda 		= 'You have to know';
			$preg->pregunta_id 	= 2;
			$preg->idioma_id	= 2; // Inglés
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 16;
				$opc->definicion 		= "Four";
				$opc->is_correct 		= true;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 6;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 17;
				$opc->definicion 		= "Three";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 6;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 18;
				$opc->definicion 		= "Five";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 6;
				$opc->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 7;
			$preg->enunciado 	= "<p><strong><u>Quanto</u></strong> &eacute; 2 + 2?</p>";
			$preg->ayuda 		= 'Você tem que saber como adicionar.';
			$preg->pregunta_id 	= 2;
			$preg->idioma_id	= 3; // Portugués
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 19;
				$opc->definicion 		= "quatro";
				$opc->is_correct 		= true;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 7;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 20;
				$opc->definicion 		= "três";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 7;
				$opc->save();
				$opc = new Opcion;

				$opc->id 				= 21;
				$opc->definicion 		= "cinco";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 7;
				$opc->save();

			$preg = new Pregunta_traduc;
			$preg->id 			= 8;
			$preg->enunciado 	= "<p>Combien font 2 + 2? en Franc&eacute;s :P</p>";
			$preg->ayuda 		= 'Vous devez savoir comment ajouter.';
			$preg->pregunta_id 	= 2;
			$preg->idioma_id	= 4; // Francés
			$preg->traducido	= true;
			$preg->save();

				$opc = new Opcion;
				$opc->id 				= 22;
				$opc->definicion 		= "quatre";
				$opc->is_correct 		= true;
				$opc->orden 			= 0;
				$opc->pregunta_traduc_id = 8;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 23;
				$opc->definicion 		= "trois";
				$opc->is_correct 		= false;
				$opc->orden 			= 1;
				$opc->pregunta_traduc_id = 8;
				$opc->save();

				$opc = new Opcion;
				$opc->id 				= 24;
				$opc->definicion 		= "cinq";
				$opc->is_correct 		= false;
				$opc->orden 			= 2;
				$opc->pregunta_traduc_id = 8;
				$opc->save();



		// Añadir preguntas a la evaluación
		$pregeva = new Pregunta_evaluacion;
		$pregeva->id 				= 1;
		$pregeva->evaluacion_id 	= 1;
		$pregeva->pregunta_id 		= 1;
		$pregeva->orden 			= 1;
		$pregeva->aleatorias 		= 0;
		$pregeva->save();

		$pregeva = new Pregunta_evaluacion;
		$pregeva->id 				= 2;
		$pregeva->evaluacion_id 	= 1;
		$pregeva->pregunta_id 		= 2;
		$pregeva->orden 			= 2;
		$pregeva->aleatorias 		= 0;
		$pregeva->save();














		DB::table('ws_user_event')->delete();
		$us_ev 	= new User_event;
		$us_ev->user_id 	= 2;
		$us_ev->evento_id 	= 1;
		$us_ev->pagado 		= 1000;
		$us_ev->pazysalvo 	= false;
		$us_ev->save();

		$us_ev 	= new User_event;
		$us_ev->user_id 	= 3;
		$us_ev->evento_id 	= 1;
		$us_ev->pagado 		= 2000;
		$us_ev->pazysalvo 	= true;
		$us_ev->save();

		$parti = Role::find(5);

		$user = User::find(2);
		$user->attachRole($parti);

		$user = User::find(3);
		$user->attachRole($parti);




    }
}


