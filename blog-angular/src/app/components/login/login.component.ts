import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {
	public page_title: string;
	public user: User;
	public status: string;
	public token;
	public identity;

  constructor(
  	 private _userService: UserService,
  	 private _router: Router,
  	 private _route: ActivatedRoute
  	) { 
  	this.page_title = 'Comparte con el mundo!!';
  	this.user = new User(1,'','','ROLE_USER', '', '', '', '');
  }

  ngOnInit() {
  	//Se ejectua siempre y cierra sesion solo cuando le llega el parametro "sure" por la url
  	this.logout();
  }

  onSubmit(form){
  	this._userService.signup(this.user).subscribe(
  		response => {
  			//Recibiendo el token
  			if (response.status != 'error') {
  				this.status = 'success';
  				this.token = response;

  				//Objeto del usuario identificado
  				this._userService.signup(this.user, true).subscribe(
			  		response => {
			  				this.identity = response;
			  				//Utilizando el localStorage del navegador para persistir la sesion del usuario
			  				localStorage.setItem('token', this.token);
			  				localStorage.setItem('identity', JSON.stringify(this.identity));

			  				//Redireccion a inicio
  							this._router.navigate(['inicio']);
			  		},
			  		error => {
			  			this.status = 'error';
			  			console.log(<any>error);
			  		}
			  	);

  			}else{
  				this.status = 'error';
  			}

  		},
  		error => {
  			this.status = 'error';
  			console.log(<any>error);
  		}
  	);
  }
 //Metodo para cerrar sesion
  logout(){
  	this._route.params.subscribe(
  		params => {
  			//declaro variable y con el "+" lo hago un numero
  			let logout = +params['sure'];

  			//Verificaar parametros y cerrar sesion
  			if (logout == 1) {
  				//Vaciando local storage y variables
  				localStorage.removeItem('identity');
  				localStorage.removeItem('token');

  				this.identity = null;
  				this.token = null;

  				//Redireccion a inicio
  				this._router.navigate(['inicio']);
  			}
  		},

  	);
  }

}
