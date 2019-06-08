import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { global } from '../../services/global'; 

@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})
export class UserEditComponent implements OnInit {

	public page_title: string;
	public user: User;
  public identity;
  public token;
  public status;
  public url;
  //Configuraciones para editor de texto enriquecido
  public froala_options: Object = {
    charCounterCount: true,
    language:'es',
      toolbarButtons: ['bold' , 'italic' , 'underline' , 'strikeThrough' , 'fontFamily' , 'fontSize' , '|' , 'paragraphStyle' , 'paragraphFormat' , 'align' , 'undo' , 'redo'],
      toolbarButtonsXS: ['bold' , 'italic' , 'underline' , 'strikeThrough' , 'fontFamily' , 'fontSize' , '|' , 'paragraphStyle' , 'paragraphFormat' , 'align' , 'undo' , 'redo'],
      toolbarButtonsSM: ['bold' , 'italic' , 'underline' , 'strikeThrough' , 'fontFamily' , 'fontSize' , '|' , 'paragraphStyle' , 'paragraphFormat' , 'align' , 'undo' , 'redo'],
      toolbarButtonsMD: ['bold' , 'italic' , 'underline' , 'strikeThrough' , 'fontFamily' , 'fontSize' , '|' , 'paragraphStyle' , 'paragraphFormat' , 'align' , 'undo' , 'redo'],
  };

//Configuraciones para subir imagen de avatar
  public afuConfig = {
    multiple: false,
    formatsAllowed: ".jpg, .png, .gif, .jpeg",
    maxSize: "50",
    uploadAPI:  {
      url: global.url+'user/upload',
      headers: {
        "Authorization" : this._userService.getToken()
      }
    },
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: true,
    hideSelectBtn: false,
    attachPinText:'Sube tu avatar de usuario'   
  
};

  constructor(
      private _userService: UserService
    ) {
  	this.page_title = 'Ajustes de usuario';
    this.user = new User(1,'','','ROLE_USER', '', '', '', '');
    //Rellenando los datos de la base
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.url = global.url;
    //Asignando los datos traidos a la variable local
    this.user = new User(
      this.identity.sub,
      this.identity.name,
      this.identity.surname,
      this.identity.role,
      this.identity.email,
       '',
      this.identity.description,
      this.identity.image
      );
  }

  ngOnInit() {
  }

  onSubmit(form){
    //Enviando la peticion de modificacion
    this._userService.update(this.token, this.user).subscribe(
        response => {
          if (response && response.status) {
          console.log(response);
          //console.log(this.identity);
           this.status = 'success';
           
           //Actualizar el usuario en la sesion
           if (response.Change.name) {
              this.user.name = response.Change.name;
           }

           if (response.Change.surname) {
              this.user.surname = response.Change.surname;
           }

           if (response.Change.email) {
              this.user.email = response.Change.email;
           }

           if (response.Change.description) {
              this.user.description = response.Change.description;
           }

           if (response.Change.image) {
              this.user.image = response.Change.image;
           }

           //Manteniendo los cambios y actualizando el local storage
           this.identity =this.user;
           localStorage.setItem('identity', JSON.stringify(this.identity));


          }else{
            this.status = 'Error';  
          }
          
        }, 
        error => {
          this.status = 'Error';    
          console.log(<any>error);
        }
     );
  }

  //Metodo para subida de avatar
  avatarUpload(datos){
    //Obteniendo los datos de la subida de imagen  
    let data = JSON.parse(datos.response);
    this.user.image = data.image;
  }
}
