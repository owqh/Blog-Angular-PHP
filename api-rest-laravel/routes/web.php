<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
| Los Middleware se caragan antes de las funciones.
| middleware(\App\Http\Middleware\ApiAuthMiddleware::class) Sirve para autentificar al usuario
*/

//CARGANDO CLASES
use App\Http\Middleware\ApiAuthMiddleware; 

Route::get('/', function () {
    return view('welcome');
});



Route::get('/test-orm', 'PruebasController@testOrm');

//Rutas del Appi
	//Rutas de prueba
	/*Route::get('/usuario/pruebas', 'UserController@pruebas');
	Route::get('/categoria/pruebas', 'CategoryController@pruebas');
	Route::get('/entrada/pruebas', 'PostController@pruebas');
	//RUTAS DE PRUEBA
	Route::get('/pruebas', function(){
		return '<h2>Texto desde la ruta</h2>';
	});
	*/

	//Rutas del controlador de usuarios 
	Route::post('/api/register', 'UserController@register');
	Route::post('/api/login', 'UserController@login');
	Route::put('/api/user/update', 'UserController@update');
	Route::post('/api/user/upload', 'UserController@upload')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class); 
	Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
	Route::get('/api/user/detail/{id}', 'UserController@detail');

	//Rutas del controlador de categorias (Utilizando rutas tipo resourse para no tener que escribirlas todas)
	Route::resource('api/category', 'CategoryController');

	//Rutas del controlador de entradas (Utilizando rutas tipo resourse para no tener que escribirlas todas)
	Route::resource('api/post', 'PostController');
	Route::post('/api/post/upload', 'PostController@upload');
 	Route::get('/api/post/image/{filename}', 'PostController@getImage');
 	Route::get('/api/post/category/{id}', 'PostController@getPostsByCategory');
 	Route::get('/api/post/user/{id}', 'PostController@getPostsByUser');