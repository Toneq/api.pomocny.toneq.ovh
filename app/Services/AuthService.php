<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    public function login($request){        
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $loginField = filter_var($validator->validated()['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginField => $validator->validated()['login'],
            'password' => $validator->validated()['password'],
        ];

        if ($x = !JWTAuth::attempt($credentials, false)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where($loginField, $validator->validated()['login'])->first();

        $customClaims = ['data' => [
            "email" => $user->email,
            "username" => $user->name
        ]];
        
        $token = JWTAuth::customClaims($customClaims)->fromSubject($user);
        $refreshToken = JWTAuth::setToken($token)->refresh();
        return $this->createNewToken($token, $refreshToken);
    }

    public function register($request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'User successfully registered'
            // 'user' => $user
        ], 201);
    }

    public function logout($request){
        $token = $request->bearerToken(); // Pobierz token z nagłówka Authorization

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
    
        try {
            JWTAuth::setToken($token)->invalidate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not invalidate token'], 500);
        }
    
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh(){
        try {
            $token = JWTAuth::parseToken()->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }

        $refreshToken = JWTAuth::setToken($token)->refresh();
        return $this->createNewToken($token, $refreshToken);
    }

    public function userProfile(){
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return response()->json(['user' => $user]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    protected function createNewToken($access, $refresh){
        return response()->json([
            'access_token' => $access,
            'refresh_token' => $refresh,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL()
        ]);
    }
}
