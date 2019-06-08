import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { CategoryService } from '../../services/category.service';
import { PostService } from '../../services/post.service';
import { Post } from '../../models/post';
import { global } from '../../services/global'; 

@Component({
  selector: 'app-post-new',
  templateUrl: './post-new.component.html',
  styleUrls: ['./post-new.component.css'],
  providers: [UserService, CategoryService, PostService]

})
export class PostNewComponent implements OnInit {

	public page_title: string;
	public identity;
	public token; 
	public post: Post;
	public categories;
  public status;

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
	      url: global.url+'post/upload',
	      headers: {
	        "Authorization" : this._userService.getToken()
	      }
	    },
	    theme: "attachPin",
	    hideProgressBar: false,
	    hideResetBtn: true,
	    hideSelectBtn: false,
	    attachPinText:'Sube la imagen del articulo'   
	  
	};

  constructor(

  	private _route: ActivatedRoute,
  	private _router: Router,
  	private _userService: UserService,
  	private _categoryService: CategoryService,
    private _postService: PostService

  	) {
  		this.page_title = "Crear una entrada";
  		this.identity = this._userService.getIdentity();
  		this.token = this._userService.getToken();
  }

  ngOnInit() {
  	this.getCategories();
  	this.post = new Post(1,this.identity.sub,1,'','','',null, null);
  	//console.log(this.post);
  }

  getCategories(){
  	this._categoryService.getCategories().subscribe(
  		response => {
  			if (response.status == 'success') {
  				this.categories = response.categories;

  			}
  		},
  		error => {
  			console.log(<any>error);
  		}
  	);
  }

  imageUpload(data){
  	 //Obteniendo los datos de la subida de imagen  
    let image_data = JSON.parse(data.response);
    this.post.image = image_data.image;
  }

  onSubmit(form){
      this._postService.create(this.token, this.post).subscribe(
          response => {
            if (response.status == 'success') {
              this.post = response.post;
              this.status ='success';
              this._router.navigate(['/inicio']);
            }else{
              this.status = 'error';
            }
          },
          error => {
            this.status ='error';
            console.log(<any>error);
          }
        );

    }
}
