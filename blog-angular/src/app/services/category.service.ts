import {Injectable} from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs'; //Los observables proporcionan soporte para pasar mensajes entre editores y suscriptores en la aplicación
import { Category } from '../models/category';
import { global } from './global';

@Injectable()
export class CategoryService{

	public url: string; 
	//public identity;
	//public token;

	constructor(
		private _http: HttpClient
	){
		this.url = global.url;
	}

	//Metodo para crear una nueva categoria
	create(token, category): Observable<any>{
		let json = JSON.stringify(category);
		let params = "json="+json;

		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
										.set('Authorization', token);

		//Devolver la peticion
		return this._http.post(this.url + 'category', params, {headers: headers});
	}

	//Metodo para listar todas las categorias
	getCategories(): Observable<any>{
		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

		return  this._http.get(this.url + 'category', {headers: headers});
	}

	//Metodo para listar una categoria
	getCategory(id): Observable<any>{
		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

		return  this._http.get(this.url + 'category/' + id, {headers: headers});
	}

	//Metodo para listar los post de una categoria
	getPosts(id): Observable<any>{
		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

		return  this._http.get(this.url + 'post/category/' + id, {headers: headers});
	}
}