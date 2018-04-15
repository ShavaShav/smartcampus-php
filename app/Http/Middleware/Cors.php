<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Allows Cross-Origin requests
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // For testing, disable CORs middleware
        if (! env('APP_ENV', 'testing')) {

            header("Access-Control-Allow-Origin: *");

            $headers = [
                'Access-Control-Allow-Methods'=> 'POST, GET, PUT, DELETE',
                'Access-Control-Allow-Headers'=> 'Content-Type, Authorization, Origin'
            ];

            foreach($headers as $key => $value)
                $response->header($key, $value);
        }
        
        return $response;
    }
}
