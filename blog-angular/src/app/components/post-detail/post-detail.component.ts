import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { Post } from '../../models/post';
import { global } from '../../services/global';
import { PostService } from '../../services/post.service';
import { UserService } from '../../services/user.service';


@Component({
  selector: 'app-post-detail',
  templateUrl: './post-detail.component.html',
  styleUrls: ['./post-detail.component.css'],
  providers: [PostService, UserService]
})
export class PostDetailComponent implements OnInit {
	public post: Post;
  public identity;
  public url;
  public token;
  constructor(
  		private _postService: PostService,
      private _userService: UserService,
  		private _route: ActivatedRoute,
  		private _router: Router
  	) { 
      this.identity =this._userService.getIdentity();
      this.url =global.url;
      this.token = this._userService.getToken();
  }

  ngOnInit() {
   
  	this.getPost();
  }

  //Meotodo para obtener el post deseado
  getPost(){
  	//sacar el id del post de la url
  	this._route.params.subscribe(
  		params =>{
  			let id = +params['id'];
  			
  			//Peticion ajax para sacar los datos del post
  			this._postService.getPost(id).subscribe(
  				response => {
  					if (response.status == 'success') {
  						this.post = response.post;
  						
  					}
  					else{
  						this._router.navigate(['/inicio']);
  					}
  				}, 
  				error => {
  					this._router.navigate(['/inicio']);
  					console.log(<any>error);
  				}
  			);
  		}
  	);
  }

   deletePost(id){
    this._postService.delete(this.token, id).subscribe(
          response => {
            this._router.navigate(['/inicio']);
          }, 
          error  => {
            console.log(<any>error)
          }
      );
  }

}
