<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        // $trusted_domains = ["http://localhost:4200", "http://10.0.0.143:4200", "http://member-orabella.digimahouse.com"];
        
        // if(isset($request->server()['HTTP_ORIGIN'])) 
        // {
        //     $origin = $request->server()['HTTP_ORIGIN'];

        //     if(in_array($origin, $trusted_domains)) 
        //     {
                // header('Access-Control-Allow-Origin: ' . $origin);
                // header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
                // header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
        //     }
        // }

        //commented 2/19/2019
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');

        return $response;
        //commented 2/19/2019

        // return $next($request)->header('Access-Control-Allow-Origin' , '*')
        //     ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH')
        //     ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
    }
}