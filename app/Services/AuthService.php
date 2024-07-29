<?php

namespace App\Services;

use Validator;

//services section
use App\Services\ResponseService;

//models section
use App\Models\User;

//jwt section
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }
    
    public function login($request){        
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->responseService->response(false, "Błędy podczas walidacji", $validator->errors(), 400);
        }

        $loginField = filter_var($validator->validated()['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginField => $validator->validated()['login'],
            'password' => $validator->validated()['password'],
        ];

        if (!JWTAuth::attempt($credentials, false)) {
            return $this->responseService->response(false, "Brak autoryzacji z aplikacji!", [], 403);
        }

        $user = User::where($loginField, $validator->validated()['login'])->first();

        $customClaims = ['data' => [
            "email" => $user->email,
            "username" => $user->name
        ]];
        
        $token = JWTAuth::customClaims($customClaims)->fromSubject($user);
        return $this->responseService->response(true, "Pomyślnie zalogowano do aplikacji!", $this->tokenResponse($token), 200);
    }

    public function register($request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);
        if($validator->fails()){
            return $this->responseService->response(false, "Błędy podczas walidacji", $validator->errors(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return $this->responseService->response(true, "Udało się zarejestrować", [], 201);
    }

    public function logout($request){
        $token = $request->bearerToken(); // Pobierz token z nagłówka Authorization

        if (!$token) {
            return $this->responseService->response(false, "Nie podano tokena", [], 401);
        }
        try {
            JWTAuth::setToken($token)->invalidate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->responseService->response(false, "Token jest nieprawidłowy", [], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->responseService->response(false, "Nie można unieważnić tokena", [], 500);
        }
    
        return $this->responseService->response(true, "Użytkownik pomyślnie wylogowany", [], 200);
    }

    public function refresh(){
        try {
            $token = JWTAuth::parseToken()->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->responseService->response(false, "Token jest nieprawidłowy", [], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->responseService->response(false, "Token wygasł", [], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->responseService->response(false, "Nie można odświeżyć tokena", [], 500);
        }

        return $this->responseService->response(true, "Token został zaktualizowany", $this->tokenResponse($token), 200);
    }

    public function userProfile(){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return $this->responseService->response(true, "Token został zaktualizowany", $user, 200);
        } catch (JWTException $e) {
            return $this->responseService->response(false, "Nieautoryzowany", [], 403);
        }
    }

    protected function tokenResponse($access){
        return [
            'access_token' => $access,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL()
        ];
    }
}
