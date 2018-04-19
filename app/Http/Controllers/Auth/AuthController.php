<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\JWTAuthException;

class AuthController extends Controller
{
    private $user;
    private $jwtauth;
    
    public function __construct(User $user, JWTAuth $jwtauth)
    {
        $this->user = $user;
        $this->jwtauth = $jwtauth;
    }

    public function register(RegisterRequest $request)
    {
        $userData = $request->get('user');

        $newUser = $this->user->create([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password'])
        ]);

        if (!$newUser) {
            return response()->json(['failed_to_create_new_user'], 500);
        }

        return response()->json([
            'token' => $this->jwtauth->fromUser($newUser)
        ]);
    }

    public function login(LoginRequest $request)
    {
        $userData = $request->get('user');

        // get user credentials: email, password
        $credentials = [ 
            'email' => $userData['email'],
            'password' => $userData['password']
        ];
        
        $token = null;

        try {

            $token = $this->jwtauth->attempt($credentials);

            if (!$token) {
                return response()->json(['invalid_email_or_password'], 422);
            }

        } catch (JWTAuthException $e) {
            
            return response()->json(['failed_to_create_token'], 500);
        }
        
        return response()->json(compact('token'));
    }
}
