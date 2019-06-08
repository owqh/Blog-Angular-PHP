import { Component, OnInit } from '@angular/core';
import { PostService } from '../../services/post.service';
import { UserService } from '../../services/user.service';
import { Post } from '../../models/post';
import { global } from '../../services/global';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  providers: [PostService, UserService]
})
export class HomeComponent implements OnInit {
	public url; 
	public posts: Array<Post>;
	public identity;
	public token;


  constructor(
  	private _postService: PostService,
  	private _userService: UserService
  	) { 
  	this.url =global.url;
  	this.identity = this._userService.getIdentity();
  	this.token = this._userService.getToken();
  }

  ngOnInit() {
  	this.getPosts();

  }

  //Metodo para obtener los post
  getPosts(){
  	this._postService.getPosts().subscribe(
  			response => {
          
  					if (response.status == 'success') {
  						this.posts = response.Posts;

  					}
  			},
  			error => {

  			}
  	);
  }

  deletePost(id){
    this._postService.delete(this.token, id).subscribe(
          response => {
            this.getPosts();
          }, 
          error  => {
            console.log(<any>error)
          }
      );
  }
}
