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
        // Disable for testing environment
        if (\App::environment('testing')) {
            return $next($request);
        }

        header("Access-Control-Allow-Origin: *");

        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, Authorization, Origin'
        ];

        $response = $next($request);

        foreach($headers as $key => $value)
            $response->header($key, $value);
        
        return $response;
    }
}
