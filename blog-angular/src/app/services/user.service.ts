import {Injectable} from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs'; //Los observables proporcionan soporte para pasar mensajes entre editores y suscriptores en la aplicación
import { User } from '../models/user';
import { global } from './global';

@Injectable()
export class UserService{

	public url: string; 
	public identity;
	public token;

	constructor(
		public _http: HttpClient
	){
		this.url = global.url;
	}

	//Metodo para registrar un usuario enviandose al backend
	register(user): Observable<any> {
		//El método JSON.stringify() convierte un objeto o valor de JavaScript en una cadena de texto JSON
		let json = JSON.stringify(user);
		//Envio al backend la variable 'json' (ya que asi especificamos que se llama en el backend )
		//mas la conversion de datos en la variable json actual!
		let params = 'json='+json;
		//Enviando los datos como un formulario HTML normal
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded');

		//Haciendo la peticion ajax this._http.metodo(post,get,put)(URL de peticion, parametros, cabeceras)
		return this._http.post(this.url+'register', params, {headers: headers});
	}

	//Metodo para iniciar sesion 
	signup(user, gettoken = null): Observable<any> {
		//Validar que venga get token
		if (gettoken != null) {
			user.gettoken = 'true';
		}

		let json = JSON.stringify(user);
		let params = 'json='+json;
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded');

		return this._http.post(this.url+'login', params, {headers: headers});
	}

	//Metodo para actualizar datos del usuario
	update(token, user): Observable<any> {
		//Limpiar campo description (editor de texto enriquesido) htmlEntities > utf8
		user.description = global.htmlEntities(user.description);

		//Sacar la informacion y combertirla en json
		let json = JSON.stringify(user);
		let params = 'json='+json;
		//Creando las cabeceras
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded')
									   .set('Authorization', token);
		//Haciendo la peticion
		return this._http.put(this.url + 'user/update', params , {headers : headers});
	}

	//Metodo para obtener la identidad del usario desde el localStorage
	getIdentity(){
		//Obteniendo los datos y combirtiendolos a json
		let identity = JSON.parse(localStorage.getItem('identity'));

		if (identity && identity != 'undefined') {
			this.identity = identity;
		}else{
			this.identity = null;
		}

		return this.identity;
	}

	//Metodo para obtner el token del usuario
	getToken(){
		let token = localStorage.getItem('token');

		if (token && token != 'undefined' ) {
			this.token = token;
		}else{
			this.token =null;
		}
		return this.token;
	}

	//Metodo para listar los post de un usuario
	getPosts(id): Observable<any>{
		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

		return  this._http.get(this.url + 'post/user/' + id, {headers: headers});
	}


	//Metodo para sacar informacion de un usuario
	getUser(id): Observable<any>{
		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

		return  this._http.get(this.url + 'user/detail/' + id, {headers: headers});
	}


}