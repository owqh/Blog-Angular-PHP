<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table ='posts';

   	//Arreglo para poder modificar multiples campos
   	protected $fillable = [
        'title', 'description', 'content', 'category_id', 'image'
    ];

    //Relacion de uno a muchos a la inversa (muchos a uno)
    public function user(){
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function category(){
    	return $this->belongsTo('App\Category', 'category_id');
    }
}
