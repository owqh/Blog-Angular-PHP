<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Comprobar si el usuario esta identificado

        //Recojer el token de la cabezera del sitio
        $token = $request->header('Authorization');
        $jwtAuth = new \App\Helpers\JwtAuth();
        $chekToken = $jwtAuth->chekToken($token);

        if ($chekToken) {

            return $next($request);
        }else{
            $data = array(
                    'status'    =>  'error', 
                    'code'      =>  400, 
                    'error'   =>  'El usuario no se ha podido identificar.'
                );
            return response()->json($data, $data['error']);
        }
       
    }
}
