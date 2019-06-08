<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    //Metodo para registrar usuarios
    public function register(Request $request){
    	//Recoger datos del usuario por POST
    	$json = $request->input('json', null);
    	$params=json_decode($json); //Decodifico el json recibido y creo objeto 
    	$params_array=json_decode($json, true); //Decodifico el json recibido y creo arreglo 
		

		//Validar que el json si traiga datos
    	if (!empty($params) && !empty($params_array)) {
    		//Limpiar espacios en blanco
	    	$params_array =array_map('trim', $params_array);
    		//Validar datos con laravel
	    	$validate=\Validator::make($params_array, [
	    		'name' 		=> 'required|alpha',
	    		'surname'	=> 'required|alpha',
	    		'email'		=> 'required|email|unique:users', //Unique:tablaBD comprueba si el usuario ya existe o duplicado
	    		'password'	=> 'required'
	    	]); 

	    	if ($validate->fails()) {
	    		//Falla en validacion
	    		$data = array(
	    			'status' => 'error', 
	    			'code' => 400, 
	    			'message' => 'El usuario no se ha creado.', 
	    			'errors' => $validate->errors() 
	    		);
	    	}else{
	    		/* --------Validacion pasada correctamente-------*/

	    		//Cifrar la contraseña (metodo de encriptacion, variable)
	    		$pwd = hash('sha256', $params->password);
 
		    	//Crear el usuario
		    	$user=new User();
		    	$user->name = $params_array['name'];
		    	$user->surname = $params_array['surname'];
		    	$user->email = $params_array['email'];
		    	$user->password = $pwd;
		    	$user->role = 'ROLE_USER';

		    	//Guardar el usuario con laravel
		    	$user->save();

		    	//Dar una respuesta 
	    		$data = array(
	    			'status' => 'success', 
	    			'code' => 200, 
	    			'message' => 'El usuario se ha creado correctamente.',
	    			'user' => $user 
	    		);
	    	}
	    	} else{
	    		$data = array(
	    			'status' => 'error', 
	    			'code' => 400, 
	    			'message' => 'Los datos enviados no son correctos'
	    		);
	    	}
		return response()->json($data, $data['code']);
    }

    //Metodo para iniciar sesion en la aplicacion
    public function login(Request $request){

    	$jwtAuth = new \App\Helpers\JwtAuth();
    	
    	//Recibir el datos por POST
    	$json = $request->input('json', null);
    	$params=json_decode($json); //Decodifico el json recibido y creo objeto 
    	$params_array=json_decode($json, true); //Decodifico el json recibido y creo arreglo 
    	
    	//Validar datos con laravel
	    	$validate=\Validator::make($params_array, [
	    		'email'		=> 'required|email',
	    		'password'	=> 'required'
	    	]); 

	    	if ($validate->fails()) {
	    		//Falla en validacion
	    		$signup = array(
	    			'status' => 'error', 
	    			'code' => 400, 
	    			'message' => 'El usuario no se ha podido identificar.', 
	    			'errors' => $validate->errors() 
	    		);
	    	}else{
	    		//Cifrar la contraseña
	    		
	    		$pwd = hash('sha256', $params->password);

    			//-------------Devolver token o datos----------
    			//TOKEN
    			$signup = $jwtAuth->signup($params->email, $pwd);
    			if (!empty($params->gettoken)) {
    				//Datos
    				$signup = $jwtAuth->signup($params->email, $pwd, true);
    			}
	    	}

    	return response()->json($signup, 200);
    }
    //Metodo para actualizar datos del usuario 
    public function update(Request $request){
    	//Comprobar si el usuario esta identificado

    	//Recojer el token de la cabezera del sitio
    	$token = $request->header('Authorization');
    	$jwtAuth = new \App\Helpers\JwtAuth();
    	$chekToken = $jwtAuth->chekToken($token);

    	//Recoger los datos por post
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true); //Decodifico el json recibido y creo arreglo 

    	if ($chekToken && !empty($params_array)) {
    		//---------Actualizar el usuario--------------

    		//Sacar usuario identificado
    		$user = $jwtAuth->chekToken($token, true);

    		//Validar los datos
    		$validate = \Validator::make($params_array, [
    			'name' 		=> 'required|alpha',
	    		'surname'	=> 'required|alpha',
	    		'email'		=> 'required|email|unique:users,'.$user->sub, //Unique:tablaBD comprueba si el usuario ya existe o duplicado con excepcion del correo actual
	    		'password'	=> 'required'
    		]);
    		//Quitar los campos que no quiero actualizar 
    		unset($params_array['id']);
    		unset($params_array['role']);
    		unset($params_array['password']);
    		unset($params_array['created_at']);
    		unset($params_array['remember_token']);

    		//Actualizar el usario en la BD
    		$user_update = User::where('id', $user->sub)->update($params_array);

    		//Devolver un array con resultado
    		$data = array(
	    			'status' => 'success', 
	    			'code' => 200, 
	    			'Usuario' => $user,
	    			'Change' => $params_array
	    		);

    	}else{
    		//Si no esta identificado devolver error
    		$data = array(
	    			'status'	=> 	'error', 
	    			'code' 		=> 	400, 
	    			'message' 	=> 	'El usuario no se ha podido identificar.'
	    		);
    	}
    	return response()->json($data, $data['code']);
    }

    //Metodo para subir una foto 
    public function upload(Request $request){
    	//Recoger datos de la peticion
    	$image =$request->file('file0');

    	//Validacion que sea una imagen
    	$validate = \Validator::make($request->all(), [
    		'file0'		=>	'required|image|mimes:jpg,jpeg,png,gif'
    	]);

    	//Subir y guardar la imagen
    	if (!$image || $validate->fails()) {
    		$data = array(
    		'status' 	=> 	'error', 
	    	'code' 		=> 	400, 
	    	'message' 	=> 	'Error al subir imagen.'
    	);	

    	}else{
        	//Creando el nombre unico de la imagen 
    		$image_name = time().$image->getClientOriginalName();
    		//Guaradando la imagen
    		\Storage::disk('users')->put($image_name, \File::get($image));

    		$data = array(
    		'code'		=>	200,
    		'status' 	=> 	'success',
    		'image' 	=>	$image_name
    	);
    	}   	
    	return response()->json($data, $data['code']);
    }

    //funcion para sacar una imagen de usuario
    public function getImage($filename){
    	//Comprobar que el archivo exista
    	$isset = \Storage::disk('users')->exists($filename);

    	if ($isset) {
    		//Obtener el archivo
    		$file = \Storage::disk('users')->get($filename);

    		return new Response($file, 200);
    	}else{
    		$data = array(
    		'code'		=>	400,
    		'status' 	=> 	'error',
    		'message' 	=>	'La imagen no existe'
    	);
    		return response()->json($data, $data['code']);
    	}
    } 

    public function detail($id){
    	$user = User::find($id);

    	if (is_object($user)) {
    		$data = array(
    			'code'		=>	200,
    			'status'	=>	'success',
    			'user'		=>	$user
    		);
    	}else{
    		$data = array(
    			'code'		=>	400,
    			'status'	=>	'error',
    			'message'	=>	'El usuario no existe.'
    		);
    	}

    	return response()->json($data, $data['code']);
    }
}
