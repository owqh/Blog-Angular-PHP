import { Component, OnInit, DoCheck } from '@angular/core';
import { UserService } from './services/user.service';
import { CategoryService } from './services/category.service';
import { global } from './services/global'; 

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers: [UserService, CategoryService]
})
export class AppComponent implements OnInit, DoCheck {
  public title = 'blog-angular';
  public identity;
  public token;
  public url;
  public categories;
  public status;

  constructor(
  	private _userService: UserService,
    private _categoryService: CategoryService
  	){
  		this.loadUser();
      this.url = global.url;
  }

  //Metodo que se ejecuta al iniciar el programa
  ngOnInit(){
    this.getCategories();
  }

  //Metodo que revisa y actualiza los datos del usuario
  ngDoCheck(){
     this.loadUser();
  }

  //Metodo para cargar los datos del usuario
  loadUser(){
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
  }

  //Metodo para listar todas las categorias
  getCategories(){
    this._categoryService.getCategories().subscribe(
        response => {
          if (response.status == 'success') {
            this.categories = response.categories;
            
          }
        },
        error => {
          console.log(error);
        }
     );
  }
}
