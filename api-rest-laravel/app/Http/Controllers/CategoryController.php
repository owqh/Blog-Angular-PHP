<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{
	public function __construct(){
		//Llamando el middleware de autentidicacion para todas los metodos con excepcion de algunos metodos
		//$this->middleware('nombre del middleware', ['excepcion' => ['metodo_excluido1', 'metodo_excluido2']]); 
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

	//Listando todas las categorias
    public function index (){
    	$categories = Category::all();

    	return response()->json([
    		'code'			=> 	200,
    		'status'		=> 'success',
    		'categories'	=>	$categories
    	]);
    }

    //Listando una categoria unica deacuerdo a lo solicitado
    public function show($id){
    	$category = Category::find($id);

    	if (is_object($category)) {
    		$data = array(
    		'code'			=> 	200,
    		'status'		=> 'success',
    		'categories'	=>	$category
    		);
    	}else{
    		$data = array(
    		'code'			=> 	400,
    		'status'		=> 'error',
    		'message'		=>	'No se encuentra la categoria'
    		);
    	}

    	return response()->json($data, $data['code']);
    }

    public function store(Request $request){
    	//Recoger los datos por post
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);


    	if (!empty($params_array)) {
    		//Validar los datos
	    	$validate=\Validator::make($params_array, [
	    		'name'	=> 'required'
	    	]);

	    	//Guardar la categoria
	    	if ($validate->fails()) {
	    		//Si la validacion falla
	    		$data = array(
	    		'code'			=> 	400,
	    		'status'		=> 'error',
	    		'message'		=>	'No se ha guardado la categoria'
	    		);
	    	}else{
	    		//Guardando en la base de datos
	    		$category = new Category();
	    		$category->name = $params_array['name'];
	    		$category->save();

	    		$data = array(
	    		'code'			=> 	200,
	    		'status'		=> 'success',
	    		'message'		=>	'Se ha guardado la categoria',
	    		'category'		=>	$category
	    		);
	    	}
    	}else{
    		$data = array(
	    		'code'			=> 	400,
	    		'status'		=> 'error',
	    		'message'		=>	'No se ha enviado ninguna categoria'
	    		);
    	}
    	
    	//Devolver el resultado

    	return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
    	//Recoger los datos por post
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if (!empty($params_array)) {
    		//Validar los datos
	    	$validate =\Validator::make($params_array, [
	    		'name' 	=> 'required'
	    	]);

	    	//quitar lo que no quiero actualizar
	    	unset($params_array['id']);
	    	unset($params_array['create_at']);

	    	//Actualizar la categoria
	    	$category = Category::where('id', $id)->update($params_array);

	    	$data = array(
	    		'code'			=> 	200,
	    		'status'		=> 'success',
	    		'message'		=>	'Se ha guardado la categoria',
	    		'category'		=>	$params_array
	    		);
    	}else{
    		$data = array(
	    		'code'			=> 	400,
	    		'status'		=> 'error',
	    		'message'		=>	'No se ha enviado ninguna categoria'
	    		);
    	}
    	
    	//Devolver el resultado
    	return response()->json($data, $data['code']);
    }
}
