import { Component, OnInit, Input } from '@angular/core';

@Component({
  selector: 'post-list',
  templateUrl: './post-list.component.html',
  styleUrls: ['./post-list.component.css']
})
export class PostListComponent implements OnInit {

	@Input() posts;
	@Input() identity; 
	@Input() url;
	
  constructor() { }

  ngOnInit() {
  }

}
