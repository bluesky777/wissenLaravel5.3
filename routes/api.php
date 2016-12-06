<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

	AdvancedRoute::controller('welcome', 'WelcomeController');
	AdvancedRoute::controller('usuarios', 'UsersController');

	AdvancedRoute::controller('idiomas', 'IdiomasController');

	AdvancedRoute::controller('entidades', 'EntidadesController');
	AdvancedRoute::controller('disciplinas', 'DisciplinasController');
	AdvancedRoute::controller('disciplinas_traduc', 'Disciplinas_traducController');
	AdvancedRoute::controller('niveles', 'NivelesController');
	AdvancedRoute::controller('niveles_traduc', 'Niveles_traducController');
	AdvancedRoute::controller('categorias', 'CategoriasController');
	AdvancedRoute::controller('categorias_traduc', 'Categorias_traducController');
	AdvancedRoute::controller('chat', 'ChatController');
	AdvancedRoute::controller('eventos', 'EventosController');
	AdvancedRoute::controller('qr', 'QrCodesController');


	AdvancedRoute::controller('nivel_participantes', 'Nivel_participanteController');
	AdvancedRoute::controller('inscripciones', 'InscripcionesController');
	AdvancedRoute::controller('user_event', 'User_eventController');
	AdvancedRoute::controller('perfiles', 'PerfilController');
	AdvancedRoute::controller('idiomas', 'IdiomasController');
	AdvancedRoute::controller('examenes_respuesta', 'Examenes_respuestaController');
	AdvancedRoute::controller('respuestas', 'RespuestasController');
	AdvancedRoute::controller('imagenes', 'ImagesController');

	AdvancedRoute::controller('evaluaciones', 'EvaluacionesController');
	AdvancedRoute::controller('preguntas', 'Preguntas_kingController');
	AdvancedRoute::controller('pregunta_traduc', 'Pregunta_traducController');
	AdvancedRoute::controller('opciones', 'OpcionesController');
	AdvancedRoute::controller('grupo_preguntas', 'Grupo_preguntasController');
	AdvancedRoute::controller('contenido_traduc', 'Contenido_traducController');
	AdvancedRoute::controller('preguntas_agrupadas', 'Preguntas_agrupadasController');
	AdvancedRoute::controller('opciones_agrupadas', 'Opciones_agrupadasController');
	AdvancedRoute::controller('pregunta_evaluacion', 'Pregunta_evaluacionController');
	AdvancedRoute::controller('informes', 'InformesController');

	AdvancedRoute::controller('login', 'LoginController');
	AdvancedRoute::controller('roles', 'RolesController');
