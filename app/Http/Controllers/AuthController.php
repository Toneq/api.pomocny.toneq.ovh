<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    protected $eventService;

    public function __construct(AuthService $authService){
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authService = $authService;
    }

    public function login(Request $request){
        return $this->authService->login($request);
    }

    public function register(Request $request){
        return $this->authService->register($request);
    }

    public function logout(Request $request){
        return $this->authService->logout($request);
    }

    public function refresh(){
        return $this->authService->refresh();
    }

    public function user_profile(Request $request){
        return $this->authService->userProfile($request);
    }
}
