import {Injectable} from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs'; //Los observables proporcionan soporte para pasar mensajes entre editores y suscriptores en la aplicación
import { Post } from '../models/post';
import { global } from './global';

@Injectable()
export class PostService{

	public url: string; 
	//public identity;
	//public token;

	constructor(
		private _http: HttpClient
	){
		this.url = global.url;
	}


	//Metodo para crear nuevos articulos
	create(token, post): Observable<any>{
		//Limpiar campo content (editor de texto enrquesido) htmlEntities > utf-8
		post.content = global.htmlEntities(post.content);
		let json = JSON.stringify(post);
		let params = "json="+json;
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded')
									   .set('Authorization', token);

		return this._http.post(this.url+'post', params, {headers: headers});


	}

	//Metodo para obtener todos los post de la base de datos para ser listados
	getPosts(): Observable<any>{
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded');
		return this._http.get(this.url+'post',  {headers: headers} );
	}

	//Meotod para obtener un unico post de la base de datos para ser mostrado
	getPost(id): Observable<any>{
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded');
		return this._http.get(this.url+'post/' + id,  {headers: headers} );
	}

	//Metodo para actualizar o modificar post
	update(token, post, id): Observable<any>{
		//Limpiar campo content (editor de texto enrquesido) htmlEntities > utf-8
		post.content = global.htmlEntities(post.content);
		
		let json = JSON.stringify(post);
		let params = "json="+json;

		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded')
										.set('Authorization', token);

		return this._http.put(this.url+ 'post/'+ id, params ,{headers: headers});
	}

	//Metodo para eliminar un post ya existente
	delete(token, id){
		let headers = new HttpHeaders().set('Content-type', 'application/x-www-form-urlencoded')
										.set('Authorization', token);

		return this._http.delete(this.url + 'post/' + id, {headers: headers});
	}
}