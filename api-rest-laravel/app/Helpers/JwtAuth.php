<?php

namespace App\Helpers;

use Firebase\JWT\JWT; //Para utilizar el token de JWT
use Illuminate\Support\Facades\DB; //Para hacer consulta a la BD laravel
use App\User;


class JwtAuth {
	//variable para la key de JWT
	public $key;
	//Asignado valor a la key (puede ser cualquier cosa)
	public function __construct()
	{
		$this->key = "Esto_es_una_clave_super_secreta-99887766";
	}

	public function signup($email, $password, $getToken = null){
		//Buscar si existe el usuario con sus credenciales a travez de laravel
		$user = User::where([
			'email' 	=> $email,
			'password' 	=> $password
		])->first(); 

		//Comprobar si son correctas(objeto)
		$signup = false;
		if (is_object($user)) {
			$signup = true;
		}

		//Generar el TOKEN con los datos del usuario identificado
		//Si el la variable $signup es true entonces se crea el token
		if ($signup) {
			$token = array(
				'sub' 		=> 	$user->id,
				'email'		=> 	$user->email,
				'name'		=>	$user->name,
				'surname'	=>	$user->surname,
				'description'=>	$user->description,
				'image'		=> 	$user->image,
				'iat'		=> 	time(), //iat es para la fecha y hora que se crea
				'exp'		=>	time() + (7 * 24 * 60 * 60) //Fecha de vencimiento [actual + (calculo de segundos)]
			);

			//Creando el token se llama la clase y se pasan los valores del tokens, la llave y el cifrado
			$jwt = JWT::encode($token, $this->key, 'HS256');

			//Decodificar el token
			//var_dump($token); die();
			$decoded = JWT::decode($jwt, $this->key, ['HS256'] );

			//Devolver los datos decodificados o el TOKEN, en función de un parametro
			if (is_null($getToken)) {
				$data = $jwt;
			}else{
				$data = $decoded;
			}
		}else{
			$data = array(
				'status' 	=> 'error',
				'message' 	=> 'Login in correcto.'
			);
		}
		return $data;
} 
	//Funcion que servira para verificar el token del usuario
	public function chekToken($jwt, $getIdentity=false){
		$auth = false; //Variable de autentificacion en falso

		//Decodificando el token y capturando posibles errores 
		try {
			//Limpianndo la cabecera si trae comillas dobles las elimino str_replace(antiguo valor, nuevo valor, variable)
			$jwt = str_replace('"','', $jwt); 
			$decoded= JWT::decode($jwt, $this->key, ['HS256']);
		} catch (\UnexpectedValueException $e) {
			$auth = false;
		} catch (\DomainException $e){
			$auth = false;
		}

		//Validando que la decodificacion no este vacia, sea un objeto y si tengo guardado el id de usuario
		if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
			//Aceptando el token
			$auth = true;
		}else{
			//Negando el token
			$auth = false;
		}

		//Si trae un identity devolver los datos decodificados
		if ($getIdentity) {
			return $decoded;
		}
		return $auth;
	}
}




  ?>