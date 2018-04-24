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

        $user = $this->user->create([
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password'])
        ]);

        if (!$user) {
            return response()->json(['error' => 'failed_to_create_new_user'], 500);
        }

        // Append token to User
        $user['token'] = $this->jwtauth->fromUser($user);

        return response()->json(compact('user'));
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
        $user = null;

        try {

            $token = $this->jwtauth->attempt($credentials);

            if ($token) {
                // Get user from token, append token
                $user = $this->jwtauth->toUser($token);
                $user['token'] = $token;
            } else {
                return response()->json(['error' => 'invalid_email_or_password'], 422);
            }

        } catch (JWTAuthException $e) {
            
            return response()->json(['error' => 'failed_to_create_token'], 500);
        }
        
        return response()->json(compact('user'));
    }

    public function logout(Request $request)
    {
        // Get token from header
        $token = $this->jwtauth->getToken();

        // Attempt to invalidate. User will need new token next time
        try {
            $this->jwtauth->invalidate($token);
            return response()->json(['message'=> 'logout_successful']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'failed_to_logout'], 500);
        }
    }

    public function show(Request $request)
    {
        // Get user from bearer token
        $token = $this->jwtauth->getToken();
        $user = $this->jwtauth->toUser($token);

        // Append token to User
        $user['token'] = (string) $token;
        
        return response()->json(compact('user'));
    }
}
