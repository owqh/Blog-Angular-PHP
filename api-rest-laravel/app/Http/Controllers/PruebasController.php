<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
   public function testOrm()
    {
    	/*$posts= Post::all();
    	foreach ($posts as $post) {
    		echo "<h1>".$post->title."</h1>";
    		echo "<span>{$post->user->name} - {$post->category->name} </span>";
    		echo "<p>".$post->content."</p>";
    		echo "<hr>";
    	}*/

    	$categories = Category::all();
    	foreach ($categories as $category) {
    		echo "<h1>{$category->name}</h1>";

    		foreach ($category->posts as $pos) {
    		echo "<h3>".$pos->title."</h3>";
    		echo "<span>{$pos->user->name} - {$pos->category->name} </span>";
    		echo "<p>".$pos->content."</p>";
    		
    	}
    	echo "<hr>";
    	}

    	die();
    }
}
