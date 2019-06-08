import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'], 
  providers: [UserService]
})
export class RegisterComponent implements OnInit {

	public page_title: string;
  public user: User;
  public status: string;

  constructor(
    private _userService: UserService
    ) { 
  	this.page_title = "Unete a la comunidad mas grande!";
    this.user = new User(1,'','','ROLE_USER', '', '', '', '');
  }
  

  ngOnInit() {
 
  }

  onSubmit(form){
    //console.log(this.user);
    //Registrando al usuario
    //Sevicio_creado.metodo(parametros).metodo_de_Observable
    this._userService.register(this.user).subscribe(
      //Funcion que recibe respuesta
      response =>{
        //Validando que todo este bien
        if (response.status == "success") {
          this.status = response.status;
          //Limpia el formulario al ser enviado
          form.reset();
        }else{
          this.status = 'error';
        }
        
        
      },
      //funcion que recibe error
      error =>{
        this.status = 'error';
        console.log(<any>error);
      }
      );
    
  }

}
