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

        $headers = [
            'Access-Control-Allow-Origin'  =>  '*',
            'Access-Control-Allow-Methods' => 'POST, GET, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, Origin',
            'Access-Control-Expose-Headers' => 'Authorization'
        ];

        $response = $next($request);

        foreach($headers as $key => $value)
            $response->header($key, $value);
        
        return $response;
    }
}
