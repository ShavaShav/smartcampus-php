<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWT
{
    /**
     * Authenticates JWT, and refreshes tokens if expired
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        JWTAuth::setRequest($request);

        if (! $token = JWTAuth::getToken()) {
            return response()->json(['error' => 'token_not_provided'], 400);
        }

        try {
            if (! $user = JWTAuth::authenticate($token) ) {
                return response()->json(['error' => 'user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            // If the token is expired, then it will be refreshed and added to the headers
            try {
                $refreshed = JWTAuth::refresh($token);
                $user = JWTAuth::setToken($refreshed)->toUser();
                header('Authorization: Bearer ' . $refreshed);
            } 
            catch (TokenExpiredException $e) {
                return response()->json(['error' => 'token_expired'], $e->getStatusCode());
            }
            catch (JWTException $e) {
                return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        }

        // Login the user instance for global usage
        Auth::login($user, false);

        return  $next($request);
    }
}
