<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{  	
    public function __construct(){
    	//Llamando el middleware de autentidicacion para todas los metodos con excepcion de algunos metodos
		//$this->middleware('nombre del middleware', ['excepcion' => ['metodo_excluido1', 'metodo_excluido2']]);
		$this->middleware('api.auth', ['except' => ['index', 'show', 'getImage', 'getPostsByUser', 'getPostsByCategory']]); 
    }

    //Metodo para listar todos los post 
    public function index(){
        //Listando todos los post de la base de datos y agregando los datos de la categoria
    	$posts = Post::all()->load('category')
                            ->load('user');

    	return response()->json([
    		'code'			=> 	200,
    		'status'		=> 'success',
    		'Posts'	        =>	$posts
    	]);
    }

    //Metodo para listar un post en especifico
    public function show($id){

        $post = Post::find($id)->load('category')
                               ->load('user');

        if (is_object($post)) {
           $data = array(
            'code'          =>  200,
            'status'        => 'success',
            'post'          =>  $post
            );
        }else{
            $data = array(
            'code'          =>  400,
            'status'        => 'error',
            'message'       =>  'La entrada no existe'
            );
        }

        return response()->json($data, $data['code']);
    }
    
    //Metodo para crear entradas o post
    public function store(Request $request){
        //Recoger datos enviados 
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //Conseguir usuario identidicado
        $user = $this->getIdentity($request);


        if (!empty($params_array)) {
            //Conseguir usuario identidicado
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->chekToken($token, true);

            //Validar los datos
            $validate = \Validator::make($params_array, [
                'title'         => 'required',
                'description'   => 'required',
                'content'       => 'required',
                'category_id'   => 'required'
            ]);

            //comprobar que no hayan errores en la validacion
            if ($validate->fails()) {
               $data = array(
                'code'          =>  400,
                'status'        => 'error',
                'message'       =>  'No se ha guardado el post, Faltan datos.'
                );  
            }else{
                //Guardar el post
                $post = new Post();

                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->description = $params->description;
                $post->content = $params->content;
                $post->image = $params->image; 
                $post->save();

                $data = array(
                'code'          =>  200,
                'status'        => 'success',
                'post'          =>  $post
                );

            }
            

        }else{
             $data = array(
                'code'          =>  400,
                'status'        => 'error',
                'message'       =>  'Envie los datos correctamente.'
                );  
        }
        
        //Devolver una respuesta
         return response()->json($data, $data['code']); 
    }

    //Metodo para actualizar un post
    public function update($id, Request $request){
        //Recojer los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

             //Validar los datos
            $validate =\Validator::make($params_array, [
                'title'         => 'required',
                'description'   => 'required',
                'content'       => 'required',
                'category_id'   => 'required',
                'image'         => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'code'          =>  400,
                    'status'        => 'error',
                    'message'       =>  'Envie los datos correctamente.'
                    );  
            }else{
                 //Eliminar lo que no queremos actualizar
                    unset($params_array['id']);
                    unset($params_array['created_at']);
                    unset($params_array['user_id']);
                    unset($params_array['user']);

                //Conseguir usuario identidicado
                $user = $this->getIdentity($request);

                //Buscar el registro a modificar
                 $post = Post::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();

                if (!empty($post) && is_object($post)) {
                    $post->update($params_array);
                    //Devolver respuesta correcta
                    $data = array(
                    'code'          =>  200,
                    'status'        => 'success',
                    'post'          => $post,
                    'changes'       => $params_array
                    );
                }else{
                    $data = array(
                    'code'          =>  400,
                    'status'        => 'error',
                    'message'       => 'Datos incorrectos.'
                    );  
                }
                /* OTRO METODO PUEDE SER:

                   |
                   |Se crea una variable de nombre "where" para poner en esta todas las condiciones ya que que al utilizar
                   |el metodo updateOrCreate unicamente se puede utilizar una condicion, recordando que para poder hacer esto en el modelo 
                   |debe de estar un arreglo $fillable con los campos a modificar en la bd Ejemplo:
                   |protected $fillable = ['name', 'surname', 'description', 'email', 'password' ];
                   |
                

                //Actualizar el resgistro 
                $where = [
                    'id'        => $id,
                    'user_id'   => $user->sub
                ];
                    $post = Post::updateOrCreate($where, $params_array);
                    $data = array(
                    'code'          =>  200,
                    'status'        => 'success',
                    'post'          => $post,
                    'changes'       => $params_array
                    );*/
            }
        }else{
             $data = array(
                    'code'          =>  400,
                    'status'        => 'error',
                    'message'       => 'La cagamos',
                    'datos'         =>$params_array
                    );  
        }
       
       

        // Devolver una respuesta
        return response()->json($data, $data['code']); 
    }

    public function destroy($id, Request $request){
        //Conseguir usuario identidicado
       $user = $this->getIdentity($request);


        //Conseguir el post
        $post = Post::where('id', $id)
                        ->where('user_id', $user->sub)
                        ->first();

        if (!empty($post)) {
           //Borrar el registro
            $post->delete();
            //devolver algo
            $data = array(
                    'code'          =>  200,
                    'status'        => 'success',
                    'post'          => $post
                    );

        }else{
            $data = array(
                    'code'          =>  400,
                    'status'        => 'error',
                    'message'       => 'El post no existe.'
                    );  
        }

        

          return response()->json($data, $data['code']); 
    }

    //Conseguir usuario identidicado
    private function getIdentity($request){
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->chekToken($token, true);

        return $user;
    }

    //Funcion para subir una imagen
    public function upload(Request $request){
        //Recoger datos de la peticion
        $image = $request->file('file0');

        //Validacion que sea una imagen
        $validate = \Validator::make($request->all(), [
            'file0'     =>  'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        //Subir y guardar la imagen
        if (!$image || $validate->fails()) {
            $data = array(
            'status'    =>  'error', 
            'code'      =>  400, 
            'message'   =>  'Error al subir imagen.'
        );  

        }else{
            //Creando el nombre unico de la imagen 
            $image_name = time().$image->getClientOriginalName();
            //Guaradando la imagen
            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = array(
            'code'      =>  200,
            'status'    =>  'success',
            'image'     =>  $image_name
        );
        }       
        //Devolver datos
        return response()->json($data, $data['code']);
    }

    //Funcion para obtener una imagen
    public function getImage($filename){
        //Comprobar si existe el fichero
        $isset = \Storage::disk('images')->exists($filename);

        if ($isset) {
            //Conseguir la imagen
            $file = \Storage::disk('images')->get($filename);

            //Devolver la imagen 
            return new Response($file, 200);
        }else{
             $data = array(
            'status'    =>  'error', 
            'code'      =>  400, 
            'message'   =>  'No se encuentra la imagen'
            );  
        }

        return response()->json($data, $data['code']);
        
    }

    //funcion para obtener los post por categoria
    public function getPostsByCategory($id){
        //Consulta a la BD 
        $posts = Post::where('category_id', $id)->get();

        //Respuesta de la consulta
        return response()->json([
            'status'    => 'success',
            'posts'     => $posts
        ] , 200);
    }

    //Funcion para obterner los post por usuario
    public function getPostsByUser($id){
        //Consulta a la BD 
        $posts = Post::where('user_id', $id)->get();

        //Respuesta de la consulta
        return response()->json([
            'status'    => 'success',
            'posts'     => $posts
        ] , 200);
    }

}
         
